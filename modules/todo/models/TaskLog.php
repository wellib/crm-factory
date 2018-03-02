<?php

namespace app\modules\todo\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\modules\todo\Module;
use app\modules\accounts\models\User;
use yii\helpers\Html;
use yii\web\UploadedFile;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\Menu;

/**
 * This is the model class for collection "todo_task_log".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property integer $id
 * @property mixed $task_id
 * @property mixed $type
 * @property mixed $_data
 * @property mixed $_attached_files
 * @property mixed $_user
 * @property mixed $comment
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @property UploadedFile[] $attachedFilesUpload
 *
 * @property User $user
 * @property Task $task
 */
class TaskLog extends \yii\mongodb\ActiveRecord
{

    const NOTIFY_TASK_CREATED                      = 'notify_task_created'; // задача создана
    const NOTIFY_TASK_UPDATED                      = 'notify_task_updated'; // задача обновлена + данный которые обновились
    
    const NOTIFY_TASK_AWAITING_APPROVAL            = 'notify_task_awaiting_approval'; // ожидает согласования
    const NOTIFY_USER_APPROVED_TASK                = 'notify_user_approved_task'; // пользователь ОДОБРИЛ выполнение задачи(утвердил/согласовал)
    const NOTIFY_USER_DISAPPROVED_TASK             = 'notify_user_disapproved_task'; // пользователь НЕ ОДОБРИЛ выполнение задачи(не утвердил/ несогласовал)
    const NOTIFY_TASK_DENIED_APPROVAL              = 'notify_task_denied_approval'; // Задача не прошла согласование
    const NOTIFY_AGAIN_APPROVE                     = 'notify_again_approve'; // задача отправлена на повторное согласование
    const NOTIFY_TASK_APPROVED                     = 'notify_task_approved'; // задача утверждена(прошла согласование)
    
    const NOTIFY_TASK_AWAITING_EXECUTION           = 'notify_task_awaiting_execution'; // Ожидает начала работы
    const NOTIFY_USER_STARTED_PERFORM              = 'notify_user_started_perform'; // Исполнитель приступил к выполнению

    const NOTIFY_SET_STATUS_IN_PROGRESS            = 'notify_set_status_in_progress'; // Установлен статус в работе

    const NOTIFY_USER_FINISHED_PERFORM             = 'notify_user_finished_perform'; // Исполнитель завершил выполнение задачи
    const NOTIFY_USER_FINISHED_PERFORM_CONTROL     = 'notify_user_finished_perform_control'; // Контролирующий юзера завершил контроль и считает что задача выполнена
    const NOTIFY_USER_BACK_PERFORM                 = 'notify_user_back_perform'; // Исполнитель вернулся к работе
    const NOTIFY_USER_BACK_PERFORM_CONTROL         = 'notify_user_back_perform_control'; // Контролирующий юзер вернулся к работе над задачей


    const NOTIFY_SET_STATUS_AWAITING_CHECK_RESULTS = 'notify_set_status_awaiting_check_results'; // Работа завершена, задача ожидает проверки результата
    const NOTIFY_USER_APPROVED_RESULTS             = 'notify_user_approved_results'; // Пользователь подвердил(принял) результата выполненной задачи
    const NOTIFY_USER_DISAPPROVED_RESULTS          = 'notify_user_disapproved_results'; // Пользователь НЕ подвердил(НЕ принял) результата выполненной задачи
    const NOTIFY_SET_STATUS_DISAPPROVE_RESULTS     = 'notify_set_status_disapprove_results'; // Результаты не приняты, задча не выполнена
    const NOTIFY_SET_STATUS_DONE                   = 'notify_set_status_done'; // Задача выполнена

    const COMMENT = 'comment';

    const SCENARIO_COMMENT = 'comment';

    /**
     * @var UploadedFile[]
     */
    public $attachedFilesUpload;

