<?php

class m160622_020110_create_rbac extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('accounts_rbac_item');
        $this->createCollection('accounts_rbac_rule');
        $this->createCollection('accounts_rbac_assignment');
    }

    public function down()
    {
        $this->dropCollection('accounts_rbac_item');
        $this->dropCollection('accounts_rbac_rule');
        $this->dropCollection('accounts_rbac_assignment');
    }
}
