<?php
use yii\helpers\Html;
use yii\grid\GridView;

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\modules\docs\Module;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\CalendarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ежедневник';
$this->params['breadcrumbs'][] = $this->title;

if(!isset($_SESSION['curtime']))$_SESSION['curtime']=time();
?>

<div class="row">
  <div class="col-lg-3" style="text-align: left;">

  </div>
  <div class="col-lg-3" style="text-align: right;">
    <a href="day" class="btn btn-default">День</a>
    <a href="week" class="btn btn-default">Неделя</a>
    <a href="month" class="btn btn-default">Месяц</a>
  </div>
  <div class="col-lg-3">

  </div>
    <div class="col-lg-3">
    <form action="/docs/calendar/search" method="get">
    <div class="input-group">
          <input type="text" name="CalendarSearch[name]" class="form-control" placeholder="Поиск">
          <span class="input-group-btn">
          <button class="btn btn-default" type="submit">Найти</button>
          </span>
    </div>
    </form>
    </div>
</div>

<div class="search-index">
<?= GridView::widget(['dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'date',
            'date_to',
            'description',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) use ($searchModel) {
                    return $model->getStatusName();
                },
            ],
            ['class' => \app\themes\gentelella\widgets\grid\ActionColumn::className()],
        ],
]); ?>
</div>
