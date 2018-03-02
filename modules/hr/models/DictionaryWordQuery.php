<?php

namespace app\modules\hr\models;

use Yii;
use yii\base\InvalidValueException;
use yii\mongodb\ActiveQuery;
use MongoDB\BSON\ObjectID;

/**
 * This is the ActiveQuery class for [[EmploDictionaryWordyee]].
 *
 * @see DictionaryWord
 */
class DictionaryWordQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return DictionaryWord[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DictionaryWord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }


    /**
     * Выборка по словарю
     * @param integer $dictionary Идекнтификатор словаря
     * @return $this
     */
    public function dictionary($dictionary)
    {
        if (!is_integer($dictionary)) {
            throw new InvalidValueException('$dictionary must be set integer value');
        }
        return $this->andWhere(['dictionary' => $dictionary]);
    }

    /**
     * @param ObjectID|string $id
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
