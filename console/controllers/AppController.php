<?php

namespace console\controllers;

use common\models\User;
use yii\helpers\Console;
use yii\console\Controller;

class AppController extends Controller
{

    /**
     *  Create admin through console
     * @param $username
     * @param $password
     * @return void
     */
    public function actionCreateAdminUser($username, $password = null)
    {
        \console\models\User::createAdminUser($username,$password);
    }
}