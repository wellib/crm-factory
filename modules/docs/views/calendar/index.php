<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\modules\docs\Module;
use kartik\datetime\DateTimePicker;

use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\CalendarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ежедневник';
$this->params['breadcrumbs'][] = $this->title;

if(!isset($_SESSION['curtime']))$_SESSION['curtime']=time();

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


?>



<div class="row">
  <div class="col-lg-3" style="text-align: left;">
    <a href="?CalendarSearch[type]=personal" class="btn btn-default <?php echo ($type=='personal')?'active':''; ?>">Личный</a>
    <a href="?CalendarSearch[type]=work" class="btn btn-default <?php echo ($type=='work')?'active':''; ?>">Рабочий</a>
  </div>
  <div class="col-lg-3" style="text-align: right;">
    <a href="day" class="btn btn-default">День</a>
    <a href="week" class="btn btn-default">Неделя</a>
    <a href="month" class="btn btn-default active">Месяц</a>
  </div>
  <div class="col-lg-3">
    <ul class="pager select_month" style="margin: 0;">
      <li><a href="#" inc="-30">&lt</a></li>
      <span style="width: 120px;display: inline-block;">
        <?php
          echo $monthes[intval(date('m',$_SESSION['curtime']))].' '.date('Y',$_SESSION['curtime']);
        ?>
       </span>
      <li><a href="#" inc="30">&gt;</a></li>
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
    <table class="table calendar month">
      <thead>
        <tr>
          <th>Понедельник</th>
          <th>Вторник</th>
          <th>Среда</th>
          <th>Четверг</th>
          <th>Пятница</th>
          <th>Суббота</th>
          <th>Воскресенье</th>
        </tr>
      </thead>
      <tbody>
        <?php
          // определение первой даты для отображения
          $cdate=$_SESSION['curtime'];
          $day=strtotime("01-".date('m-Y',$cdate));
          if(date('w',$day)=='0')$day-=6*86400;
          else $day-=(intval(date('w',$day))-1)*86400;

          // 5 недель
          $offset=0;
          while(date('m',$day+$offset*86400)==date('m',$cdate) || $offset<10){
            echo "<tr>";
            for($j=0;$j<7;$j++){
              // отличаем дни другого месяца
              if(date('m',$day+$offset*86400)!=date('m',$cdate))$nomonth='op05'; else $nomonth=0;
                
              $dt_bd = date('Y-m-d',$day+$offset*86400);
              $dtt = date('d.m.Y',$day+$offset*86400);
              $dt = date('d',$day+$offset*86400);
              ?>
              <td dt="<?php echo $dtt;?>" class="w<?php echo ($j+1).' '.$nomonth;?>">
                <div class="addjobnew addevent_modal" data-toggle="modal" data-target="#myModal">
                  <div class='number'><?php echo $dt; ?></div>
                  <div class='events'>
                    <?php
                      for($ii=0;$ii<23;$ii++){
                        if ($ii<10) $sii = '0'.$ii;
                          else $sii=''.$ii;
                        if (isset($array_models[$dtt.' '.$sii.':00'])) {
                        echo "<a class='event-edit' data-id='".$models[$array_models[$dtt.' '.$sii.':00']]->_id."'>".date('H:i',  $models[$array_models[$dtt.' '.$sii.':00']]->timestamp)." ".$models[$array_models[$dtt.' '.$sii.':00']]->name."</a><br/>";
                        }
                      }
                    ?>
                  </div>

              </div></td>
              <?php
              $offset++;
            }
            echo "</tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php
    Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modalevent',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
    ]);
?>
<div id='modalContent'></div>
<?php Modal::end();?>


<?= $this->render('_modal_form', [       'model' => $model]) ?>

<?php
$this->registerJsFile('/js/calendar.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
