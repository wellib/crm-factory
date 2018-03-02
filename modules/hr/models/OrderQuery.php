<?php

namespace app\modules\hr\models;

use Yii;
use yii\base\InvalidValueException;
use yii\mongodb\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null|integer|string $from unixtime or valid string (example "01.07.2017")
     * @param null|integer|string $to unixtime or valid string (example "12.09.2017")
     * @return OrderQuery
     */
    public function dateFromTo($from = null, $to = null)
    {
        $subQuery = Order::find();
        if (!empty($from) && $from !== null) {
            if (!is_int($from)) {
                if (($from = strtotime($from)) === false) {
                    throw new InvalidValueException('$from value must be null, integer or valid date string(example "01.07.2017") ');
                }
            }
            $subQuery->andWhere(['>=', 'date_unixtime', $from]);
        }
        if (!empty($to) && $to !== null) {
            if (!is_int($to)) {
                if (($to = strtotime($to)) === false) {
                    throw new InvalidValueException('$to value must be null, integer or valid date string(example "01.07.2017") ');
                }
            }
            $subQuery->andWhere(['<=', 'date_unixtime', $to]);
        }
        return $this->andWhere($subQuery->where);
    }
}
