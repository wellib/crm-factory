<?php

namespace app\modules\hr\models;

use Yii;
use yii\base\InvalidParamException;
use yii\mongodb\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Employee]].
 *
 * @see Employee
 */
class EmployeeQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return Employee[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Employee|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    public function searchByFullName($name)
    {
        $subQuery = Employee::find();
        $subQuery->filterWhere(['like', 'full_name', $name]);
        $subQuery->orFilterWhere(['like', 'last_name_with_initials', $name]);
        return $this->andWhere($subQuery->where);
    }

    /**
     * @param array $ids
     * @return EmployeeQuery
     */
    public function structureDepartment($ids)
    {
        if (!is_array($ids)) {
            throw new InvalidParamException('$ids must be array');
        }
        return $this->andWhere(['in', '_company_card._structure_department', $ids]);
    }
}
