<?php

namespace components;

use yii\base\Application;

class SetLanguages extends \yii\base\Behavior
{

    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'change-lang'
        ];
    }


    public function changeLang()
    {
        if (\Yii::$app->session->has('language')){
            \Yii::$app->language = \Yii::$app->session->get('language');
        }
    }
}