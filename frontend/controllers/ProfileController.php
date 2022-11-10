<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class ProfileController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access'=>[
                'class'=>AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index','update-address','update-account'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
    *   profile page
    */
    public function actionIndex() : string
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $userAddress = $user->getAddress();
        return $this->render('index',[
            'user'=>$user,
            'userAddress'=>$userAddress
        ]);
    }
    /**
    *   Update User Address Information
    */
    public function actionUpdateAddress() : string
    {
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Your are only allowed to make ajax request');
        }
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $userAddress = $user->getAddress();
        $success = false;
        if($userAddress->load(Yii::$app->request->post()) && $userAddress->save()){
            $success = true;
        }
        return $this->renderAjax('update_address',[
            'userAddress'=>$userAddress,
            'success'=>$success
        ]);
    }

    /**
    *   Update User Account Information
    */
    public function actionUpdateAccount() : string
    {
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Your are only allowed to make ajax request');
        }
        /** @var User $user */
        $user = Yii::$app->user->identity;
        $success = false;
        if($user->load(Yii::$app->request->post()) && $user->save()){
            $success = true;
        }
        return $this->renderAjax('update_account',[
            'user'=>$user,
            'success'=>$success
        ]);
    }
}