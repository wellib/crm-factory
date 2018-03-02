<?php

namespace app\modules\todo\task\actions\interfaces;

interface ActionInterface
{
    /**
     * Название действия
     * @return string
     */
    public function getName();

    /**
     * Проверка доступности действивя (проверка доступа)
     * @return boolean
     */
    public function isAvailable();

    /**
     * Выполнение действия
     */
    public function runAction();

    /**
     * Рендер управляющих элементов (кнопок например)
     * @return mixed
     */
    public function renderControls();
}