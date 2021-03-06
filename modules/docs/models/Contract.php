<?php

namespace app\modules\docs\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use app\modules\docs\Module;
use app\modules\accounts\models\User;

/**
 * This is the model class for collection "docs_contract".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $number
 * @property mixed $date
 * @property mixed $company
 * @property mixed $names
 * @property mixed $parent
 * @property mixed $files
 * @property mixed $description
 */
class Contract extends \yii\mongodb\ActiveRecord
{
  /**
     * @var UploadedFile[]
     */
    public $attachedFilesUpload;
    /**
     * Максимальное кол-во файлов которые можно загрузить
     */
    const ATTACHED_FILES_UPLOAD__MAX_FILES = 10;

  
  
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'docs_contract';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'id',
            'name',
            'number',
            'date',
            'date_timestamp',
            'company',
            'names',
            'parent',
            'files',
            '_author',
            'description',
            'status',
            'created_at',
            'updated_at',
            'approve',
            'alls',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        [['name', 'number', 'date',], 'required'],
        [
                ['attachedFilesUpload'],
                'file',
                'skipOnEmpty' => true,
                'maxFiles'    => self::ATTACHED_FILES_UPLOAD__MAX_FILES,
                'extensions'  => ['pdf'],
                'maxSize'     => 1024 * 1024 * 20, // 20 мегабайт
            ],
            [['name', 'id','number', 'date', 'date_timestamp', 'company', 'names', 'parent', 'files', 'description', 'status', '_author', 'created_at', 'updated_at', 'approve', 'alls'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'id' => 'ID',
            'name' => 'Наименование',
            'number' => '№ документа',
            'date' => 'Дата',
            'company' => 'Предприятие',
            'names' => 'Наименование контрагента',
            'parent' => 'Привязка',
            'files' => 'Прикрепленные файлы',
            'attachedFilesUpload' => 'Прикрепленные файлы',
            'description' => 'Примечание',
            'status' => 'Статус',
            '_author' => 'Кто добавил',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата обновления',
            'approve' => 'Дата утверждения',
            'alls' => 'Для всех сотрудников',
        ];
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
                    $files = $this->files;
                    $files[] = [
                        'filename_orig' => $fileNameOrig,
                        'filename'      => $fileName,
                    ];
                    $this->setAttribute('files', $files);
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
        if (is_array($this->files)) {
            foreach ($this->files as $file) {
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
        return Url::to(['/docs/contract/download-attached-file', 'id' => (string) $this->_id, 'filename' => $filename], $scheme);
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
        if (is_array($this->files)) {
            foreach ($this->files as $file) {
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

    public function getId()
    {
        return (string)$this->_id;
    }

    public function getCompanyName()
    {
      $comp =  \app\modules\docs\models\Company::find()->where(['_id' => $this->company])->one();
      if (isset($comp->name))
        return (string)$comp->name;
    }

    public function getAuthor()
    {
        $comp =  \app\modules\accounts\models\User::find()->where(['_id' => $this->_author])->one();
        return (string)$comp->name;
    }

    public function getParentContract()
    {
        $comp =  Contract::find()->where(['_id' => $this->parent])->one();
        return (string)$comp->name;
    }

    public function getRelatedContract()
    {
        $comp =  Contract::find()->where(['parent' => $this->getId()])->all();
        return $comp;
    }

    public function getRelatedContractLink()
    {
        $output = '';
        $comps =  Contract::find()->where(['parent' => $this->getId()])->all();
        foreach ($comps as $comp) {
            $output .= '<a target="_blank" href="'. Url::to(['/docs/contract/view', 'id' => (string) $comp->_id]).'">'.$comp->name.'</a><br/>';
        }
        return $output;
    }

    public function beforeSave($insert)
    {

        if ($this->isNewRecord) {
            // Если новая модель, то сгенерируем int auto increment ID (для mongodb)
            $this->id = ContractAutoIncrement::getNextAutoIncrementID();
            $this->created_at = time();
        }
        $this->updated_at = time();
        if (!empty($this->date)) {
            $this->date_timestamp = strtotime($this->date . '23:59:59');
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getAccessUpdate()
    {
        $userid = Yii::$app->getUser()->getId();
        $user = User::find()->where(['_id' => $userid])->one();

        if ($this->status != 1 && ($this->_author == $userid || $user->getStatus()))
            return TRUE;

        if ($this->status >= 1 && $user->getStatus())
            return TRUE;

        return FALSE;
    }

    public function getAccessAccept()
    {
        $userid = Yii::$app->getUser()->getId();
        $user = User::find()->where(['_id' => $userid])->one();
        if ($this->status != 1 && $user->getStatus())
            return TRUE;

        return FALSE;
    }

    public function getAccessRepeatAccept()
    {
        $userid = Yii::$app->getUser()->getId();
        $user = User::find()->where(['_id' => $userid])->one();
        if ($this->_author == $userid)
            return FALSE;
        if ($this->status != 1 && $user->getStatus())
            return TRUE;

        return FALSE;
    }
}
