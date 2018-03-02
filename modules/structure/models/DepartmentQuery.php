<?php

namespace app\modules\structure\models;

use MongoDB\BSON\ObjectID;

use yii\mongodb\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Department]].
 *
 * @see Department
 */
class DepartmentQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return Department[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Department|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param string|ObjectID $id
     * @return DepartmentQuery
     */
    public function id($id)
    {
        //var_dump($id);
        //die();
        if (($id instanceof ObjectID) === false) {
            $id = new ObjectID((string) $id);
        }
        return $this->andWhere(['_id' => $id]);
    }
}
