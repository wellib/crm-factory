<?php

namespace app\modules\accounts\models;


use app\validators\MongoObjectIdValidator;
use MongoDB\BSON\ObjectID;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\mongodb\rbac\Role;
use yii\web\UploadedFile;
use app\modules\accounts\Module;
use Intervention\Image\ImageManagerStatic as Image;


/**
 * This is the model class for collection "accounts_user".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property string $auth_key
 * @property string $access_token
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $mail_recovery_token Секретный ключ(токен) используется для восстановления пароля через почту
 * @property string $mail_recovery_created_at Когда был создан запрос на восстановления пароля через почту
 *
 * @property string $email
 * @property string $password_hash
 * @property string $name
 * @property string $avatar
 * @property string $position
 * @property string $nickname
 * @property ObjectID $parent_id

 *
 * @property UploadedFile $avatar_upload
 * @property null|string $password
 * @property string $id
 */
class User extends \yii\mongodb\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * string|null Расширение для файла(изображения) аватара
     */
    const AVATAR_FILE_EXTENSION = 'jpg';

    /**
     * integer Ширина изображения
     */
    const AVATAR_IMAGE_WIDTH  = 100;

    /**
     * integer Высота изображения
     */
    const AVATAR_IMAGE_HEIGHT = 100;

    /**
     * integer Качество изображения при сохранении
     */
    const AVATAR_IMAGE_QUALITY  = 75;

    /**
     * string Сценарий для добавления пользователя
     */
    const SCENARIO_CREATE = 'create';

    /**
     * string Сценарий для обновления информации существующего пользователя
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * int Минимальное кол-во символов в пароле
     */
    const PASSWORD_MIN_LENGTH = 1;

    /**
     * int Длина генерируемого ключа(токена) для восстановления
     */
    const MAIL_RECOVERY_TOKEN_LENGTH = 64;


    /**
     * @var string Виртуальный атрибут для назначения пароля
     */
    public $password;

    /**
     * @var UploadedFile Виртуальный атрибут используется для загрузки файла(avatar)
     */
    public $avatar_upload;

    /**
     * @var boolean Удаление заруженной аватарки
     */
    public $avatar_delete;

    /**
     * @var array Роли пользователя
     */
    public $roles;


    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'accounts_user';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            //systyem attributes
            '_id',
            'auth_key',
            'access_token',
            'created_at',
            'updated_at',

            //recovery by mail
            'mail_recovery_token',
            'mail_recovery_created_at',

            // other attributes
            'nickname',
            'email',
            'password_hash',
            'name',
            'avatar',
            'position',
            'rukovodstvo',
            'status',
            'parent_id',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['nickname', 'email', 'name', 'position', 'password', 'avatar_upload', 'roles', 'parent_id'],
            self::SCENARIO_UPDATE => ['nickname', 'email', 'name', 'position', 'rukovodstvo', 'status', 'password', 'avatar_upload', 'avatar_delete', 'roles', 'parent_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // default rules
            [['nickname', 'email', 'password_hash', 'name', 'avatar', 'position', 'password', 'rukovodstvo', 'status'], 'string'],
            [['created_at', 'updated_at'], 'integer'],

            [['position'], 'default', 'value' => null],
            // email rules
            [['email'], 'email'],
            [['email'], 'unique'],
            [['email'], 'default', 'value' => null],


            [['nickname'], 'unique'],
            // custom rules
            [['nickname', 'name', 'email'], 'required'],
            [['password'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['password'], 'string', 'min' => self::PASSWORD_MIN_LENGTH],


            // for crop
            [
                ['avatar_upload'],
                'image',
                'skipOnEmpty' => true,
                'extensions'  => self::allowImageExtensions(),
                'mimeTypes'   => self::allowImageMimeTypes()
            ],
            [['avatar_delete'], 'boolean'],
            
            ['roles', 'safe'],
            ['roles', 'default', 'value' => []],

            ['parent_id', 'default'],
            ['parent_id', MongoObjectIdValidator::className()],
        ];
    }

    public static function allowImageExtensions()
    {
        return ['jpg', 'jpeg', 'png', 'gif'];
    }
    public static function allowImageMimeTypes()
    {
        return ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id'           => Module::t('user', 'ATTRIBUTE__ID__LABEL'),
            'nickname'      => Module::t('user', 'ATTRIBUTE__NICKNAME__LABEL'),
            'email'         => Module::t('user', 'ATTRIBUTE__EMAIL__LABEL'),
            'password_hash' => Module::t('user', 'ATTRIBUTE__PASSWORD_HASH__LABEL'),
            'name'          => Module::t('user', 'ATTRIBUTE__NAME__LABEL'),
            'avatar'        => Module::t('user', 'ATTRIBUTE__AVATAR__LABEL'),
            'position'      => Module::t('user', 'ATTRIBUTE__POSITION__LABEL'),
            'created_at'    => Module::t('user', 'ATTRIBUTE__CREATED_AT__LABEL'),
            'updated_at'    => Module::t('user', 'ATTRIBUTE__UPDATED_AT__LABEL'),
						'rukovodstvo' => Module::t('user', 'ATTRIBUTE__RUK__LABEL'),
						'status'    => Module::t('user', 'ATTRIBUTE__STATUS__LABEL'),
            // virtual attributes
            'password'      => Module::t('user', 'ATTRIBUTE__PASSWORD__LABEL'),
            'avatar_upload' => Module::t('user', 'ATTRIBUTE__AVATAR_UPLOAD__LABEL'),
            'avatar_delete' => Module::t('user', 'ATTRIBUTE__AVATAR_DELETE__LABEL'),
            
            'roles' => Module::t('user', 'ATTRIBUTE__ROLES__LABEL'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }


    /**
     * @param string $id
     * @return self|static
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * @param string $token
     * @param mixed $type
     * @return null|self
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['access_token' => $token]);
    }

    /**
     * @param bool $toString
     * @return \MongoDB\BSON\ObjectID|string
     */
    public function getId($toString = true)
    {
        return $toString ? (string) $this->_id : $this->_id;
    }

    public function getStatus()
    {
				if ($this->status || $this->rukovodstvo)
					return 1;
				
        return (string) $this->status;
    }

    /**
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }
    
    
    /**
     * Установка(измение) пароля
     * @param string $password
     * @throws \yii\base\Exception
     * @return self
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        return $this;
    }

    /**
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function beforeSave($insert)
    {
        if (!empty($this->password)) {
            $this->setPassword($this->password);
        }

        if ($this->avatar_delete) {
            $this->deleteAvatar();
        }

        $this->uploadAvatar();

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->updateRoles();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
        $this->loadRoles();
    }


    public function deleteAvatar()
    {
        if (!empty($this->avatar)) {
            $module = Module::getInstance();
            $files = FileHelper::findFiles($module->getUploadPath(),[
                'only' => [
                    $this->avatar,
                ],
            ]);
            foreach ($files as $file) {
                @unlink($file);
            }
            $this->avatar = null;
        }
        return true;
    }

    public function uploadAvatar()
    {
        if (($this->avatar_upload instanceof UploadedFile) === false) {
            return true; // если изображение обязательное к загрузке то возможно стоит поставить false
        }
        if ($this->validate(['avatar_upload']) === false) {
            return false;
        }

        $module = Module::getInstance();
        $dir = $module->getUploadPath(true);
        $fileNamePrefix = $module->getUploadFilePrefix();
        $fileName = uniqid($fileNamePrefix);
        $fileExtension = !empty(self::AVATAR_FILE_EXTENSION) ? self::AVATAR_FILE_EXTENSION : $this->avatar_upload->extension;
        $fileNameWithExtension = $fileName. '.' . $fileExtension;
        $fullPath = $dir . $fileNameWithExtension;

        $saveResult = Image::make($this->avatar_upload->tempName)
            ->fit(self::AVATAR_IMAGE_WIDTH, self::AVATAR_IMAGE_HEIGHT)
            ->save($fullPath,self::AVATAR_IMAGE_QUALITY);

        if ($saveResult) {
            $this->deleteAvatar();
            $this->avatar = $fileNameWithExtension;
            return true;
        } else {
            return false;
        }
    }
    
    public function getAvatar($default = null)
    {
        if (!empty($this->avatar)) {
            $module = Module::getInstance();
            return $module->getUploadUrl(true) . $this->avatar;
        }
        return $default;
    }

    /**
     * Имя и должность
     * 
     * @return string
     */
    public function getNameAndPosition()
    {
        return $this->name . (!empty($this->position) ? ' (' . $this->position . ')' : '');
    }
    
    public function getViewUrl()
    {
        return ['/accounts/user/view', 'id' => (string)$this->_id];
    }


    /**
     * Создает новый запрос на восстановление пароля
     * Генерирует новый [[mail_recovery_token]] и время когда был произведен запрос на восстановление [[mail_recovery_created_at]]
     *
     * @return self
     */
    public function createMailRecoveryRequest()
    {
        $this->mail_recovery_created_at = time();
        $this->mail_recovery_token = Yii::$app->security->generateRandomString(self::MAIL_RECOVERY_TOKEN_LENGTH);
        return $this;
    }
    
    public function sendMail($subject, $body)
    {
        return Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['smtpEmail'])
                ->setFrom([Yii::$app->params['smtpEmail'] => Yii::$app->name])
                ->setTo($this->email)
                ->setSubject($subject)
                ->setTextBody(strip_tags($body))
                ->setHtmlBody($body)
                ->send();
    }

    /**
     * Roles list <br/>
     * Key(role name) => Value (Role description)<br/>
     * For use in form (dropDownList, checkboxList, etc.)
     * @return array
     */
    public function getRolesList()
    {
        return ArrayHelper::map(Yii::$app->getAuthManager()->getRoles(), function ($model) {
            /** @var Role $model */
            return $model->name;
        }, function ($model){
            /** @var Role $model */
            return $model->description;
        });
    }

    /**
     * Update user roles<br/>
     * Revoking all old roles and assign new roles
     */
    public function updateRoles()
    {
        $am = Yii::$app->getAuthManager();
        $userID = $this->getId(false);
        $am->revokeAll($userID);
        foreach ($this->roles as $roleName) {
            if ($role = $am->getRole($roleName)) {
                $am->assign($role, $userID);
            }
        }
    }

    /**
     * Load assigned user roles
     */
    public function loadRoles()
    {
        $am = Yii::$app->getAuthManager();
        $userRoles = $am->getRolesByUser($this->getId(false));
        $this->roles = array_map(function($role) {
            /** @var Role $role */
            return $role->name;
        }, $userRoles);
    }

}
