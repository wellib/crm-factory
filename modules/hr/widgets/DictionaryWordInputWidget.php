<?php


namespace app\modules\hr\widgets;

use app\themes\gentelella\widgets\grid\ActionColumn;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\InputWidget;

use yii\base\InvalidConfigException;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use app\modules\hr\models\DictionaryWord;

use yii\bootstrap\Modal;


class DictionaryWordInputWidget extends InputWidget
{
    /**
     * See constants DICTIONARY_* in \app\modules\hr\models\DictionaryWord
     * @var integer
     * @see DictionaryWord
     */
    public $dictionary;

    /**
     * @var DictionaryWord[]|array
     */
    protected $models;


    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if (empty($this->dictionary)) {
            throw new InvalidConfigException('$dictionary not be empty.');
        }
        if (!in_array($this->dictionary, array_keys(DictionaryWord::dictionaryList()))) {
            throw new InvalidConfigException('Unknown $dictionary. See constants DICTIONARY_* in \app\modules\hr\models\DictionaryWord.');
        }
        $this->models = DictionaryWord::find()->dictionary($this->dictionary)->all();
    }

    public function run()
    {
        echo Html::beginTag('div', ['class' => 'row']);
            echo Html::beginTag('div', ['class' => 'col-lg-10 col-md-10 col-sm-9']);
                $this->renderField();
            echo Html::endTag('div');
            echo Html::beginTag('div', ['class' => 'col-lg-2 col-md-2 col-sm-3']);
                $this->renderButton();
            echo Html::endTag('div');
        echo Html::endTag('div');
    }
    
    protected function renderField()
    {
        $items = ArrayHelper::map($this->models, function($model){
            /** @var DictionaryWord $model */
            return $model->getId();
        }, function($model){
            /** @var DictionaryWord $model */
            return $model->getWord();
        });
        $options = ArrayHelper::merge([
            'class' => 'form-control',
            'data-dictionary-word-field' => $this->dictionary,
            'prompt' => '',
        ], $this->options);
        if ($this->hasModel()) {
            echo Html::activeDropDownList($this->model, $this->attribute, $items, $options);
        } else {
            echo Html::dropDownList($this->name, $this->value, $items, $options);
        }
    }

    protected function renderButton()
    {
        echo Html::button('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', [
            'type' => 'button',
            'class' => 'btn btn-primary btn-block',
            'onclick' => 'vueDictionaryWordCrud.showModal(' . $this->dictionary . '); return false;'
        ]);
    }

}