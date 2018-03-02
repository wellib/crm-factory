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
    <a href="day" class="btn btn-default active">День</a>
    <a href="week" class="btn btn-default ">Неделя</a>
    <a href="month" class="btn btn-default">Месяц</a>
  </div>
  <div class="col-lg-3">
    <ul class="pager select_month" style="margin: 0;">
      <li><a href="#" inc="-1">&lt</a></li>
      <span style="width: 120px;display: inline-block;">
        <?php
          // определение первой даты для отображения
          $cdate=$_SESSION['curtime'];
          echo date('d.m.Y',$cdate);
        ?>
       </span>
      <li><a href="#" inc="1">&gt;</a></li>
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
<?php
//var_dump($array_models);
?>
<div class="row">
  <div class="col-md-12">
    <table class="table calendar_day" dt="<?php echo date('d.m.Y',$cdate);?>">
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
          $dt_bd = date('Y-m-d',$cdate);
          $dtt = date('d.m.Y',$cdate);
          $ee=array();
          for($j=0;$j<24;$j++){
            if($j<10)$j="0$j";
            $tm="$j:00";
            if (isset($array_models[$dtt.' '.$tm])) {

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
                $id = $models[$array_models[$dtt.' '.$tm.'']]->getId();
                echo '<tr dt="'.$dtt.'" class="week event event_" >';
                echo '
                  <td name="time">'.date('H:i',  $models[$array_models[$dtt.' '.$tm.'']]->timestamp).'</td>
                  <td name="name">'.$models[$array_models[$dtt.' '.$tm.'']]->name.'</td>
                  <td name="descr">'.$models[$array_models[$dtt.' '.$tm.'']]->description.'</td>
                  <td name="descr">'.$models[$array_models[$dtt.' '.$tm.'']]->getStatusName().'</td>
                  <td>'.
                    Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $id], ['class' => 'btn btn-primary btn-sm', 'title' => Yii::t('yii', 'Просмотр')]).''.
                    Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $id], $options_view).''.
                    Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $id], $options_del).'
                  </td>
                </tr>';

            }
            if(isset($ee[$j]))foreach ($ee[$j] as $e){
              $tm=date('H:i',strtotime($e[tm]));
              echo '<tr tp="'.$e[type].'" class="week event event_'.$e[type].'" i="'.$e[id].'" tm="'.$tm.'">
                  <td name="time">
                  '.$tm.'
                  <span class="addjob addevent_modal glyphicon glyphicon-plus" data-toggle="modal" data-target="#myModal"></span>
                  </td>
                  <td name="name">'.$e[name].'</td>
                  <td name="descr">'.$e[descr].'</td>
                  <td>
                  <span class="del glyphicon glyphicon-remove" title="Удалить"></span>
                  <span class="edit glyphicon glyphicon-pencil" title="Редактировать"></span>
                  </td>
                </tr>';
            }else{
              echo '<tr class="week event" tm="'.$tm.'">
                  <td name="time">'.$j.':00</td>
                  <td name="name"><a class="addjob addevent_modal"  style="display:none;" data-toggle="modal" data-target="#myModal">Добавить</a></td>
                  <td name="descr"></td>
                  <td name="descr"></td>
                  <td>
                    
                  </td>
                </tr>';
            }
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

