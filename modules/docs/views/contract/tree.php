<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\docs\Module;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Реестр договоров';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="contract-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
	<ul class="nav nav-tabs">
		<li><?= Html::a('Таблицей', ['index'], ['class' => '']) ?></li>
		<li><?= Html::a('Деревом', ['tree'], ['class' => '']) ?></li>
		<li><?= Html::a('Компании', ['/docs/contractcompany/index'], ['class' => '']) ?></li>
	</ul>
    </p>

<ul class="nav child_menu" style="display: block;">
<?php

    function outTree($ret_array, $parent_id, $level) {
        if (isset($ret_array[$parent_id])) { 
            foreach ($ret_array[$parent_id] as $value) {
								$id = $value->getId();
                echo "<li style='margin-left:" . ($level * 25) . "px;'>
												<a href='". Url::to(['/docs/contract/view', 'id' => (string) $id])."' style='color:#000;'>".
												$value->name."</a>
											</li>";
                $level++; 
                outTree($ret_array, $id, $level);
                $level--;
            }
        }
    }

		outTree($return, 0, 0);

?>
</ul>
</div>
