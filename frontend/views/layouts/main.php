<?php

/** @var \yii\web\View $this */

/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

$cartItems = $this->params['cartItem'];
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.0/css/all.min.css" integrity="sha512-3PN6gfRNZEX4YFyz+sIyTF6pGlQiryJu9NlGhu9LrLMQ7eDjNgudQoFDK3WSNAayeIKc6B8WXXpo4a7HqxjKwg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <?php $this->head() ?>
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md  navbar-dark bg-dark fixed-top',
            ],
        ]);
        $menuItems[] = [
            'label' => "Cart <span class='badge bg-danger' id='product-counter'>" . $cartItems . "</span>",
            'url' => ['/cart/index'],
        ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => Yii::t('app','Signup'), 'url' => ['/site/signup']];
            $menuItems[] = ['label' => Yii::t('app','Login'), 'url' => ['/site/login']];
        } else {
            $menuItems[] = [
                'label' => Yii::$app->user->identity->getDisplayName(),
                'items' => [
                    ['label' => 'Profile', 'url' => ['/profile/index']],
                    [
                        'label' => 'Logout',
                        'linkOptions' => [
                            'data-method' => 'post'
                        ],
                        'url' => ['/site/logout']],
                ]
            ];
        }

        echo Nav::widget([
            'options' => [
                'class' => 'navbar-nav ms-auto',
            ],
            'items' => $menuItems,
            'encodeLabels' => false,
        ]);
        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0">
        <div class="container">
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted">
        <div class="container">
            <p class="float-start">&copy; <?= Html::encode('My Application') ?> <?= date('Y') ?></p>
            <p class="float-end"><?= Yii::powered() ?></p>
        </div>
    </footer>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage();
