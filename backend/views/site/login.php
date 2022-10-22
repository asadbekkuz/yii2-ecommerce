<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>
<!-- Nested Row within Card Body -->
<div class="row justify-content-center">

    <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                    <div class="col-lg-6" style="height: 510px;">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                            </div>
                            <?php $form = ActiveForm::begin([
                                'options'=>[
                                        'class'=>'user'
                                ],
                            ]); ?>
                                <div class="form-group">

                                    <?= $form->field($model, 'username')->textInput([
                                            'autofocus' => true,
                                            'class'=>'form-control form-control-user',
                                            'placeholder'=>'Enter Email Address...'
                                    ])->label(false) ?>

                                </div>
                                <div class="form-group">

                                    <?= $form->field($model, 'password')->passwordInput([
                                        'class'=>'form-control form-control-user',
                                        'placeholder'=>'Password'
                                    ])->label(false) ?>

                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember
                                            Me</label>
                                    </div>
                                </div>
                                <?= Html::submitButton('Login', [
                                        'class' => 'btn btn-primary btn-user btn-block',
                                        'name' => 'login-button'
                                ]) ?>

                               
                            <?php ActiveForm::end() ?>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?php echo \yii\helpers\Url::to(['/site/forgot-password']) ?>">Forgot Password?</a>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
