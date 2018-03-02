<?php

return [
    'MODEL_NAME' => 'Приказ',
    'MODEL_NAME_PLURAL' => 'Журнал приказов',

    'ATTR__ID__LABEL' => 'ID',

    'ATTR__CREATED_AT__LABEL' => 'Добавлен',
    'ATTR__UPDATED_AT__LABEL' => 'Последнее изменение',

    'ATTR__TYPE__LABEL'          => 'Тип',
    'TYPE__HIRING__LABEL'        => 'Прием на работу',
    'TYPE__FIRED__LABEL'         => 'Увольнение',
    'TYPE__BUSINESS_TRIP__LABEL' => 'Командировка',
    'TYPE__VACATION__LABEL'      => 'Отпуск',
    'TYPE__UNKNOWN'              => 'Неизвестный тип',

    'ATTR__NUMBER__LABEL' => 'Номер',
    'ATTR__NOTE__LABEL'   => 'Примечание',
    
    'ATTR__DATE__LABEL'   => 'Дата составления',
    'ATTR__DATE__VALIDATE_MESSAGE__BAD_DATE' => 'Дата составления должна быть в формате {format}',

    'ATTR__EMPLOYEES__LABEL' => 'Сотрудники',


    'CREATE__FORM__SUBMIT_BTN' => 'Добавить приказ',
    'UPDATE__FORM__SUBMIT_BTN' => 'Сохранить изменения',

    'CREATE__PAGE__TITLE' => 'Добавление приказа',
    'UPDATE__PAGE__TITLE' => 'Изменение приказа',

    'CREATE___LINK__LABEL' => 'Добавить приказ',
    'UPDATE___LINK__LABEL' => 'Изменить',
    'DELETE___LINK__LABEL' => 'Удалить',
    'DELETE___LINK__CONFIRM_MESSAGE' => 'Вы действительно хотите удалить данный пркиаз?',

    'HTTP_ERROR__NOT_FOUND' => 'Ой :( Такого приказа не существует, возможно он был удален или еще не создан!',
];
