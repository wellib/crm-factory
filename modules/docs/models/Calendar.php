<?php

namespace app\modules\docs\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use app\modules\docs\Module;
use app\modules\accounts\models\User;
use app\modules\docs\validators\DateCompareValidator;
/**
 * This is the model class for collection "docs_contract".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $number
 * @property mixed $date
 * @property mixed $company
 * @property mixed $names
 * @property mixed $parent
 * @property mixed $files
 * @property mixed $description
 */
class Calendar extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'docs_calendar';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'date',
            'date_to',
            'timestamp',
            'type',
            'name',
            'place',
            'description',
            'status',
            'notify',
            '_author',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['name', 'date',], 'required'],
          [['date_to'], DateCompareValidator::className(),
              'operator' => '>',
              'compareAttribute' => 'date',
          ],
          [['name', 'timestamp', 'notify', 'date_to', 'place', 'description', 'status', '_author', 'created_at', 'updated_at', 'type'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => 'Название события',
            'type' => 'Тип события',
            'date' => 'Время проведения',
            'date_to' => 'До',
            'timestamp' => 'Дата, время',
            'description' => 'Описание',
            'place' => 'Место',
            'status' => 'Статус',
            'notify' => 'Уведомлять',
            '_author' => 'Кто добавил',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function getId()
    {
        return (string)$this->_id;
    }

    public function getAuthor()
    {
        $comp =  \app\modules\accounts\models\User::find()->where(['_id' => $this->_author])->one();
        return (string)$comp->name;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $userID = Yii::$app->getUser()->getId();
            $this->_author = $userID;
        }           
        $this->updated_at = time();
        //$this->status = 0;
        $this->timestamp =strtotime($this->date);
        $this->timestamp =strtotime($this->date);
        return parent::beforeSave($insert); 
    }

}
