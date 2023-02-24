<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property float $total_price
 * @property int|null $status
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string|null $transaction_id
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property OrderAddress[] $orderAddresses
 * @property OrderItem[] $ordersItems
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_FAILURED = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price', 'firstname', 'lastname', 'email'], 'required'],
            [['total_price'], 'number'],
            [['status', 'created_at', 'created_by'], 'integer'],
            [['firstname', 'lastname', 'email', 'transaction_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'transaction_id' => 'Transaction ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[OrderAddresses]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderAddressQuery
     */
    public function getOrderAddresses()
    {
        return $this->hasMany(OrderAddress::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemQuery
     */
    public function getOrdersItems()
    {
        return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
    }

    public function saveOrderItems($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $cartItems = CartItem::getItemsForUser(currUserId());
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $id;
            $orderItem->quantity = $cartItem['quantity'];
            if(!$orderItem->save()){
                throw new Exception('Order items was not save:'.implode('<br/>',$orderItem->getFirstErrors()));
            }
        }
        $transaction->commit();
        return true;
    }

    public function saveOrder($postData,$totalPrice): bool
    {
        $order = new Order();
        $orderAddress = new OrderAddress();
        $order->total_price = $totalPrice;
        $order->status = Order::STATUS_DRAFT;
        $order->created_at = time();
        if($order->load($postData) && $order->save()){
            $order->refresh();
            if($order->saveOrderAddress($postData,$order->id) && $order->saveOrderItems($order->id)){
                return true;
            }
        }
        return false;
    }
    public function saveOrderAddress($postData,$id): bool
    {
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $id;
        if($orderAddress->load($postData) && $orderAddress->save()){
            return true;
        }
        return false;
    }

}
