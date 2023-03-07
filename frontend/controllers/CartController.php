<?php

namespace frontend\controllers;


use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsGetRequest;
use Yii;
use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use common\models\CartItem;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *   CartController use cart functionality
 */
class CartController extends Controller
{
    public function behaviors(): array
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add', 'change-quantity','submit-payment'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ]
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'create-order'=>['post']
                ]
            ]
        ];
    }

    /**
     *   Deploy products that user add to cart
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): string
    {
        return $this->render('index', [
            'items' => CartItem::getItemsForUser(currUserId())
        ]);
    }

    /**
     *   click 'add' button add product to cart basket
     * @throws NotFoundHttpException
     */
    public function actionAdd(): array
    {
        $productId = Yii::$app->request->post('id');
        return Product::addProductToCart($productId);
    }

    public function actionDelete($id): Response
    {
        CartItem::removeCartItemForUser($id);
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionChangeQuantity(): array
    {
        $productId = Yii::$app->request->post('id');
        $productQuantity = Yii::$app->request->post('value');
        return CartItem::changeTotalQuantity($productId,$productQuantity);
    }

    /**
     *   Action checkout
     */
    public function actionCheckout(): string
    {

        $cartItems = CartItem::getItemsForUser(currUserId());
        $productQuantity = CartItem::getTotalQuantity(currUserId());
        $totalPrice = CartItem::getTotalPrice(currUserId());

        if (empty($cartItems)) {
            return $this->redirect(['/site/index']);
        }
        $order = new Order();
        $order->total_price = $totalPrice;
        $order->status = Order::STATUS_DRAFT;
        $order->created_at = time();
        $order->created_by = currUserId();
        $transaction = Yii::$app->db->beginTransaction();
        if ($order->load(Yii::$app->request->post())
            && $order->save()
            && $order->saveOrderAddress(Yii::$app->request->post())
            && $order->saveOrderItems()) {
            $transaction->commit();

            CartItem::clearCartItems(currUserId());

            return $this->render('pay-now', [
                'order' => $order,
            ]);
        }

        $orderAddress = new OrderAddress();
        if (!isGuest()) {
            /** @var \common\models\User $user */
            $user = Yii::$app->user->identity;
            $userAddress = $user->getAddress();

            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;
            $order->status = Order::STATUS_DRAFT;

            $orderAddress->address = $userAddress->address;
            $orderAddress->city = $userAddress->city;
            $orderAddress->state = $userAddress->state;
            $orderAddress->country = $userAddress->country;
            $orderAddress->zipcode = $userAddress->zipcode;
        }

        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }

    /**
     *  Check Transaction Id 
     * 
     */
    public function actionSubmitPayment($orderId){
        $where = ['id'=>$orderId,'status'=>Order::STATUS_DRAFT];
        if(!isGuest()){
            $where['created_by'] = currUserId();
        }
        $order = Order::findOne($where);
        if(!$order){
            throw new NotFoundHttpException("Order not found");
        }
        $paypalOrderId = Yii::$app->request->post('paypalOrderId');
        $exist = Order::find()->andWhere(['paypal_order_id'=>$paypalOrderId])->exists();

        if($exist){
            throw new BadRequestHttpException();
        }
        // Creating an environment
        $clientId = Yii::$app->params['paypalClientId'];
        $clientSecret = Yii::$app->params['paypalSecretId'];

        $environment = new SandboxEnvironment($clientId, $clientSecret);
        $client = new PayPalHttpClient($environment);
        $response = $client->execute(new OrdersGetRequest($paypalOrderId));

        if ($response->statusCode === 200) {
            $order->paypal_order_id = $paypalOrderId;
            $paidAmount = 0;
            foreach ($response->result->purchase_units as $purchase_unit) {
                if ($purchase_unit->amount->currency_code === 'USD') {
                    $paidAmount += $purchase_unit->amount->value;
                }
            }
            if ($paidAmount === (float)$order->total_price && $response->result->status === 'COMPLETED') {
                $order->status = Order::STATUS_PAID;
            }
            $order->transaction_id = $response->result->purchase_units[0]->payments->captures[0]->id;
            if  ($order->save()) {
                if (!$order->sendEmailToVendor()) {
                    Yii::error("Email to the vendor is not sent");
                }
                if (!$order->sendEmailToCustomer()) {
                    Yii::error("Email to the customer is not sent");
                }

                return [
                    'success' => true
                ];
            } else {
                Yii::error("Order was not saved. Data: ".VarDumper::dumpAsString($order->toArray()).
                    '. Errors: '.VarDumper::dumpAsString($order->errors));
            }
        }

        throw new BadRequestHttpException();
    }
}