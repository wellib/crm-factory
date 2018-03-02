<?php

return [
    'MODEL_NAME' => 'Документооборот',
    'MODEL_NAME_PLURAL' => 'Документооборот',

    'ATTR__ID__LABEL' => 'ID',
    'ATTR__STATUS__LABEL' => 'Статус',

    'ATTR__SUBJECT__LABEL' => 'Название',
    'ATTR__DESCRIPTION__LABEL' => 'Описание служебки',

    'ATTR__PRIORITY__LABEL' => 'Приоритет',

    'PRIORITY__LOW'     => 'Низкий',
    'PRIORITY__MEDIUM'  => 'Средний',
    'PRIORITY__HIGH'    => 'Высокий',

    'PRIORITY__UNKNOWN' => 'Неизвестно',
    'PRIORITY__ANY'     => 'Любой',

    'ATTR__DEADLINE_TYPE__LABEL' => 'Тип служебки',
    'DEADLINE_TYPE__ONE_TIME'    => 'Разовая',
    'DEADLINE_TYPE__EVERY_DAY'   => 'Каждый день',
    'DEADLINE_TYPE__EVERY_WEEK'  => 'Каждую неделю',
    'DEADLINE_TYPE__EVERY_MONTH' => 'Каждый месяц',
    'DEADLINE_TYPE__EVERY_DATE'  => 'В определенные даты',

    'ATTR__DEADLINE_EVERY_WEEK__LABEL'  => 'Дни недели по которым нужно выполнять служебку',
    'ATTR__DEADLINE_EVERY_MONTH__LABEL' => 'Дни месяца по которым нужно выполнять служебку',
    'ATTR__DEADLINE_EVERY_DATE__LABEL'  => 'Даты по которым нужно выполнять служебку',

    //'ATTR__PERFORM_DATE__LABEL'       => 'Дата до которой нужно выполнит служебку',
    'ATTR__PERFORM_DATE__LABEL'       => 'Начать выполнение',
    'ATTR__PERFORM_TIME__LABEL'       => 'Время до которого нужно выполнить служебку',
    'ATTR__PERFORM_TIMESTAMP__LABEL'  => 'Начать выполнение с',

    //'ATTR__DEADLINE_DATE__LABEL'       => 'Дата до которой нужно выполнит служебку',
    'ATTR__DEADLINE_DATE__LABEL'       => 'Завершить выполнение',
    'ATTR__DEADLINE_TIME__LABEL'       => 'Время до которого нужно выполнить служебку',
    'ATTR__DEADLINE_TIMESTAMP__LABEL'  => 'Завершить выполнение до',

    //'ATTR__START_DATE__LABEL'       => 'Дата с которой нужно начать выполнение служебки',
    'ATTR__START_DATE__LABEL'       => 'Начать выполнение с',
    //'ATTR__START_TIME__LABEL'       => 'Время с которого нужно начать выполнение служебки',
    'ATTR__START_TIMESTAMP__LABEL'  => 'Начать выполнение с',

    //'ATTR__END_DATE__LABEL'      => 'Дата завершения выполнения цикла',
    'ATTR__END_DATE__LABEL'      => 'Дата завершения выполнения цикла',
    //'ATTR__END_TIME__LABEL'      => 'Время завершения выполнения цикла',
    'ATTR__END_TIMESTAMP__LABEL' => 'Завершить выполнение цикла',

    'ATTR__DEADLINE_TIMESTAMP_APPROVE_EXECUTE__LABEL' => 'Акцептировать выполнение до',
    'ATTR__DEADLINE_TIMESTAMP_CHECK_RESULTS__LABEL'   => 'Акцептировать результат до',

    'ATTR__ATTACHED_FILES__LABEL'        => 'Прикрепленные файлы',
    'ATTR__ATTACHED_FILES_UPLOAD__LABEL' => 'Прикрепить файлы',

    'ATTR__USERS_APPROVE_EXECUTE__LABEL'          => 'Акцепторы служебки',
    'ATTR__USERS_APPROVE_EXECUTE_RESPONSE__LABEL' => 'Акцептировали выполнение',

    'ATTR__USERS_PERFORMERS__LABEL'          => 'Исполнители',
    'ATTR__USERS_PERFORMERS_FINISHED__LABEL' => 'Исполнители завершившие выполнение служебки',

    'ATTR__USERS_CHECK_RESULT__LABEL'          => 'Акцепторы результата',
    'ATTR__USERS_CHECK_RESULT_RESPONSE__LABEL' => 'Акцептировали результат',

    'ATTR__USERS_NOTIFY_AFTER_FINISHED__LABEL' => 'Уведомить о выполнении',


    'ATTR__CREATED_AT__LABEL' => 'Создана',
    'ATTR__UPDATED_AT__LABEL' => 'Последнее изменение',







    //'ATTR__DEADLINE__LABEL' => 'Дата завершения',
    //'ATTR__DEADLINE_TIME__LABEL' => 'Время завершения',
    //'ATTR__LAST_DEADLINE__LABEL' => 'Дата окончания цикла',
    //'ATTR__LAST_DEADLINE_TIME__LABEL' => 'Время окончания цикла',
    //'ATTR__LAST_DEADLINE__HINT' => 'Дата после которой служебка перестанет циклически выполняться',
    //
    //'ATTR__DEADLINE_FORMAT__VALIDATE_MESSAGE__BAD_DATE' => 'Дата должна быть в формате {format}',





    //'ATTR__TYPE__LABEL'   => 'Период выполнения',





    'ATTR__ATTACHED_FILES__LABEL' => 'Прикрепленные файлы',
    'ATTR__ATTACHED_FILES_UPLOAD__LABEL' => 'Прикрепить файлы',
    'ATTR__AUTHOR__LABEL' => 'Автор',
    //
    ////'ATTR__USERS_APPROVE_EXECUTE__LABEL' => 'Акцепторы служебки',
    ////'ATTR__USERS_APPROVE_EXECUTE_ANSWERS__LABEL' => 'Дали свой ответ по согласованию',
    //
    ////'ATTR__USERS_PERFORMERS__LABEL' => 'Исполнители',
    'ATTR__USERS_PERFORMERS__HINT' => 'Если оставить поле пустым то по умолчанию исполнителем становиться автор служебки',
    //'ATTR__USERS_PERFORMERS_EXECUTE__LABEL' => 'Подтвердили выполнение',
    //
    //'ATTR__USERS_CONTROL_EXECUTION__LABEL' => 'Контроль выполнения',
    //'ATTR__USERS_APPROVED_FINISHED_PERFORM__LABEL' => 'Подтвердили что служебка выполнена',
    //
    'ATTR__USERS_CONTROL_RESULTS__LABEL' => 'Акцепторы результата',
    'ATTR__USERS_CONTROL_RESULTS__HINT' => 'По умолчанию автор служебки всегда акцептирует результат',
    ////'ATTR__USERS_CONTROL_RESULTS_ANSWERS__LABEL' => 'Проконтролировали результат',
    //
    //'ATTR__USERS_NOTIFY_AFTER_FINISHING__LABEL' => 'Уведомить о выполнении',
    //
    //
    //
    //
    'CREATE__FORM__SUBMIT_BTN' => 'Создать',
    'UPDATE__FORM__SUBMIT_BTN' => 'Сохранить изменения',

    'CREATE__PAGE__TITLE' => 'Создание служебной записки',
    'UPDATE__PAGE__TITLE' => 'Изменение',

    'CREATE___LINK__LABEL' => 'Добавить',
    'UPDATE___LINK__LABEL' => 'Изменить',
    'DELETE___LINK__LABEL' => 'Удалить',
    'DELETE___LINK__CONFIRM_MESSAGE' => 'Вы действительно хотите удалить данную служебку?',

    'HTTP_ERROR__NOT_FOUND' => 'Ой :( Такой служебки не существует, возможно она была удалена!',
    //
    //
    'INBOX'             => 'Мне',
    'INBOX_IN_PROGRESS' => 'В работе',
    'INBOX_OVERDUE'     => 'Просроченные',
    'INBOX_CHECK'       => 'На проверке',
    'INBOX_DONE'        => 'Выполненные',


    'OUTBOX'           => 'От меня',
    'OUTBOX_APPROVING' => 'На акцептации',
    'OUTBOX_PERFORMED' => 'В работе',
    'OUTBOX_EXPIRED'   => 'Просроченные',
    'OUTBOX_DONE'      => 'Выполненные',
    'OUTBOX_CHECK'     => 'Необходимо проверить',
    //
    //
    ////'AWAITING_EXECUTION' => 'Ожидают начала работы',
    ////'IN_PROGRESS' => 'Выполняются',
    ////'CONTROL_EXECUTION' => 'Контроль выполнения',
    ////'AWAITING_APPROVAL' => 'Ожидают согласования',
    ////'AWAITING_CHECK_RESULTS' => 'Ожидают проверки результата',
    ////'DONE' => 'Выполнены',
    //
    //

    /**
     * Статусы
     */
    'STATUS__APPROVAL_AWAITING' => 'Ожидает акцептации',
    'STATUS__APPROVAL_AWAITING__SHORT' => 'На согласовании',
    'STATUS__APPROVAL_FAILED' => 'служебка не прошла акцептацию',
    'STATUS__APPROVAL_FAILED__SHORT' => 'Не прошла акцептацию',

    'STATUS__IN_PROGRESS' => 'Выполняется (в работе)',
    'STATUS__IN_PROGRESS__SHORT' => 'Выполняется',

    'STATUS__CHECK_RESULTS_AWAITING' => 'Ожидает акцептации результата выполнения',
    'STATUS__CHECK_RESULTS_AWAITING__SHORT' => 'Проверка результата',
    'STATUS__CHECK_RESULTS_FAILED' => 'Результата выполненной служебки не принят акцепторами',
    'STATUS__CHECK_RESULTS_FAILED__SHORT' => 'Результат не принят',

    'STATUS__DONE' => 'служебка выполнена',
    'STATUS__DONE__SHORT' => 'Выполнена',

    'STATUS__UNKNOWN' => 'Неизвестно',
    'STATUS__UNKNOWN__SHORT' => 'Неизвестно',
    
    //
    //
    ////'ATTR__DEADLINE_TYPE__LABEL' => 'Тип служебки',
    ////'DEADLINE_TYPE_ONE_TIME'    => 'Разовая',
    ////'DEADLINE_TYPE_EVERY_DAY'   => 'Каждый день',
    ////'DEADLINE_TYPE_EVERY_WEEK'  => 'Каждую неделю',
    ////'DEADLINE_TYPE_EVERY_MONTH' => 'Каждый месяц',
    ////'DEADLINE_TYPE_REPEATS'     => 'Периодичность',
    //
    //'ATTR__DEADLINE_REPEATS_NUMBER__LABEL' => 'Кол-во повторений',
    //'ATTR__DEADLINE_REPEATS_COUNTER__LABEL' => 'Выполнено повторений',
    //
    //
    'ATTR__APPROVE_EXECUTE_DEADLINE_TIMESTAMP__LABEL' => 'Акцептировать выполнение до',
    'ATTR__CHECK_RESULTS_DEADLINE_TIMESTAMP__LABEL' => 'Акцептировать результат до',
    //
    'USER_ROLES_IN_TASK' => 'Роли других сотрудников в служебке',
    //
    //
    'TASK_SEARCH__ATTR__USERS__LABEL' => 'Сотрудники',
];
