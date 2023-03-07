<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\Exception;
use yii\helpers\Html;

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
 * @property string|null $paypal_order_id
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property OrderAddress[] $orderAddresses
 * @property OrderItem[] $ordersItems
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PAID = 1;
    const STATUS_FAILED = 2;
    const STATUS_COMPLETED = 10;
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
            [['firstname', 'lastname', 'email', 'transaction_id','paypal_order_id'], 'string', 'max' => 255],
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
            'paypal_order_id' => 'Paypal Order ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[OrderAddresses]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderAddressQuery
     */
    public function getOrderAddress()
    {
        return $this->hasMany(OrderAddress::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemQuery
     */
    public function getOrderItems()
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

    public function saveOrderItems()
    {
        $cartItems = CartItem::getItemsForUser(currUserId());
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $this->id;
            $orderItem->quantity = $cartItem['quantity'];
            if (!$orderItem->save()) {
                throw new Exception("Order item was not saved: " . implode('<br>', $orderItem->getFirstErrors()));
            }
        }
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
    public function saveOrderAddress($postData): bool
    {
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $this->id;
        if($orderAddress->load($postData) && $orderAddress->save()){
            return true;
        }
        return false;
    }

    // get Full Name
    public function getFullName()
    {
        $fullName = $this->firstname.' '.$this->lastname;
        return $fullName ?? 'Unknown User';
    }

    // Html label for Order status
    public function getStatusLabel($status)
    {
        switch ($status){
            case Order::STATUS_DRAFT:
                $label = Html::tag('span','Draft',['class'=>'badge badge-secondary']);
            break;
            case Order::STATUS_FAILED:
                $label = Html::tag('span','Failed',['class'=>'badge badge-danger']);
            break;
            case Order::STATUS_PAID:
                $label = Html::tag('span','Paid',['class'=>'badge badge-primary']);
            break;
            case Order::STATUS_COMPLETED:
                $label = Html::tag('span','Completed',['class'=>'badge badge-success']);
            break;
            default: $label ='Error';
        }
        return $label;
    }


    /**
     *
     *  Get Items Quantity
     * */
    public function getItemsQuantity()
    {
        return $sum = CartItem::findBySql(
            "SELECT SUM(quantity) FROM orders_items WHERE order_id = :orderId", ['orderId' => $this->id]
        )->scalar();
    }

    public function sendEmailToVendor()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'order_completed_vendor-html', 'text' => 'order_completed_vendor-text'],
                ['order' => $this]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo(Yii::$app->params['vendorEmail'])
            ->setSubject('New Order has been made ' . Yii::$app->name)
            ->send();
    }

    public function sendEmailToCustomer()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'order_completed_customer-html', 'text' => 'order_completed_customer-text'],
                ['order' => $this]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Your order confirmed at ' . Yii::$app->name)
            ->send();
    }

}
