<?php

namespace app\modules\accounts\models;

use MongoDB\BSON\ObjectID;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends \yii\mongodb\ActiveQuery
{
    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function findByLogin($login)
    {
        return $this->andWhere(['or', ['nickname' => $login]]);
    }

    /**
     * @param $id
     * @return $this
     */
    public function id($id)
    {
        if (($id instanceof ObjectID) === false && is_string($id)) {
            $id = new ObjectID((string) $id);
        }
        return $this->andWhere(['_id' => $id]);
    }
}