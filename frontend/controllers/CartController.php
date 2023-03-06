<?php

namespace frontend\controllers;


use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use common\models\User;
use Yii;
use common\models\CartItem;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
use yii\filters\VerbFilter;
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
                'only' => ['add', 'change-quantity'],
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
             return   $this->redirect([Yii::$app->homeUrl]);
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

//            CartItem::clearCartItems(currUserId());

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
       *  create order and save User address and information, or update
      */
//    public function actionCreateOrder()
//    {
//        $order = new Order();
//        $orderAddress = new OrderAddress();
//        $postData = Yii::$app->request->post();
//        $totalPrice = CartItem::getTotalPrice(currUserId());
//        $productQuantity = CartItem::getTotalQuantity(currUserId());
//
//        if($order->load($postData)
//            && $order->saveOrder($postData,$totalPrice)){
//
//            return $this->render('submit-payment',[
//                'order' => $order,
//                'orderAddress' =>$orderAddress,
//                'productQuantity' => $productQuantity,
//                'totalPrice' => $totalPrice
//            ]);
//        }
//        throw new BadRequestHttpException();
//    }

}