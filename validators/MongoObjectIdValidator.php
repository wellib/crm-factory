<?php

namespace app\validators;

use MongoDB\BSON\ObjectID;
use Yii;
use yii\validators\Validator;

class MongoObjectIdValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} не является объектом MongoDB.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (ctype_xdigit($model->$attribute) && strlen($model->$attribute) == 24) {
            $model->$attribute = new ObjectID($model->$attribute);
        }

        if (!($model->$attribute instanceof ObjectID)) {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
