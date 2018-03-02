<?php

use app\themes\gentelella\widgets\Alert;
use yii\widgets\Breadcrumbs;
use app\themes\gentelella\widgets\Menu as MainMenuGroup;
use app\modules\app\Module;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset as Theme;

/* @var $this \yii\web\View */
/* @var $content string */

$theme = Theme::register($this);

?>
<?php $this->beginContent('@theme/views/layouts/layout.php') ?>
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="/" class="site_title"><img src="/logo3.png"> <span><?= Yii::$app->name ?></span></a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <?php if (($user = Yii::$app->getUser()->getIdentity()) !== null): ?>
                <div class="profile">
                    <div class="profile_pic">
                        <img src="<?= $user->getAvatar($theme->getUserAvatarDefault()) ?>" alt="..." class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span><?= $user->position ?></span>
                        <h2><?= $user->name ?></h2>
                    </div>
                </div>
                <?php endif; ?>
                <!-- /menu profile quick info -->
                <div class="clearfix"></div>

                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <?php foreach (Module::getInstance()->getNavGroups() as $group): ?>
                    <div class="menu_section">
                        <!--<h3>General</h3>-->
                        <?= MainMenuGroup::widget([
                            'options' => ['class' => 'nav side-menu'],
                            'items' => $group,
                            'encodeLabels' => false,
                            'submenuTemplate' => "\n<ul class='nav child_menu'>\n{items}\n</ul>\n",
                            'encodeLabels' => false, //allows you to use html in labels
                            'activateParents' => true,
                        ]) ?>
                        <?php $this->registerCss(<<<CSS
                        .nav-sm .side-menu > li > a > .label {
                            display: block;
                            margin-bottom: 5px;
                            padding: 5px;
                            margin-top: 5px;
                            font-size: 11px;
                        }
CSS
                        )?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <!--<div class="sidebar-footer hidden-small">-->
                <!--    <a data-toggle="tooltip" data-placement="top" title="Settings">-->
                <!--        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>-->
                <!--    </a>-->
                <!--    <a data-toggle="tooltip" data-placement="top" title="FullScreen">-->
                <!--        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>-->
                <!--    </a>-->
                <!--    <a data-toggle="tooltip" data-placement="top" title="Lock">-->
                <!--        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>-->
                <!--    </a>-->
                <!--    <a data-toggle="tooltip" data-placement="top" title="Logout">-->
                <!--        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>-->
                <!--    </a>-->
                <!--</div>-->
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav class="" role="navigation">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <?php if (($user = Yii::$app->getUser()->getIdentity()) !== null): ?>
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="<?= $user->getAvatar($theme->getUserAvatarDefault()) ?>" alt=""><?= $user->name ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="<?= \yii\helpers\Url::to(['/accounts/user/update', 'id' => (string)Yii::$app->getUser()->getIdentity()->_id]) ?>"><i class="fa fa-sign-out pull-right"></i> Профиль</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['/accounts/user/signout']) ?>"><i class="fa fa-sign-out pull-right"></i> Выход</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                //'homeLink' => [
                //    'label' => 'Админ-панель',
                //    'url' => ['/admin/default/index'],
                //],
                'options' => [
                    'class' => 'breadcrumb',
                    'style' => 'margin-left: 0;background: #ededed;margin-top: 70px;',
                ]
            ]) ?>

            <?= Alert::widget() ?>

            <?= $content ?>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
            <!--<div class="pull-right">-->
            <!--    Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>-->
            <!--</div>-->
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>
</div>
<?php $this->endContent() ?>
