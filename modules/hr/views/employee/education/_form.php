<?php

use yii\web\View;

use yii\helpers\Json;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Employee;
use app\modules\hr\models\embedded\Education;

use app\assets\VueAsset;


/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */

VueAsset::register($this);

$education = new Education();

?>

<div id="vue-education">
    <table class="table table-hover">
        <thead>
        <tr>
        <?php foreach ($education->attributeLabels() as $attribute => $label): ?>
            <th><?= $label ?></th>
        <?php endforeach; ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(education, index) in data" v-bind:class="{ danger: education.hasErrors }">
        <?php foreach ($education->attributeLabels() as $attribute => $label): ?>
            <td>
                {{ education.<?= $attribute ?> }}
                <input
                    type="hidden"
                    :id="'<?= $education->formName() ?>-' + index + '-<?= $attribute ?>'"
                    :name="'<?= $education->formName() ?>[' + index + '][<?= $attribute ?>]'"
                    :value="education.<?= $attribute ?>"
                >

            </td>
        <?php endforeach; ?>
            <td>
                <button v-on:click="edit(index)" type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                <button v-on:click="remove(index)" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
            </td>
        </tr>
        </tbody>
    </table>

    <button v-on:click="create()" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add education
    </button>

    <div class="modal fade" id="vue-education-modal" tabindex="-1" role="dialog" aria-labelledby="vue-education-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button v-on:click="hideModal()" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="vue-education-modal-label">Modal title</h4>
                </div>
                <div class="modal-body">
                <?php foreach ($education->attributeLabels() as $attribute => $label): ?>
                    <div class="form-group">
                        <label for="education-<?= $attribute ?>" class="control-label"><?= $label ?></label>
                        <input v-model="model.<?= $attribute ?>" type="text" id="education-<?= $attribute ?>"class="form-control">
                    </div>
                <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button v-on:click="hideModal()" type="button" class="btn btn-default pull-right">Close</button>
                    <template v-if="typeof(updateIndex) === 'number'">
                        <button v-on:click="update()" type="button" class="btn btn-primary pull-right">update</button>
                    </template>
                    <template v-else>
                        <button v-on:click="add()" type="button" class="btn btn-success pull-right">create</button>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $this->registerCss(<<<CSS
    #vue-educations .table tbody td:last-child {
        width: 100px;
    }
CSS
) ?>

<?php

$attributesJson = Json::encode($education->getAttributes());

$data = Json::encode(array_map(function($model) {
    /** @var Education $model */
    $data = $model->getAttributes();
    $data['hasErrors'] = $model->hasErrors();
    return $data;
}, (array) $model->educations));

// Если у какой то из моделей обнаружены ошибки валидации, то подсветим красным секцию контактов в аккоридионе
foreach ((array) $model->educations as $education) {
    if ($education->hasErrors()) {
        $this->registerJs(<<<JS
            $('#accordion-section-education').addClass('panel-danger');
            $('#accordion-section-education').find('.collapse').addClass('in');
JS
        );
    }
}



$this->registerJs(<<<JS

appEducation = new Vue({
  el: '#vue-education',
  data: {
    model: $attributesJson,
    updateIndex: undefined,
    data: $data
  },
  methods: {
    resetModelData: function() {
        this.model = $attributesJson;
    },
    resetUpdateIndex: function() {
        this.updateIndex = undefined;
    },
    create: function() {
        this.showModal();
        this.resetModelData();
    },
    add: function () {
        this.data.push(Vue.util.extend({}, this.model))
        this.resetModelData();
        this.hideModal();
    },
    edit: function(index) {
        this.showModal();
        this.updateIndex = index;
        this.model = Vue.util.extend({}, this.data[index]);
    },
    update: function() {
        this.model.hasErrors = false;
        $('#accordion-section-education').removeClass('panel-danger')
        this.data[this.updateIndex] = Vue.util.extend({}, this.model)
        this.resetModelData();
        this.resetUpdateIndex();
        this.hideModal();
    },
    remove: function(index) {
        if (confirm('Удалить образование?')) {
            this.data.splice(index, 1)
        }
    },
    showModal: function() {
        $('#vue-education-modal').modal('show');
    },
    hideModal: function() {
        $('#vue-education-modal').modal('hide');
        this.resetUpdateIndex();
    },
    submitForm: function() {
    
    }
  }
});

JS
); ?>