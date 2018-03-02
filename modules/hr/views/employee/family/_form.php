<?php

use yii\web\View;

use yii\helpers\Json;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Employee;
use app\modules\hr\models\embedded\Family;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\widgets\DictionaryWordInputWidget;

use app\assets\VueAsset;

use kartik\date\DatePickerAsset;


/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */

VueAsset::register($this);
DatePickerAsset::register($this);

$family = new Family();

?>

<div id="vue-family">
    <table class="table table-hover">
        <thead>
        <tr>
        <?php foreach ($family->attributeLabels() as $attribute => $label): ?>
            <th><?= $label ?></th>
        <?php endforeach; ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(family, index) in data" v-bind:class="{ danger: family.hasErrors }">
        <?php foreach ($family->attributeLabels() as $attribute => $label): ?>
            <td>
                <?php if ($attribute == '__kinship'): ?>
                    {{ getKinshipLabel(family.<?= $attribute ?>) }}
                <?php else: ?>
                    {{ family.<?= $attribute ?> }}
                <?php endif; ?>
                <input
                    type="hidden"
                    :id="'<?= $family->formName() ?>-' + index + '-<?= $attribute ?>'"
                    :name="'<?= $family->formName() ?>[' + index + '][<?= $attribute ?>]'"
                    :value="family.<?= $attribute ?>"
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
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add family
    </button>

    <div class="modal fade" id="vue-family-modal" tabindex="-1" role="dialog" aria-labelledby="vue-family-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button v-on:click="hideModal()" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="vue-family-modal-label">Modal title</h4>
                </div>
                <div class="modal-body">
                    
                    <div class="form-group">
                        <label for="family-kinship" class="control-label">Степень родства</label>
                        <?= DictionaryWordInputWidget::widget([
                            'name' => 'kinship',
                            'id' => 'family-kinship',
                            'dictionary' => DictionaryWord::DICTIONARY_KINSHIP,
                            'options' => [
                                'v-model' => 'model.__kinship',
                            ],
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label for="family-full_name" class="control-label">ФИО</label>
                        <input v-model="model.full_name" type="text" id="family-full_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="family-birth_date" class="control-label">Дата рождения</label>
                        <input v-model="model.birth_date" type="text" id="family-birth_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="family-note" class="control-label">Описание</label>
                        <textarea v-model="model.note" id="family-note" class="form-control"></textarea>
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
    #vue-family .table tbody td:last-child {
        width: 100px;
    }
CSS
) ?>

<?php

$attributesJson = Json::encode($family->getAttributes());

$data = Json::encode(array_map(function($model) {
    /** @var Family $model */
    $data = $model->getAttributes();
    $data['__kinship'] = (string) $data['__kinship']; // fix, attribute type - is ObjectID
    $data['hasErrors'] = $model->hasErrors();
    return $data;
}, (array) $model->family));

// Если у какой то из моделей обнаружены ошибки валидации, то подсветим красным секцию контактов в аккоридионе
foreach ((array) $model->family as $family) {
    if ($family->hasErrors()) {
        $this->registerJs(<<<JS
            $('#accordion-section-family').addClass('panel-danger');
            $('#accordion-section-family').find('.collapse').addClass('in');
JS
        );
    }
}



$this->registerJs(<<<JS

appFamily = new Vue({
  el: '#vue-family',
  data: {
    model: $attributesJson,
    updateIndex: undefined,
    data: $data
  },
  mounted: function() {
    var self = this;
     $('#family-birth_date').kvDatepicker({
        autoclose: true,
        format: 'dd.mm.yyyy',
        language: 'ru'
     }).on('changeDate', function(){
        // Костыль: не отрабатывает v-model="model.birth_date"
        // Поэтому по событию приходится вручную назначать значение
        // console.log(self.model.birth_date);
        // console.log($(this).val());
        self.model.birth_date = $(this).val();
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
    getKinshipLabel: function(value) {
        return $('#family-kinship option[value="' + value + '"]').text();
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
        $('#accordion-section-family').removeClass('panel-danger')
        this.data[this.updateIndex] = Vue.util.extend({}, this.model)
        this.resetModelData();
        this.resetUpdateIndex();
        this.hideModal();
    },
    remove: function(index) {
        if (confirm('Удалить контакт?')) {
            this.data.splice(index, 1)
        }
    },
    showModal: function() {
        $('#vue-family-modal').modal('show');
    },
    hideModal: function() {
        $('#vue-family-modal').modal('hide');
        this.resetUpdateIndex();
    },
    submitForm: function() {
    
    }
  }
});

JS
); ?>