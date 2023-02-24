<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\Order;
use common\models\OrderItem;
use common\models\User;
use PhpParser\Node\Expr\Array_;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login','forgot-password', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $totalEarings = Order::find()->paid();
        $productsSold = OrderItem::find()->soldProduct();
        $orderMade = Order::find()->orderCount();
        $totalUser = User::find()->count();
        $orders = Order::findBySql("
            SELECT
                CAST(DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m-%d') AS DATE) as `date`,
                SUM(total_price) as total_price
            FROM orders
            WHERE status = :status
            GROUP BY CAST(DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m-%d') AS DATE)
            ORDER BY created_at
        ",['status'=>Order::STATUS_COMPLETED])
            ->asArray()
            ->all();
       // Line Chart
       $earningsData = [];
       $labels = [];
       if(!empty($orders)){
           $minDate = $orders[0]['date'];
           $d = new \DateTime($minDate);
           $nowDate = new \DateTime();
           $orderByPriceMap = ArrayHelper::map($orders,'date','total_price');
           while($d->getTimestamp() < $nowDate->getTimestamp()){
               $labels[] = $d->format('d/m/Y');
               $d->setTimestamp($d->getTimestamp() + 86400);
               $earningsData[] = (float)($orderByPriceMap[$d->format('Y-m-d')] ?? 0);
           }
       }
       // Pie Chart
       $countriesData = Order::findBySql("
            SELECT 
                oa.country as country,SUM(orders.total_price) as total_price
            FROM orders 
            INNER JOIN order_addresses oa on orders.id = oa.order_id
            WHERE orders.status = :status
            GROUP BY country
       ",['status'=>Order::STATUS_COMPLETED])
           ->asArray()
           ->all();
       $countries = ArrayHelper::getColumn($countriesData,'country');

//       $bgColors = [];
//        foreach ($countries as $country) {
//            $color = "rgb(".rand(0,255).", ".rand(0,255).", ".rand(0,255).")";
//            $bgColors[] =$color;
//       }
        $bgColors = [];
        $colors = ['#4e73df', '#1cc88a', '#36b9cc'];
        foreach ($countries as $i => $country) {
            $bgColors[] = $colors[$i % count($countries)];
        }
       $countriesPriceData = ArrayHelper::getColumn($countriesData,'total_price');

        return $this->render('index',[
            'earings'=>$totalEarings,
            'products'=>$productsSold,
            'orderMade'=>$orderMade,
            'totalUser'=>$totalUser,
            'data'=>$earningsData,
            'labels'=>$labels,
            'bgColors'=>$bgColors,
            'countriesData'=>$countriesPriceData,
            'countries'=>$countries
        ]);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    public function actionForgotPassword(){

        return "Forgot password";
    }
}
