<?php

namespace app\modules\todo\task\actions;

use yii\base\Object;
use app\modules\todo\task\actions\interfaces\ActionInterface;
use app\modules\todo\models\Task;

/**
 * Class ApproveExecuteAction
 *
 * @property Task $_owner
 *
 * @package app\modules\todo\task\actions
 */
class ApproveExecuteAction extends Object implements ActionInterface
{
    const ACTION_NAME = 'approve-execute';

    /**
     * @var Task
     */
    protected $_owner;

    public function __construct($owner)
    {
        $this->_owner = $owner;
    }

    public function getName()
    {
        return self::ACTION_NAME;
    }


    public function isAvailable()
    {
        return true;
    }

    public function runAction()
    {
        return true;
    }

    public function renderControls()
    {
        return 'ok';
    }
}