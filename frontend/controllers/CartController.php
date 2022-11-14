<?php

namespace frontend\controllers;


use common\models\Product;
use Yii;
use common\models\CartItem;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *   CartController use cart functionality
 */
class CartController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON
                ]
            ]
        ];
    }

    /**
     *   Deploy products that user add to cart
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            //todo session
        } else {
            $cartItem = CartItem::findBySql("
               SELECT
                    p.name,
                    p.image,
                    p.price,
                    c.quantity,
                    c.user_id as client,
                    p.price * c.quantity as total_price
               FROM cart_item c 
                   LEFT JOIN products p on p.id = c.product_id
               WHERE c.user_id = :userId 
            ", ['userId' => Yii::$app->user->id])
                ->asArray()
                ->all();
        }
        return $this->render('index', [
            'item' => $cartItem
        ]);
    }

    /**
     *   click 'add' button add product to cart basket
     */
    public function actionAdd(): array
    {
        $userId = Yii::$app->user->id;
        $productId = Yii::$app->request->post('id');
        $productItem = Product::find()->id($productId)->published()->one();
        if (!$productItem) {
            throw new NotFoundHttpException('Product does not exsist');
        }

        if (\Yii::$app->user->isGuest) {
            return [
                'success' => false
            ];
        } else {
            $cartItem = CartItem::find()->userId($userId)->productId($productId)->one();
            if ($cartItem) {
                $cartItem->quantity++;
            } else {
                $cartItem = new CartItem();
                $cartItem->user_id = $userId;
                $cartItem->product_id = $productId;
                $cartItem->quantity = 1;
            }
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
}