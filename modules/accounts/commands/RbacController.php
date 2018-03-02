<?php

namespace app\modules\accounts\commands;

use Yii;
use yii\helpers\Console;


class RbacController extends \yii\console\Controller
{
    const ROLE_NAME = 'accounts';
    const ROLE_DESCRIPTION = 'Учетные записи';
    
    /**
     * Add roles (init module roles)
     */
    public function actionAdd()
    {
        $role = Yii::$app->authManager->getRole(self::ROLE_NAME);
        if ($role) {
            $this->printRoleExist(self::ROLE_DESCRIPTION);
            return 1;
        }
        $role = Yii::$app->authManager->createRole(self::ROLE_NAME);
        $role->description = self::ROLE_DESCRIPTION;
        Yii::$app->authManager->add($role);
        $this->printAddRole(self::ROLE_DESCRIPTION);
    }

    /**
     * Remove roles (destroy module roles)
     */
    public function actionRemove()
    {
        $role = Yii::$app->authManager->getRole('hr');
        if ($role) {
            Yii::$app->authManager->remove($role);
            $this->printRemoveRole(self::ROLE_DESCRIPTION);
        } else {
            $this->printRoleNotExist(self::ROLE_DESCRIPTION);
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
