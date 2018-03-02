<?php

return [
    'MODEL_NAME'        => 'Слово из словаря',
    'MODEL_NAME_PLURAL' => 'Слова из словаря',


    'ATTR__ID__LABEL'         => 'id',
    'ATTR__DICTIONARY__LABEL' => 'Словарь',
    'ATTR__WORD__LABEL'       => 'Слово',
    'ATTR__CREATED_AT__LABEL' => 'Добавлено',
    'ATTR__UPDATED_AT__LABEL' => 'Изменено',

    'DICTIONARY_ISSUING_AUTHORITY' => 'Орган выдачи',
    'DICTIONARY_NATIONALITY'       => 'Национальность',
    'DICTIONARY_MARITAL_STATUS'    => 'Семейное положение',
    'DICTIONARY_UNKNOWN'           => 'Неизвестный словарь',
    'DICTIONARY_CONTACT_TYPE'      => 'Тип контакта',
    'DICTIONARY_KINSHIP'           => 'Степень родства',
    'DICTIONARY_DISMISSAL_REASON'  => 'Причина увольнения',
    'DICTIONARY_EMPLOYMENT_TERM'   => 'Характер работы',
    'DICTIONARY_BASE_DISMISSAL'    => 'Основание увольнения',

    // for modules/hr/validators/DictionaryWordValidator.php
    'VALIDATOR__ID_DOES_NOT_EXIST_IN_DATABASE' => 'Указнное значение не найдено в базе',
    'VALIDATOR__NO_RELATION_TO_DICTIONARY'     => 'Указанное значение не является значением словара "{dictionary}"',

];
