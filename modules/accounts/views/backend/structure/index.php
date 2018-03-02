<?php

use app\modules\accounts\assets\UserStructureAsset;
use app\modules\accounts\models\User;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $treeUsers User[] */

UserStructureAsset::register($this);

$this->title = 'Структура пользователей';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    var treeUsers = <?= Json::encode($treeUsers)?>;
</script>

<div class="accounts-structure-index">

    <h1><?= $this->title ?></h1>

    <table id="structure-table" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Сотрудники</th>
        </tr>
        </thead>
        <tbody>
        <tr data-key="587cb0c2d2a57d0042506815">
            <td></td>
        </tr>
        </tbody>
    </table>

</div>