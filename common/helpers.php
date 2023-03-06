<?php
/**
*   Helpers php file in the project
*/


/**
*   True or false
*/
function isGuest() : bool
{
    return Yii::$app->user->isGuest;
}

/**
*   Get current User id
*/

function currUserId() : ?int
{
    return Yii::$app->user->id;
}

/**
 * Get data from params
*/
function param($key){
    if(Yii::$app->params[$key]){
        return Yii::$app->params[$key];
    }
    throw new \yii\web\NotFoundHttpException("Params not found is given key");
}