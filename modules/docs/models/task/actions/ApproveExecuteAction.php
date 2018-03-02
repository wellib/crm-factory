<?php

namespace app\modules\docs\task\actions;

use yii\base\Object;
use app\modules\docs\task\actions\interfaces\ActionInterface;
use app\modules\docs\models\Task;

/**
 * Class ApproveExecuteAction
 *
 * @property Task $_owner
 *
 * @package app\modules\docs\task\actions
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