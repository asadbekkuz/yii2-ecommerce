<?php

namespace common\models;

use Faker\Core\File;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $imageFile
 * @property float $price
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property CartItem[] $cartItems
 * @property OrderItem[] $ordersItems
 */
class Product extends ActiveRecord
{
    /**
     * @var $imageFile UploadedFile;
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    public static function getProduct($productId)
    {
        $productItem = Product::find()->id($productId)->published()->one();
        if (!$productItem) {
            throw new NotFoundHttpException('Product don\'t exsist');
        }
        return $productItem;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['imageFile'], 'image', 'extensions' => 'png,jpg,jpeg', 'maxSize' => 5 * 1024 * 1024],
            [['price'], 'number'],
            [['description'], 'string'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Product Image',
            'imageFile' => 'Product Image',
            'price' => 'Price',
            'status' => 'Published',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CartItemQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(CartItem::className(), ['product_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemQuery
     */
    public function getOrdersItems()
    {
        return $this->hasMany(OrderItem::className(), ['product_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProductQuery(get_called_class());
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function save($runValidation = true, $attributeNames = null)
    {   
        /*
         * Remove image and directory, if $model is not is newRecord and imageFile is empty
         *
         *  */
        if(!$this->isNewRecord && $this->imageFile !== null){
            $this->removeImageDirectory($this->image);
        }

        if ($this->imageFile) {
            $this->image = '/product/' . Yii::$app->security->generateRandomString() . '/' . $this->imageFile->name;
        }
        $saveModel = parent::save($runValidation, $attributeNames);
        $transaction = Yii::$app->db->beginTransaction();
        if ($saveModel && $this->imageFile) {
            $fullPath = Yii::getAlias('@frontend/web/storage') . $this->image;
            $path = dirname($fullPath);
            if (!FileHelper::createDirectory($path) || !$this->imageFile->saveAs($fullPath)) {
                $transaction->rollBack();
            }
        }
        $transaction->commit();
        return $saveModel;
    }

    public static function getImgUrl($image) : string
    {
        return self::formatImageUrl($image);
    }

    public static function formatImageUrl($image) : string
    {
        if ($image) {
            return  Yii::$app->params['imagePath'].$image;
        }
        return Yii::$app->params['imagePath'].'/img/no-image.png';
    }

    public function getStatus(?int $status)
    {
        return Html::tag('span', $status === 1 ? 'Active' : 'Draft', [
            'class' => $status === 1 ? 'badge badge-success' : 'badge badge-danger']);
    }

    /**
     *  Get the Description without tags and white spaces
     */
    public function getShortDescription()
    {
        return StringHelper::truncateWords(strip_tags($this->description), 20);
    }

    /**
     *  Get Currency price
     */
    public function getCurrencyPrice(): string
    {
        return Yii::$app->formatter->asCurrency($this->price);
    }

    public static function addProductToCart($productId)
    {
        $productItem = Product::getProduct($productId);
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
            $cartItem = CartItem::getProductForUser($productId);
            if ($cartItem) {
                $cartItem->quantity++;
            }
            if (!$cartItem) {
                $cartItem = new CartItem();
                $cartItem->user_id = currUserId();
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


    /*
     * remove image directory and image file
     *
     * */
    public function removeImageDirectory($directory): void
    {
        $fullPath = Yii::getAlias('@frontend/web/storage').$directory;
        $subString = substr($fullPath,0,strripos($fullPath,'/'));
        FileHelper::removeDirectory($subString);
    }
}
