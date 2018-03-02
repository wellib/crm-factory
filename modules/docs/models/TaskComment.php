<?php

namespace app\modules\docs\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\modules\accounts\models\User;

/**
 * This is the model class for collection "docs_task_comments".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $_task
 * @property mixed $_user
 * @property mixed $comment
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property User $user
 */
class TaskComment extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'docs_task_comments';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            '_task',
            '_user',
            'comment',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_task', '_user', 'comment', 'created_at', 'updated_at'], 'safe'],
            [['comment'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        //return [
        //    '_id' => Yii::t('task_message', 'ID'),
        //    '_task' => Yii::t('task_message', 'Task'),
        //    '_user' => Yii::t('task_message', 'User'),
        //    'comment' => Yii::t('task_message', 'Comment'),
        //    'created_at' => Yii::t('task_message', 'Created At'),
        //    'updated_at' => Yii::t('task_message', 'Updated At'),
        //];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['_id' => '_user']);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getText()
    {
        return nl2br($this->comment);
    }
}
