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
                    'delete' => ['post']
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
        if (isGuest()) {
            $cartItem = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        }
        if (!isGuest()) {
            $cartItem = CartItem::findBySql("
               SELECT
                    c.product_id as id,
                    p.name,
                    p.image,
                    p.price,
                    c.quantity,
                    c.user_id as client,
                    p.price * c.quantity as total_price
               FROM cart_item c 
                   LEFT JOIN products p on p.id = c.product_id
               WHERE c.user_id = :userId 
            ", ['userId' => currUserId()])
                ->asArray()
                ->all();
        }
        return $this->render('index', [
            'items' => $cartItem
        ]);
    }

    /**
     *   click 'add' button add product to cart basket
     * @throws NotFoundHttpException
     */
    public function actionAdd(): array
    {
        $userId = currUserId();
        $productId = Yii::$app->request->post('id');
        $productItem = Product::find()->id($productId)->published()->one();
        if (!$productItem) {
            throw new NotFoundHttpException('Product don\'t exsist');
        }
        if (isGuest()) {
            $cartItems = \Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $flag = false;
            foreach ($cartItems as &$item) {
                if ($item['id'] == $productId) {
                    $item['quantity']++;
                    $flag = true;
                }
            }
            if (!$flag) {
                $cartItem = [
                    'id' => $productId,
                    'name' => $productItem->name,
                    'image' => $productItem->image,
                    'price' => $productItem->price,
                    'quantity' => 1,
                    'total_price' => $productItem->price,
                ];
                $cartItems[] = $cartItem;
            }
            \Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        }
        if (!isGuest()) {
            $cartItem = CartItem::find()->userId($userId)->productId($productId)->one();
            if ($cartItem) {
                $cartItem->quantity++;
            }
            if (!$cartItem) {
                $cartItem = new CartItem();
                $cartItem->user_id = $userId;
                $cartItem->product_id = $productId;
                $cartItem->quantity = 1;
            }
            if ($cartItem->save()) {
                return [
                    'success' => true,
                ];
            }
            return [
                'success' => false,
                'errors' => $cartItem->errors
            ];
        }
        return [
            'success' => true
        ];
    }

    public function actionDelete($id): Response
    {
        if (isGuest()) {
            $cartItem = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItem as $key => $item) {
                if ($item['id'] == $id) {
                    array_splice($cartItem, $key, 1);
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItem);
        }
        if (!isGuest()) {
            $cartItem = CartItem::deleteAll(['user_id' => currUserId(), 'product_id' => $id]);
        }
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionChangeQuantity()
    {
        $success = false;
        $userId = currUserId();
        $productId = Yii::$app->request->post('id');
        $productQuantity = Yii::$app->request->post('value');
        $productItem = Product::find()->id($productId)->published()->one();
        if (!$productItem) {
            throw new NotFoundHttpException('Product does not exsist');
        }
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as &$cartItem) {
                if ($cartItem['id'] == $productId) {
                    $cartItem['quantity'] = $productQuantity;
                    $cartItem['total_price'] = $cartItem['price'] * $productQuantity;
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        }
        if (!isGuest()) {
            $productItem['quantity'] = $productQuantity;
            if ($productItem->save()) {
                return [
                    'success' => true,
                    'quantity' => CartItem::getTotalQuantity(currUserId())
                ];
            }

            return [
                'success' => false,
                'errors' => $productItem->errors
            ];

        }
        return [
            'success' => true,
            'quantity' => CartItem::getTotalQuantity(currUserId())
        ];
    }

    /**
     *   Action checkout
     */
    public function actionCheckout()
    {

        $order = new Order();
        $orderAddress = new OrderAddress();
        $productQuantity = CartItem::getTotalQuantity(currUserId());
        $totalPrice = CartItem::getTotalPrice(currUserId());

        if (!isGuest()) {
            /** @var User $user */
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
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }
}