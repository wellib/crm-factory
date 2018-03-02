<?php

namespace app\modules\hr\models\embedded;


use yii\base\Model;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use app\modules\structure\models\Department;
use app\modules\structure\validators\DepartmentValidator;
use yii\helpers\Html;

/**
 * Class CompanyCard
 * @package app\modules\hr\models\employee
 */
class CompanyCard extends Model
{

    /**
     * Шаблон регулярного выражения для валидации даты в формате дд.мм.гггг
     */
    const DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
    const DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';
    const DATE_PHP_FORMAT = 'd.m.Y';

    public $employee_id;
    public $biometrics_id;
    public $contract_number;
    public $contract_date;
    public $employment_date;
    public $_structure_department;


    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'employee_id',
                'biometrics_id',
                'contract_number',
                'contract_date',
                'employment_date',
                '_structure_department',
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['employee_id', 'biometrics_id', 'contract_number'], 'string', 'max' => 255],
            [['contract_date', 'employment_date'], 'match',
                'pattern' => self::DATE_REGEXP_PATTERN,
                'message' => Module::t('company-card', 'DATE__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ]),
            ],

            [['_structure_department'], DepartmentValidator::className()],
            [['_structure_department'], 'default', 'value' => null],

            [['employee_id', 'biometrics_id', 'contract_number', 'contract_date', 'employment_date', '_structure_department'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'employee_id'             => Module::t('company-card', 'ATTR__EMPLOYEE_ID__LABEL'),
            'biometrics_id'           => Module::t('company-card', 'ATTR__BIOMETRICS_ID__LABEL'),
            'contract_number'         => Module::t('company-card', 'ATTR__CONTRACT_NUMBER__LABEL'),
            'contract_date'           => Module::t('company-card', 'ATTR__CONTRACT_DATE__LABEL'),
            'employment_date'         => Module::t('company-card', 'ATTR__EMPLOYMENT_DATE__LABEL'),
            '_structure_department'   => \app\modules\structure\Module::t('department', 'MODEL_NAME'),
        ];
    }

    /**
     * Название структурного подразделения компании в которой работает сотрудник
     *
     * @param bool $renderIcon
     * @param bool $returnLink Вернет в виде ссылки [a href]
     * @param array $linkOptions
     * @return null|string
     */
    public function getStructureDepartment($renderIcon = true, $returnLink = false, $linkOptions = ['target' => '_blank'])
    {
        if (!empty($this->_structure_department) && $model = Department::find()->id($this->_structure_department)->one()) {
            if ($returnLink) {
                return Html::a($model->getName($renderIcon), $model->getViewUrl(true), $linkOptions);
            }
            return $model->getName($renderIcon);
        }
        return null;
    }

    protected static function labelForExportColumn($message)
    {
        return Module::t('company-card', 'MODEL_NAME') . ': ' . Module::t('company-card', $message);
    }

    public static function exportColumnsConfig($attribute)
    {
        return [
            [
                'attribute' => $attribute . '.employee_id',
                'label' => self::labelForExportColumn('ATTR__EMPLOYEE_ID__LABEL'),
            ],
            [
                'attribute' => $attribute . '.biometrics_id',
                'label' => self::labelForExportColumn('ATTR__BIOMETRICS_ID__LABEL'),
            ],
            [
                'attribute' => $attribute . '.contract_number',
                'label' => self::labelForExportColumn('ATTR__CONTRACT_NUMBER__LABEL'),
            ],
            [
                'attribute' => $attribute . '.contract_date',
                'label' => self::labelForExportColumn('ATTR__CONTRACT_DATE__LABEL'),
            ],
            [
                'attribute' => $attribute . '.employment_date',
                'label' => self::labelForExportColumn('ATTR__EMPLOYMENT_DATE__LABEL'),
            ],
            [
                'attribute' => $attribute . '._structure_department',
                'label' => self::labelForExportColumn('ATTR__STRUCTURE_DEPARTMENT__LABEL'),
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->companyCard->getStructureDepartment();
                }
            ],
        ];
    }
}