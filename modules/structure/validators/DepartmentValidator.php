<?php

namespace app\modules\structure\validators;

use yii\validators\Validator;
use yii\base\NotSupportedException;

use app\modules\structure\Module;
use app\modules\structure\models\Department;

/**
 * Class DictionaryWordValidator
 * @package app\modules\hr\models\validators
 */
class DepartmentValidator extends Validator
{

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $departmentModel = Department::find()->id($value)->one();
        if ($departmentModel) {
            $model->$attribute = $departmentModel->getId(false);
        } else {
            $this->addError($model, $attribute, Module::t('department', 'VALIDATOR__ID_DOES_NOT_EXIST_IN_DATABASE'));
        }

    }

    protected function validateValue($value)
    {
        throw new NotSupportedException(get_class($this) . ' does not support validateValue().');
    }
}