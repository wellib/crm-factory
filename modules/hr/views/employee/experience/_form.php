<?php

use yii\web\View;

use yii\helpers\Json;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Employee;
use app\modules\hr\models\embedded\Experience;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\widgets\DictionaryWordInputWidget;

use app\assets\VueAsset;

use kartik\date\DatePickerAsset;


/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */

VueAsset::register($this);
DatePickerAsset::register($this);

$experience = new Experience();

?>

<div id="vue-experience">
    <table class="table table-hover">
        <thead>
        <tr>
        <?php foreach ($experience->attributeLabels() as $attribute => $label): ?>
            <th><?= $label ?></th>
        <?php endforeach; ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(experience, index) in data" v-bind:class="{ danger: experience.hasErrors }">
        <?php foreach ($experience->attributeLabels() as $attribute => $label): ?>
            <td>
                <?php if ($attribute == '__dismissal_reason'): ?>
                    {{ getDismissalReasonLabel(experience.<?= $attribute ?>) }}
                <?php else: ?>
                    {{ experience.<?= $attribute ?> }}
                <?php endif; ?>
                <input
                    type="hidden"
                    :id="'<?= $experience->formName() ?>-' + index + '-<?= $attribute ?>'"
                    :name="'<?= $experience->formName() ?>[' + index + '][<?= $attribute ?>]'"
                    :value="experience.<?= $attribute ?>">
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
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add experience
    </button>

    <div class="modal fade" id="vue-experience-modal" tabindex="-1" role="dialog" aria-labelledby="vue-experience-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button v-on:click="hideModal()" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="vue-experience-modal-label">Modal title</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="experience-start_date" class="control-label">Начало</label>
                        <input v-model="model.start_date" type="text" id="experience-start_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="experience-end_date" class="control-label">Окончание</label>
                        <input v-model="model.end_date" type="text" id="experience-end_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="experience-organization" class="control-label">Название организации</label>
                        <input v-model="model.organization" type="text" id="experience-organization" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="experience-position" class="control-label">Должность</label>
                        <input v-model="model.position" type="text" id="experience-position" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="experience-dismissal_reason" class="control-label">Причина увольнения</label>
                        <?= DictionaryWordInputWidget::widget([
                            'name' => 'dismissal_reason',
                            'id' => 'experience-dismissal_reason',
                            'dictionary' => DictionaryWord::DICTIONARY_DISMISSAL_REASON,
                            'options' => [
                                'v-model' => 'model.__dismissal_reason',
                            ],
                        ]) ?>
                    </div>
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
    #vue-experience .table tbody td:last-child {
        width: 100px;
    }
CSS
) ?>

<?php

$attributesJson = Json::encode($experience->getAttributes());

$data = Json::encode(array_map(function($model) {
    /** @var Experience $model */
    $data = $model->getAttributes();
    $data['__dismissal_reason'] = (string) $data['__dismissal_reason']; // fix, attribute type - is ObjectID
    $data['hasErrors'] = $model->hasErrors();
    return $data;
}, (array) $model->experience));

// Если у какой то из моделей обнаружены ошибки валидации, то подсветим красным секцию контактов в аккоридионе
foreach ((array) $model->experience as $experience) {
    if ($experience->hasErrors()) {
        $this->registerJs(<<<JS
            $('#accordion-section-experience').addClass('panel-danger');
            $('#accordion-section-experience').find('.collapse').addClass('in');
JS
        );
    }
}



$this->registerJs(<<<JS

appExperience = new Vue({
  el: '#vue-experience',
  data: {
    model: $attributesJson,
    updateIndex: undefined,
    data: $data
  },
  mounted: function() {
    var self = this;
     $('#experience-start_date').kvDatepicker({
        autoclose: true,
        format: 'dd.mm.yyyy',
        language: 'ru'
     }).on('changeDate', function(){
        // Костыль: не отрабатывает v-model="model.birth_date"
        // Поэтому по событию приходится вручную назначать значение
        self.model.start_date = $(this).val();
     });
     $('#experience-end_date').kvDatepicker({
        autoclose: true,
        format: 'dd.mm.yyyy',
        language: 'ru'
     }).on('changeDate', function(){
        // Костыль: не отрабатывает v-model="model.birth_date"
        // Поэтому по событию приходится вручную назначать значение
        self.model.end_date = $(this).val();
     });
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
    getDismissalReasonLabel: function(value) {
        return $('#experience-dismissal_reason option[value="' + value + '"]').text();
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
        $('#accordion-section-experience').removeClass('panel-danger')
        this.data[this.updateIndex] = Vue.util.extend({}, this.model)
        this.resetModelData();
        this.resetUpdateIndex();
        this.hideModal();
    },
    remove: function(index) {
        if (confirm('Удалить стаж работы?')) {
            this.data.splice(index, 1)
        }
    },
    showModal: function() {
        $('#vue-experience-modal').modal('show');
    },
    hideModal: function() {
        $('#vue-experience-modal').modal('hide');
        this.resetUpdateIndex();
    }
  }
});

JS
); ?>