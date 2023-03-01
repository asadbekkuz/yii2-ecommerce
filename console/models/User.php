<?php

namespace console\models;


use Yii;
use yii\helpers\Console;

class User  extends \common\models\User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname','lastname'],'required'],
            [['firstname','lastname','username','email'],'string','max'=>255],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            [['admin'],'default','value' => 0,'on'=>'login'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],

            ['username','required'],
            [['username'],'unique', 'targetClass' => '\common\models\User','message' => 'This username address has already been taken.'],

            ['email','required'],
            [['email'], 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
        ];
    }
    /**
     * Create admin user with console  
     * @param $username
     * @param $password
     * @return void
     */
    public static function createAdminUser($user,$username,$password=null)
    {
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