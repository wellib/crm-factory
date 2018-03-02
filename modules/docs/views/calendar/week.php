<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\modules\docs\Module;
use app\modules\docs\models\CalendarPeriod;
use app\modules\docs\models\CalendarPeriodSearch;

/* @var $this yii\web\View */
/* @var $searchModel CalendarPeriodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ежедневник';
$this->params['breadcrumbs'][] = $this->title;

if(!$_SESSION['curtime'])$_SESSION['curtime']=time();

if (isset($_SESSION['type']))
  $type = $_SESSION['type'];
else {
  $type = 'work';
}
$params = Yii::$app->request->queryParams;
if (isset($params['CalendarSearch']['type'])) {
  $type = $params['CalendarSearch']['type'];
  $_SESSION['type'] = $type;
}

  $monthes = array(
    1 => 'Январь' , 2 => 'Февраль' , 3 => 'Март' ,
    4 => 'Апрель' , 5 => 'Май' , 6 => 'Июнь' ,
    7 => 'Июль' , 8 => 'Август' , 9 => 'Сентябрь' ,
    10 => 'Октябрь' , 11 => 'Ноябрь' ,
    12 => 'Декабрь'
  );

  $calendar_event_types=array();
  $calendar_event_types = array (
    array(
      'type' => 'alert','color' => 'yellow','name' => 'Предупреждение'
    ),
  );

?>


<div class="row">
  <div class="col-lg-3" style="text-align: left;">
    <a href="?CalendarSearch[type]=personal" class="btn btn-default <?php echo ($type=='personal')?'active':''; ?>">Личный</a>
    <a href="?CalendarSearch[type]=work" class="btn btn-default <?php echo ($type=='work')?'active':''; ?>">Рабочий</a>
  </div>
  <div class="col-lg-3" style="text-align: right;">
    <a href="day" class="btn btn-default">День</a>
    <a href="week" class="btn btn-default active">Неделя</a>
    <a href="month" class="btn btn-default">Месяц</a>
  </div>
  <div class="col-lg-3">
    <ul class="pager select_month" style="margin: 0;">
      <li><a href="#" inc="-7">&lt</a></li>
      <span style="width: 120px;display: inline-block;">
        <?php
          // определение первой даты для отображения
          $cdate=$_SESSION['curtime'];
          $cdate-=(date('w',$cdate)-1)*86400;
          echo date('d.m.Y',$cdate).' - '.date('d.m.Y',$cdate+6*86400);
        ?>
       </span>
      <li><a href="#" inc="7">&gt;</a></li>
    </ul>
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


<div class="row">
  <div class="col-md-12">
    <table class="table calendar_week">
      <thead>
        <tr>
          <th>Время</th>
          <th>Название</th>
          <th>Описание</th>
          <th>Статус</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php
          $wdays=array('Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье');
          $day=$cdate;
          $offset=0;
          for($j=0;$j<7;$j++){
            $dt_bd = date('Y-m-d',$day+$offset*86400);
            $dtt = date('d.m.Y',$day+$offset*86400);
            $dt = date('d',$day+$offset*86400);
            /*$ee=$pdo->query('select * from calendar where user='.$_SESSION['user']['id'].' and dt="'.$dt_bd.'" order by tm')->fetchAll();
            */
            $ee = array();
            $pl='<a class="addjob addevent_modal" style="display:none;" data-toggle="modal" data-target="#myModal">Добавить</a>';
            echo '<tr class="day" dt="'.$dtt.'"><td colspan=4><b>'.$wdays[$j] .'</b> '.$dtt.' '.$pl.'</td></tr>';
            
            for($ii=9;$ii<20;$ii++){
              if ($ii<10) $sii = '0'.$ii;
                else $sii=''.$ii;
              if (isset($array_models[$dtt.' '.$sii.':00'])) {

                $options_view = array_merge([
                            'title' => Yii::t('yii', 'Редактировать'),
                            'aria-label' => Yii::t('yii', 'Редактировать'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-success btn-sm',
                        ], []);
                $options_del = array_merge([
                            'title' => Yii::t('yii', 'Удалить'),
                            'aria-label' => Yii::t('yii', 'Удалить'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-danger  btn-sm',
                            'data' => [
                              'confirm' => 'Вы уверены, что хотите удалить?',
                              'method' => 'post',
                             ],              
                        ], []);
                $id = $models[$array_models[$dtt.' '.$sii.':00']]->getId();
                echo '<tr dt="'.$dtt.'" class="week event event_" >';
                echo '
                  <td name="time">'.date('H:i',  $models[$array_models[$dtt.' '.$sii.':00']]->timestamp).'</td>
                  <td name="name">'.$models[$array_models[$dtt.' '.$sii.':00']]->name.'</td>
                  <td name="descr">'.$models[$array_models[$dtt.' '.$sii.':00']]->description.'</td>
                  <td name="descr">'.$models[$array_models[$dtt.' '.$sii.':00']]->getStatusName().'</td>
                  <td>'.
                    Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $id], ['class' => 'btn btn-primary btn-sm', 'title' => Yii::t('yii', 'Просмотр')]).''.
                    Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $id], $options_view).''.
                    Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $id], $options_del).'
                  </td>
                </tr>';
              $b=0;
              }
            }
            $offset++;
          }
        ?>
      </tbody>
    </table>
  </div>
</div>


<?= $this->render('_modal_form', [
        'model' => $model,
]) ?>

<?php $this->registerJsFile('/js/calendar.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>


