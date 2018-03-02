<?php

namespace app\modules\canteen\components;

use app\exceptions\ModelErrorException;
use app\modules\canteen\models\Option;
use Yii;
use yii\base\Component;

class Canteen extends Component
{
    const CANTEEN_IS_OPEN = 'canteen_is_open';

    public function init()
    {
        parent::init();

        $option = Option::findOne(['code' => self::CANTEEN_IS_OPEN]);
        if ($option === null) {
            $option = new Option(['code' => self::CANTEEN_IS_OPEN]);
            $option->value = '1';
            if (!$option->save()) {
                throw new ModelErrorException($option);
            }
        }
    }

    public function isOpen()
    {
        $option = Option::findOne(['code' => self::CANTEEN_IS_OPEN]);
        return (bool)$option->value;
    }

    public function close()
    {
        $option = Option::findOne(['code' => self::CANTEEN_IS_OPEN]);
        $option->value = '0';
        if (!$option->save()) {
            throw new ModelErrorException($option);
        }
    }

    public function open()
    {
        $option = Option::findOne(['code' => self::CANTEEN_IS_OPEN]);
        $option->value = '1';
        if (!$option->save()) {
            throw new ModelErrorException($option);
        }
    }

    public function modeOrderOnly()
    {
        Yii::$app->session->set('modeOrderOnly', true);
    }

    public function isOrderOnly()
    {
        return Yii::$app->session->get('modeOrderOnly', false);
    }
}