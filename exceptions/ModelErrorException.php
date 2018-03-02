<?php

namespace app\exceptions;

use yii\base\Exception;
use yii\base\Model;

class ModelErrorException extends Exception
{
    public function getName()
    {
        return 'ModelErrorException';
    }
    
    /**
     * ModelErrorException constructor.
     * @param Model $model
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($model, $code = 0, Exception $previous = null)
    {
        parent::__construct(print_r([$model->getErrors(), $model->attributes], true));
    }
}