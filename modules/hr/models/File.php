<?php

namespace app\modules\hr\models;

use Yii;

use MongoDB\BSON\ObjectID;

use yii\behaviors\TimestampBehavior;
use mongosoft\file\UploadBehavior;

use app\modules\hr\Module;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for collection "hr_file".
 *
 * @property ObjectID|string $_id
 * @property string|UploadedFile $file
 * @property string $name
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 */
class File extends \yii\mongodb\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'hr_file';
    }
    
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'created_at',
            'updated_at',
            'name',
            
            'file',
            'description',
        ];
    }
    
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'file',
                'description',
            ],
            self::SCENARIO_UPDATE => [
                'description',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['description', 'string'],
            ['description', 'default', 'value' => null],
            ['file', 'file', 'extensions' => self::allowExtensions(), 'on' => self::SCENARIO_CREATE],
            [['file'], 'required'],
        ];
    }
    
    public static function allowExtensions()
    {
        return [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'tiff',
            'doc',
            'docx',
            'pdf',
            'xslx',
            'xsl',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Module::t('file', 'ATTR__ID__LABEL'),
            
            'created_at' => Module::t('file', 'ATTR__CREATED_AT__LABEL'),
            'updated_at' => Module::t('file', 'ATTR__UPDATED_AT__LABEL'),
            
            'file'        => Module::t('file', 'ATTR__FILE__LABEL'),
            'description' => Module::t('file', 'ATTR__DESCRIPTION__LABEL'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'file',
                'scenarios' => [self::SCENARIO_CREATE],
                'path' => '@app/modules/hr/uploads',
                'url' => '@web/upload/hr',
                'generateNewName' => function($file) {
                    /** @var UploadedFile $file */
                    $this->name = $file->getBaseName() . '.' . $file->getExtension();
                    return uniqid() . '.' . $file->extension;
                }
            ],
        ];
    }

    /**
     * @param bool $toString
     * @return ObjectID|string
     */
    public function getId($toString = true)
    {
        return $toString ? (string) $this->_id : $this->_id;
    }

    /**
     * Route to download file
     * @param $id
     * @return array
     */
    public static function downloadRoute($id)
    {
        return ['/hr/file-api/download', 'id' => $id];
    }

    /**
     * Url to download file
     * @param $id
     * @param bool $scheme
     * @return string
     */
    public static function downloadUrl($id, $scheme = false)
    {
        return Url::to(self::downloadRoute($id), $scheme);
    }

    /**
     * Url to download file (alias for File::downloadUrl)
     * @param bool $scheme
     * @return string
     * @see File::downloadUrl()
     */
    public function getDownloadUrl($scheme = false)
    {
        return self::downloadUrl($this->getId(true), $scheme);
    }

    /**
     * Путь к файлу (используется для скачивания клиентом)
     * @return string
     */
    public function getFilePath()
    {
        /** @var UploadBehavior $this */
        return $this->getUploadPath('file');
    }
}
