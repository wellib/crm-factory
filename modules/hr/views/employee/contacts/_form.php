<?php

use yii\web\View;

use yii\helpers\Json;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Employee;
use app\modules\hr\models\embedded\Contact;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\widgets\DictionaryWordInputWidget;

use app\assets\VueAsset;




/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */

VueAsset::register($this);

$contact = new Contact();

?>

<div id="vue-contacts">
    <table class="table table-hover">
        <thead>
        <tr>
        <?php foreach ($contact->attributeLabels() as $attribute => $label): ?>
            <th><?= $label ?></th>
        <?php endforeach; ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(contact, index) in data" v-bind:class="{ danger: contact.hasErrors }">
        <?php foreach ($contact->attributeLabels() as $attribute => $label): ?>
            <td>
                <?php if ($attribute == '__type'): ?>
                    {{ getTypeLabel(contact.<?= $attribute ?>) }}
                <?php elseif ($attribute == 'main'): ?>
                    <span v-if="contact.<?= $attribute ?> == 1" class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                <?php else: ?>
                    {{ contact.<?= $attribute ?> }}
                <?php endif; ?>
                <input
                    type="hidden"
                    :id="'<?= $contact->formName() ?>-' + index + '-<?= $attribute ?>'"
                    :name="'<?= $contact->formName() ?>[' + index + '][<?= $attribute ?>]'"
                    :value="contact.<?= $attribute ?>"
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
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add contact
    </button>

    <div class="modal fade" id="vue-contacts-modal" tabindex="-1" role="dialog" aria-labelledby="vue-contacts-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button v-on:click="hideModal()" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="vue-contacts-modal-label">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="contact-type" class="control-label">Тип</label>
                        <?= DictionaryWordInputWidget::widget([
                            'name' => 'type',
                            'id' => 'contact-type',
                            'dictionary' => DictionaryWord::DICTIONARY_CONTACT_TYPE,
                            'options' => [
                                'v-model' => 'model.__type',
                            ],
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label for="contact-value" class="control-label">Контакт</label>
                        <input v-model="model.value" type="text" id="contact-value"class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contact-description" class="control-label">Описание</label>
                        <textarea v-model="model.description" id="contact-description" class="form-control"></textarea>
                    </div>
                    <div class="form-group field-contact-main">
                        <div class="checkbox">
                            <label for="contact-main">
                                <input type="checkbox" id="contact-main" v-model="model.main" v-bind:true-value="1" v-bind:false-value="0">
                                Основной контакт
                            </label>
                        </div>
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
    #vue-contacts .table tbody td:last-child {
        width: 100px;
    }
CSS
) ?>

<?php

$attributesJson = Json::encode($contact->getAttributes());

$data = Json::encode(array_map(function($model) {
    /** @var Contact $model */
    $data = $model->getAttributes();
    $data['__type'] = (string) $data['__type']; // fix, attribute type - is ObjectID
    $data['hasErrors'] = $model->hasErrors();
    return $data;
}, (array) $model->contacts));

// Если у какой то из моделей обнаружены ошибки валидации, то подсветим красным секцию контактов в аккоридионе
foreach ((array) $model->contacts as $contact) {
    if ($contact->hasErrors()) {
        $this->registerJs(<<<JS
            $('#accordion-section-contacts').addClass('panel-danger');
            $('#accordion-section-contacts').find('.collapse').addClass('in');
JS
        );
    }
}



$this->registerJs(<<<JS

appContacts = new Vue({
  el: '#vue-contacts',
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
    getTypeLabel: function(value) {
        return $('#contact-type option[value="' + value + '"]').text();
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
        $('#accordion-section-contacts').removeClass('panel-danger')
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
        $('#vue-contacts-modal').modal('show');
    },
    hideModal: function() {
        $('#vue-contacts-modal').modal('hide');
        this.resetUpdateIndex();
    },
    submitForm: function() {
    
    }
  }
});

JS
); ?>