    const ATTACHED_FILES_UPLOAD_MAX_FILES = 10;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'todo_task_log';
    }


    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'id',
            'task_id',
            'type',
            '_data',
            '_attached_files',
            '_user',
            'comment',
            'created_at',
            'updated_at',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['comment', 'attachedFilesUpload'],
            self::SCENARIO_COMMENT => ['comment', 'attachedFilesUpload'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'type', '_data', '_user', 'created_at', 'updated_at', 'comment'], 'safe'],
            [['_attached_files'], 'safe'],
            [
                ['attachedFilesUpload'],
                'file',
                'skipOnEmpty' => true,
                'maxFiles'    => self::ATTACHED_FILES_UPLOAD_MAX_FILES,
                'maxSize'     => 1024 * 1024 * 20
            ],
            [['comment'], 'required', 'on' => self::SCENARIO_COMMENT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Module::t('task_log', 'ID'),
            'task_id' => Module::t('task_log', 'Task ID'),
            'type' => Module::t('task_log', 'Type'),
            '_data' => Module::t('task_log', 'Data'),
            'comment' => $this->getScenario() == self::SCENARIO_DEFAULT ? Module::t('task_log', 'ATTR__REASON__LABEL') : Module::t('task_log', 'ATTR__COMMENT__LABEL'),
            'attachedFilesUpload' => Module::t('task_log', 'ATTR__ATTACHEDFILESUPLOAD__LABEL'),
            'created_at' => Module::t('task_log', 'Created At'),
            'updated_at' => Module::t('task_log', 'Updated At'),
        ];
    }

    /**
     * @return TaskQuery
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['_id' => '_user']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['_id' => 'task_id']);
    }


    public function getComment()
    {
        return nl2br($this->comment);
    }

    /**
     * @param Task $task
     * @param mixed $type
     * @param mixed $user_id
     * @param array $data
     * @return bool
     */
    public static function createNotify($task, $type, $user_id = null, $data = [])
    {
        if (($task instanceof Task) === false) {
            return false;
        }
        $model = new self();
        $model->task_id = $task->_id;
        $model->type = $type;
        $model->_data = $data;
        $model->_user = $user_id;
        return $model->save(false);
    }

    public function getCreatedAtFormat()
    {
        return Yii::$app->formatter->asDatetime($this->created_at);
    }

    public function beforeSave($insert)
    {
        //if ($this->isNewRecord) {
        //    $this->id = self::find()->
        //}
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    /**
     * Загрзука файлов
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function uploadAttachedFiles()
    {
        if ($this->validate(['attachedFilesUpload'])) {
            foreach ($this->attachedFilesUpload as $file) {
                $uploadPath = Module::getInstance()->getProtectedFilesUploadPath(true);
                $fileDateTime = '';
                if(($fileTimestamp = filectime($file->tempName)) !== false) {
                    $fileDateTime = date('Y-m-d_h-i-s') . '_';
                }
                $fileNameOrig = $fileDateTime . $file->name;
                $fileName = Inflector::slug($file->name) . '-'. uniqid() . '.' . $file->extension;
                if ($file->saveAs($uploadPath . $fileName)) {
                    $_attached_files = $this->_attached_files;
                    $_attached_files[] = [
                        'filename_orig' => $fileNameOrig,
                        'filename'      => $fileName,
                    ];
                    $this->setAttribute('_attached_files', $_attached_files);
                }
            }
            return true;
        } else {
            return false;
        }
    }


    /**
     * Список ссылок на файлы
     *
     * Возвращает key=>value массив где key - это название файла, а value url для скачивания(просмотра) файла
     *
     * @return array
     */

    public function getAttachedFilesLinks()
    {
        $list = [];
        if (is_array($this->_attached_files)) {
            foreach ($this->_attached_files as $file) {
                $list[$this->getAttachedFileUrl($file['filename'])] = $file['filename_orig'];
            }
        }
        return $list;
    }

    /**
     * Возвращает ссылку на скачивание файла
     *
     * @param $filename название файла
     * @param bool $scheme если true то ссылка будет абслютной (с http://site.com/)
     * @return string
     */
    protected function getAttachedFileUrl($filename, $scheme = false)
    {
        return Url::to(['/todo/task/download-attached-file', 'id' => (string) $this->_id, 'filename' => $filename, 'type' => 'log'], $scheme);
    }

    /**
     * Список файлов и пути где они лежата на хостинге(сервере)
     *
     * Возвращает key=>value массив где key - это название файла, а value абсолютный путь расположения файла в на хостинге(сервере)
     *
     * @return array
     */
    public function getAttachedFilesPaths()
    {
        $list = [];
        if (is_array($this->_attached_files)) {
            foreach ($this->_attached_files as $file) {
                //$list[$filename] = $this->getAttachedFileSystemPath($filename);
                $list[$file['filename']] = [
                    'filename_orig' => $file['filename_orig'],
                    'file_path' => $this->getAttachedFileSystemPath($file['filename']),
                ];
            }
        }
        return $list;
    }

    /**
     * Возвращает путь где находится файла в системе
     *
     * @param $filename имя файла
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAttachedFileSystemPath($filename)
    {
        return Module::getInstance()->getProtectedFilesUploadPath(true) . $filename;
    }

    public function getDiffTaskModelDataAfterUpdate()
    {
        if (!isset($this->_data['oldModelData']) || !isset($this->_data['newModelData'])) {
            return [];
        }
        
        $diff = [];
        foreach ($this->_data['oldModelData'] as $attributeName => $attributeValue) {
            if ($attributeValue != $this->_data['newModelData'][$attributeName]) {
                switch ($attributeName) {
                    case 'priority':
                        $diff[] = [
                            'label' => $this->task->getAttributeLabel($attributeName),
                            'oldValue' => Task::priorityLabel($this->_data['oldModelData'][$attributeName]),
                            'newValue' => Task::priorityLabel($this->_data['newModelData'][$attributeName]),
                        ];
                        break;
                    case '_attached_files':
                        //var_dump($this->_data['oldModelData'][$attributeName]);
                        //die();
                        $diff[] = [
                            'label' => $this->task->getAttributeLabel($attributeName),
                            'oldValue' => is_array($this->_data['oldModelData'][$attributeName]) && count($this->_data['oldModelData'][$attributeName]) > 0 ? Menu::widget([
                                'options' => ['style' => 'padding: 0;'],
                                'items' => array_map(function($file) {
                                    return [
                                        'label' => $file['filename_orig'],
                                    ];
                                }, $this->_data['oldModelData'][$attributeName])
                            ]) : '<i>Не указано</i>',
                            'newValue' => is_array($this->_data['newModelData'][$attributeName]) && count($this->_data['newModelData'][$attributeName]) > 0 ? Menu::widget([
                                'options' => ['style' => 'padding: 0;'],
                                'items' => array_map(function($file) {
                                    return [
                                        'label' => $file['filename_orig'],
                                    ];
                                },  $this->_data['newModelData'][$attributeName])
                            ]) : '<i>Не указано</i>',
                        ];
                        break;
                    case 'deadline_timestamp':
                        //var_dump(gettype($this->_data['oldModelData'][$attributeName]));
                        //var_dump($this->_data['newModelData'][$attributeName]);
                        $diff[] = [
                            'label' => $this->task->getAttributeLabel($attributeName),
                            'oldValue' => date('d.m.Y H:i', (int)$this->_data['oldModelData'][$attributeName]),
                            'newValue' => date('d.m.Y H:i', (int)$this->_data['newModelData'][$attributeName]),
                        ];
                        break;
                    case '_users_performers':
                    case '_users_approve_execute':
                    case '_users_notify_after_finished':
                    case '_users_control_results':
                        $diff[] = [
                            'label' => $this->task->getAttributeLabel($attributeName),
                            'oldValue' => self::userList($this->_data['oldModelData'][$attributeName]),
                            'newValue' => self::userList($this->_data['newModelData'][$attributeName]),
                        ];
                    default:
                }
            }
        }
        return $diff;
    }

    public static function userList($users)
    {
        $list = [];
        if(is_array($users) && count($users) > 0) {
            foreach ($users as $userId) {
                $model = User::find()->where(['_id' => $userId])->one();
                if ($model) {
                    $list[] = $model->getNameAndPosition();
                }
            }
        } else {
            return '<i>Не указано</i>';
        }
        return implode(', ', $list);
    }
}
