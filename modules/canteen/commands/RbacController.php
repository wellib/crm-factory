<?php

namespace app\modules\canteen\commands;

use Yii;

use yii\console\Controller;
use yii\helpers\Console;

use app\modules\canteen\Module;


class RbacController extends Controller
{

    /**
     * Add roles (init module roles)
     */
    public function actionAdd()
    {
        $role = Yii::$app->authManager->getRole(Module::ROLE_CANTEEN_ADMIN);
        if ($role) {
            $this->printRoleExist(Module::ROLE_CANTEEN_ADMIN_DESCRIPTION);
            return 1;
        }
        $role = Yii::$app->authManager->createRole(Module::ROLE_CANTEEN_ADMIN);
        $role->description = Module::ROLE_CANTEEN_ADMIN_DESCRIPTION;
        Yii::$app->authManager->add($role);
        $this->printAddRole(Module::ROLE_CANTEEN_ADMIN_DESCRIPTION);
    }

    /**
     * Remove roles (destroy module roles)
     */
    public function actionRemove()
    {
        $role = Yii::$app->authManager->getRole('hr');
        if ($role) {
            Yii::$app->authManager->remove($role);
            $this->printRemoveRole(Module::ROLE_CANTEEN_ADMIN_DESCRIPTION);
        } else {
            $this->printRoleNotExist(Module::ROLE_CANTEEN_ADMIN_DESCRIPTION);
        }

    }

    /**
     * Print message in console "Added role: %roleName%"
     * @param $role
     */
    protected function printAddRole($role)
    {
        $role = $this->ansiFormat($role, Console::FG_CYAN);
        echo $this->ansiFormat('Added role:', Console::FG_GREEN) . " $role\n";
    }

    /**
     * Print message in console "Removed role: %roleName%"
     * @param $role
     */
    protected function printRemoveRole($role)
    {
        $role = $this->ansiFormat($role, Console::FG_CYAN);
        echo $this->ansiFormat('Removed role:', Console::FG_RED) . " $role\n";
    }

    /**
     * Print message in console "Exist role: %roleName%"
     * @param $role
     */
    protected function printRoleExist($role)
    {
        $role = $this->ansiFormat($role, Console::FG_CYAN);
        echo $this->ansiFormat('Exist role:', Console::FG_GREEN) . " $role\n";
    }

    /**
     * Print message in console "Role not exist: %roleName%"
     * @param $role
     */
    protected function printRoleNotExist($role)
    {
        $role = $this->ansiFormat($role, Console::FG_CYAN);
        echo $this->ansiFormat('Role not exist:', Console::FG_RED) . " $role\n";
    }
}
