<?php

namespace app\modules\hr\models\embedded;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

/**
 * Class Education
 *
 * @property string $institution
 * @property string $graduation_year
 * @property string $certificated
 * @property string $degree
 * @property string $specialty
 *
 * @package app\modules\hr\models\embedded
 */
class Education extends EmbeddedModel
{
    /** @var string */
    public $institution; // учебное заведение
    /** @var integer */
    public $graduation_year; // год окончания
    /** @var string */
    public $certificated; // диплом
    /** @var string */
    public $degree; // ученая степень
    /** @var string */
    public $specialty; // специальность
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['institution', 'certificated', 'degree', 'specialty'], 'string', 'max' => 255],
            ['graduation_year', 'filter', 'filter' => 'intval'],
            ['graduation_year', 'date', 'format' => 'php:Y'],
            [['institution', 'graduation_year', 'certificated', 'degree', 'specialty'], 'required'],
        ];
    }
    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'institution'     => Module::t('education', 'ATTR__INSTITUTION__LABEL'),
            'graduation_year' => Module::t('education', 'ATTR__GRADUATION_YEAR__LABEL'),
            'certificated'    => Module::t('education', 'ATTR__CERTIFICATED__LABEL'),
            'degree'          => Module::t('education', 'ATTR__DEGREE__LABEL'),
            'specialty'       => Module::t('education', 'ATTR__SPECIALTY__LABEL'),
        ];
    }

    public static function exportColumnsConfig($attribute)
    {
        return [
            [
                'attribute' => $attribute,
                'label' => Module::t('education', 'MODEL_NAME_PLURAL'),
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Employee $model */
                    $items = array_map(function($eModel) {
                        /** @var Education $eModel */
                        $data = [
                            $eModel->institution,
                            $eModel->graduation_year,
                            $eModel->certificated,
                            $eModel->degree,
                            $eModel->specialty,
                        ];
                        return implode(': ', $data);
                    }, (array) $model->educations);
                    $itemsInline = implode("\n", $items);
                    if (empty($items)) {
                        return null;
                    }
                    return $itemsInline;
                }
            ],
        ];
    }
}