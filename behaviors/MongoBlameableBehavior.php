<?php

namespace app\behaviors;

use MongoDB\BSON\ObjectID;
use yii\behaviors\BlameableBehavior;

class MongoBlameableBehavior extends BlameableBehavior
{
    protected function getValue($event)
    {
        $value = parent::getValue($event);
        if ($value) {
            return new ObjectID($value);
        }

        return null;
    }
}