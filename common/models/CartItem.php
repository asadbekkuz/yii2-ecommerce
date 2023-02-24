<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "cart_item".
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int $user_id
 *
 * @property Product $product
 * @property User $user
 */
class CartItem extends \yii\db\ActiveRecord
{
    const SESSION_KEY = 'CART_ITEM';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart_item';
    }

    public static function getTotalQuantity(?int $currUserId)
    {
        $sum = 0;
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as &$cartItem) {
                $sum += $cartItem['quantity'];
            }
        }
        if (!isGuest()) {
            $sum = CartItem::findBySql("
                SELECT SUM(quantity) 
                FROM cart_item 
                WHERE user_id = :user_id", ['user_id' => $currUserId])
                ->scalar();
        }
        return $sum;
    }

    public static function getTotalPrice(?int $currUserId)
    {
        $sum = 0;
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as &$cartItem) {
                $sum += $cartItem['quantity'] * $cartItem['price'];
            }
        }
        if (!isGuest()) {
            $sql = "SELECT SUM(c.quantity * p.price) 
                FROM cart_item c 
                LEFT JOIN products p on p.id = c.product_id
                WHERE c.user_id = :user_id";
            $sum = CartItem::findBySql($sql, ['user_id' => $currUserId])->scalar();
        }
        return $sum;
    }

    public static function changeQuantity($quantity,$id) : bool
    {
        $cartItem = CartItem::find()->andWhere(['product_id'=>$id,'user_id'=>currUserId()])->one();
        $cartItem->quantity = $quantity;
        if(!$cartItem->save())
            return false;
        return true;
    }

    public static function getItemsForUser(?int $currUserId)
    {
        $cartItem = [];
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
        return $cartItem;
    }

    public static function getProductForUser($productId)
    {
        return self::find()->userId(currUserId())->productId($productId)->one();
    }

    public static function removeCartItemForUser($id)
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
    }

    public static function changeTotalQuantity($productId, $productQuantity)
    {
        $productItem = Product::getProduct($productId);
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
            $data = CartItem::changeQuantity($productQuantity,$productId);
            if ($data) {
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'user_id'], 'required'],
            [['product_id', 'quantity', 'user_id'], 'integer'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProductQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CartItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CartItemQuery(get_called_class());
    }

}
