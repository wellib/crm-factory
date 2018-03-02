<?php


namespace app\modules\hr\widgets;

use yii\base\Widget;

use yii\helpers\Json;
use yii\helpers\Url;

use yii\web\View;

use app\modules\hr\models\DictionaryWord;

use app\assets\VueAsset;
use app\assets\VueResourceAsset;

/**
 * Class DictionaryWordCRUDWidget
 * @property string $dictionary
 * @package app\modules\hr\widgets
 */
class DictionaryWordCrudWidget extends Widget
{

    public function run()
    {
        $html = <<<HTML
            <div id="vue-dictionary-word-crud">
                <div class="modal fade" id="vue-dictionary-word-crud-modal" tabindex="-1" role="dialog" aria-labelledby="vue-dictionary-word-crud-modal-label">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="vue-dictionary-word-crud-modal-label">Modal title</h4>
                            </div>
                            <div class="modal-body">
                                <div id="vue-dictionary-word-crud-preloader">
                                    <div class="sk-cube-grid">
                                        <div class="sk-cube sk-cube1"></div>
                                        <div class="sk-cube sk-cube2"></div>
                                        <div class="sk-cube sk-cube3"></div>
                                        <div class="sk-cube sk-cube4"></div>
                                        <div class="sk-cube sk-cube5"></div>
                                        <div class="sk-cube sk-cube6"></div>
                                        <div class="sk-cube sk-cube7"></div>
                                        <div class="sk-cube sk-cube8"></div>
                                        <div class="sk-cube sk-cube9"></div>
                                    </div>
                                </div>
                                
                                <table class="table table-hover">
                                    <tbody>
                                    <tr v-for="(model, index) in words">
                                        <td>
                                            {{ model.word }}
                                        </td>
                                        <td>
                                            <button v-on:click="update(model)" type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            <button v-on:click="remove(model, index)" type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success pull-left" v-on:click="create()">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
HTML;

        $ajaxUrls = [
            'index'  => Url::to(['/hr/dictionary-word-api/index'], true),
            'create' => Url::to(['/hr/dictionary-word-api/create'], true),
            'update' => Url::to(['/hr/dictionary-word-api/update'], true),
            'delete' => Url::to(['/hr/dictionary-word-api/delete'], true),
        ];

        $view = $this->getView();
        VueAsset::register($view);
        VueResourceAsset::register($view);

        $dictionaryLabels = Json::encode(DictionaryWord::dictionaryList());

        $view->registerJs(<<<JS
            dictionaryLabels = {$dictionaryLabels};
            vueDictionaryWordCrud = new Vue({
                el: '#vue-dictionary-word-crud',
                data: {
                    apiURLs: {
                        index : '{$ajaxUrls['index']}',                    
                        create: '{$ajaxUrls['create']}',                    
                        update: '{$ajaxUrls['update']}',                    
                        delete: '{$ajaxUrls['delete']}'                 
                    },
                    dictionary: null,
                    words: [
                        
                    ]
                },
                watch: {
                    dictionary: 'fetchData'
                },
                methods: {
                    getHttp: function() {
                        return this.\$http;
                    },
                    fetchData: function () {
                        var self = this;
                        self.showPreloader();
                        this.getHttp().get(self.apiURLs.index, { params: { dictionary: self.dictionary }}).then(function(response) {
                            self.words = response.data;
                            self.hidePreloader();
                        }, function(response) {
                            console.log('error');
                            self.hidePreloader();
                        });
                    },
                    showModal: function(dictionary) {
                        this.dictionary = dictionary;
                        $('#vue-dictionary-word-crud-modal').modal('show');
                        $('#vue-dictionary-word-crud-modal-label').html(dictionaryLabels[dictionary]);
                    },
                    hideModal: function() {
                        $('#vue-dictionary-word-crud-modal').modal('hide');
                    },
                    showPreloader: function() {
                        $('#vue-dictionary-word-crud-preloader').fadeIn('fast');
                    },
                    hidePreloader: function() {
                        $('#vue-dictionary-word-crud-preloader').fadeOut('fast');
                    },
                    create: function() {
                        var self = this;
                        var word = prompt('Введите слово');
                        if (word) {
                            var formData = new FormData();
                            formData.append('word', word);
                            formData.append('dictionary', self.dictionary);
                            self.showPreloader();
                            this.getHttp().post(self.apiURLs.create, formData).then(function(response) {
                                self.words.push(Vue.util.extend({},response.data));
                                self.hidePreloader();
                                self.updateExternalFields();
                            }, function(response) {
                                console.log('error');
                                self.hidePreloader();
                            });
                        }
                    },
                    update: function(model) {
                        var self = this;
                        var word = prompt('Введите слово', model.word);
                        if (word) {
                            model.word = word;
                            self.showPreloader();
                            this.getHttp().put(self.apiURLs.update, model, { params: { id:  model._id }, emulateHTTP: false, emulateJSON: false}).then(function(response) {
                                self.hidePreloader();
                                self.updateExternalFields();
                            }, function(response) {
                                console.log('error');
                                self.hidePreloader();
                            });
                        }
                    },
                    remove: function(model, index) {
                        var self = this;
                        var del = confirm('Удалить?');
                        if (del) {
                            self.showPreloader();
                            this.getHttp().delete(self.apiURLs.delete, { params: { id:  model._id } }).then(function(response) {
                                self.words.splice(index, 1)
                                self.hidePreloader();
                                self.updateExternalFields();
                            }, function(response) {
                                console.log('error');
                                self.hidePreloader();
                            });
                        }
                    },
                    updateExternalFields: function() {
                        var self = this;
                        $('[data-dictionary-word-field=' + self.dictionary + ']').each(function(){
                            var el = $(this);
                                val = el.find(':selected').val();
                            el.find('option:gt(0)').remove(); // remove old options
                            $.each(self.words, function(key,model) {
                              el.append($('<option></option>')
                                 .attr('value', model._id)
                                 .prop('selected', model._id == val)
                                 .text(model.word));
                            });
                        });
                    }
                }
            });
JS
        , View::POS_READY, 'vue-dictionary-word-crud');


        $view->registerCss(<<<CSS
        #vue-dictionary-word-crud table td,
        #vue-dictionary-word-crud table th {
            text-align: left !important;
            vertical-align: middle;
        }
        #vue-dictionary-word-crud table tbody td:last-child {
            width: 100px;
        }
        #vue-dictionary-word-crud-preloader {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.7);
            display: none;
        }
        .sk-cube-grid {
          width: 40px;
          height: 40px;
          margin: 100px auto;
        }
        
        .sk-cube-grid .sk-cube {
          width: 33%;
          height: 33%;
          background-color: #333;
          float: left;
          -webkit-animation: sk-cubeGridScaleDelay 1.3s infinite ease-in-out;
                  animation: sk-cubeGridScaleDelay 1.3s infinite ease-in-out; 
        }
        .sk-cube-grid .sk-cube1 {
          -webkit-animation-delay: 0.2s;
                  animation-delay: 0.2s; }
        .sk-cube-grid .sk-cube2 {
          -webkit-animation-delay: 0.3s;
                  animation-delay: 0.3s; }
        .sk-cube-grid .sk-cube3 {
          -webkit-animation-delay: 0.4s;
                  animation-delay: 0.4s; }
        .sk-cube-grid .sk-cube4 {
          -webkit-animation-delay: 0.1s;
                  animation-delay: 0.1s; }
        .sk-cube-grid .sk-cube5 {
          -webkit-animation-delay: 0.2s;
                  animation-delay: 0.2s; }
        .sk-cube-grid .sk-cube6 {
          -webkit-animation-delay: 0.3s;
                  animation-delay: 0.3s; }
        .sk-cube-grid .sk-cube7 {
          -webkit-animation-delay: 0s;
                  animation-delay: 0s; }
        .sk-cube-grid .sk-cube8 {
          -webkit-animation-delay: 0.1s;
                  animation-delay: 0.1s; }
        .sk-cube-grid .sk-cube9 {
          -webkit-animation-delay: 0.2s;
                  animation-delay: 0.2s; }
        
        @-webkit-keyframes sk-cubeGridScaleDelay {
          0%, 70%, 100% {
            -webkit-transform: scale3D(1, 1, 1);
                    transform: scale3D(1, 1, 1);
          } 35% {
            -webkit-transform: scale3D(0, 0, 1);
                    transform: scale3D(0, 0, 1); 
          }
        }
        
        @keyframes sk-cubeGridScaleDelay {
          0%, 70%, 100% {
            -webkit-transform: scale3D(1, 1, 1);
                    transform: scale3D(1, 1, 1);
          } 35% {
            -webkit-transform: scale3D(0, 0, 1);
                    transform: scale3D(0, 0, 1);
          } 
        }
CSS
        );


        return $html;
    }
}
