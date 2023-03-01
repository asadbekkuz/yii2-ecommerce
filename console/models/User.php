<?php

namespace console\models;


use Yii;
use yii\helpers\Console;

class User  extends \common\models\User {

    /**
     * Create admin user with console  
     * @param $username
     * @param $password
     * @return void
     */
    public static function createAdminUser($username,$password=null)
    {
        $user = new \common\models\User();
        $user->firstname = $username;
        $user->lastname = $username;
        $user->username = $username;
        $user->email = $username.'@example.com';
        $user->status = 10;
        $user->admin = 1;
        $password = $password ?: \Yii::$app->security->generateRandomString(8);
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        if ($user->save()) {
            Console::output("Create admin user");
            Console::output("Username:".$username);
            Console::output("Password:".$password);
        } else{
            Console::error("Error:");
            var_dump($user->errors);
        }    
    }

}