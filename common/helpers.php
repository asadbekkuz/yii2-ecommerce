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