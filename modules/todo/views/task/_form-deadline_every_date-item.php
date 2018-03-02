<?php

use yii\web\View;
use yii\bootstrap\ActiveForm;
use app\modules\todo\models\Task;
use kartik\date\DatePicker;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model Task */
/* @var $datePickerConfig array */
/* @var $key mixed */
?>

<div class="row" data-key="<?= $key ?>">
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-10">
        <?= $form->field($model, "deadline_every_date[$key]")->widget(DatePicker::className(), $datePickerConfig)->label(false) ?>
    </div>
    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
        <button type="button" class="btn btn-danger btn-sm" onclick="everyDateDelete(this);">
            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
    </div>
</div>
<?php $this->registerJs(<<<JS
everyDateDelete = function(element) {
    $(element).closest('[data-key]').slideUp('fast', function(){
        $(this).remove();
    });
}
JS
)?>
