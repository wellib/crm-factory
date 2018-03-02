<?php

namespace app\modules\todo\commands;

use app\modules\accounts\models\User;
use app\modules\todo\models\Task;
use Yii;
use yii\helpers\Console;


class TaskController extends \yii\console\Controller
{

    public function actionEmailNotify()
    {
        $users = User::find()->all();
        foreach ($users as $user) {
            /**
             * 1. Сделать выборку задач которые нужно согласовать
             */
        }
    }
    
    public function actionCreateClones()
    {
        //var_dump(Yii::getAlias('@app/modules/todo/messages'));
        foreach (Task::find()->template()->each() as $model) {
            /** @var Task $model */
            $model->createNextTask();
        }
    }
}
