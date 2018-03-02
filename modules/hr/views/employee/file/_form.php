<?php

use yii\web\View;

use yii\helpers\Json;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Employee;
use app\modules\hr\models\File;


use app\assets\VueAsset;
use app\assets\VueResourceAsset;
use yii\helpers\Url;




/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */

VueAsset::register($this);
VueResourceAsset::register($this);

$file = new File();

?>

<div id="vue-files">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Документ</th>
            <th>Описание</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(model, index) in files" v-bind:class="{ danger: model.hasErrors }">
            <td>
                <a target="_blank" :href="'<?= Url::to(['/hr/file-api/download']) ?>?id=' + model._id">{{ model.name }}</a>
            </td>
            <td>
                 <input
                    type="hidden"
                    :id="'<?= $file->formName() ?>-' + index + '-_id'"
                    :name="'<?= $file->formName() ?>[' + index + '][_id]'"
                    :value="model._id"
                >
                <input
                    type="text"
                    class="form-control"
                    :id="'<?= $file->formName() ?>-' + index + '-description'"
                    :name="'<?= $file->formName() ?>[' + index + '][description]'"
                    :value="model.description"
                    v-model="model.description"
                    v-on:change="update(model)"
                >
            </td>
            <td>
                <button v-on:click="remove(index)" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
            </td>
        </tr>
        </tbody>
    </table>
    <div v-for="(error, i) in errors" class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" v-on:click="removeError(i)"><span aria-hidden="true">×</span></button>
        Не удалось загрузить файл <strong>{{ error.file.name }}</strong>, причины:
        <ul>
            <li v-for="err in error.errors">
                {{ err.message }}
            </li>
        </ul>
    </div>
    <input type="file" v-on:change="upload" multiple>

</div>


<?php $this->registerCss(<<<CSS
    #vue-files .table tbody td:last-child {
        width: 100px;
    }
CSS
) ?>

<?php

$attributesJson = Json::encode($file->getAttributes());

$data = Json::encode(array_map(function($model) {
    /** @var File $model */
    $data = $model->getAttributes();
    $data['_id'] = $model->getId();
    $data['hasErrors'] = $model->hasErrors();
    return $data;
}, (array) $model->files));

// Если у какой то из моделей обнаружены ошибки валидации, то подсветим красным секцию контактов в аккоридионе
foreach ((array) $model->files as $file) {
    if ($file->hasErrors()) {
        $this->registerJs(<<<JS
            $('#accordion-section-files').addClass('panel-danger');
            $('#accordion-section-files').find('.collapse').addClass('in');
JS
        );
    }
}

$ajaxUrls = [
    'create' => Url::to(['/hr/file-api/create'], true),
    'update' => Url::to(['/hr/file-api/update'], true),
];

$this->registerJs(<<<JS

appFiles = new Vue({
  el: '#vue-files',
  data: {
    apiURLs: {
        create: '{$ajaxUrls['create']}',                    
        update: '{$ajaxUrls['update']}'            
    },
    files: $data,
    errors: []
  },
  methods: {
    getHttp: function() {
         return this.\$http;
    },
    resetModelData: function() {
        this.model = $attributesJson;
    },
    // update: function(model) {
    //     var self = this;
    //     console.log(model);
    //     this.getHttp().put(self.apiURLs.update, model, { params: { id:  model._id }, emulateHTTP: false, emulateJSON: false}).then(function(response) {
    //    
    //     }, function(response) {
    //         console.log('error');
    //         
    //     });
    // },
    upload: function(e) {
        var self = this;
        var files = e.target.files || e.dataTransfer.files;
        if (!files.length) {
            return;
        }
        
        for (var i = 0, f; f = files[i]; i++) {
            (function(file) {
                var formData = new FormData();
                formData.append('{$file->formName()}[file]', file);
                self.getHttp().post(self.apiURLs.create, formData).then(function(response) {
                    self.files.push(Vue.util.extend({},response.data));
                    // self.hidePreloader();
                }, function(response, i) {
                    // console.log(response);
                    self.errors.push({file: file, errors: response.body});
                    // self.hidePreloader();
                });
            })(f);
        }
        $(e.target).val('');
    },
    uploadAjax: function() {
    
    },
    remove: function(index) {
        if (confirm('Удалить файл?')) {
            this.files.splice(index, 1)
        }
    },
    removeError: function(index) {
        this.errors.splice(index, 1);
    },
    showModal: function() {
        $('#vue-files-modal').modal('show');
    },
    hideModal: function() {
        $('#vue-files-modal').modal('hide');
    }
  }
});

JS
); ?>
