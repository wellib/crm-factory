<?php

namespace app\modules\todo\models;

use Yii;

/**
 * This is the ActiveQuery class for [[Settings]].
 *
 * @see Settings
 */
class SettingsQuery extends \yii\mongodb\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Task[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Task|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Выборка по ключу
     *
     * @param mixed $key
     * @return SettingsQuery
     */
    public function key($key)
    {
        return $this->andWhere(['key' => $key]);
    }
}