<?php

namespace common\models;

use Yii;
use common\helpers\Image;
use yii\helpers\Html;

/**
 * This is the model class for table "contracts".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $number
 * @property string $date_create
 * @property string $date_update
 * @property string $time
 * @property integer $user_id
 * @property integer $car_id
 * @property string $date_start
 * @property string $date_stop
 * @property integer $customer_id
 * @property integer $place_id
 * @property string $location
 * @property string $description
 * @property integer $status
 * @property string $photos
 * @property integer $gasoline
 * @property string $room
 * @property integer $car_clean
 * @property integer $baby_seat
 *
 * @property User $user
 * @property Cars $car
 * @property Customers $customer
 */
class Contracts extends \yii\db\ActiveRecord
{
    public  $types = [1=>'Rent', 2=>'Repair'];
    public static $statuses = array(1=>'Open', 2=>'Closed', '3'=>'Need check');
    public static $gasolines = array('Full', '7', '6', '5', 'Half', '3', '2', 'Empty');
    public $mileage;
    public $car_number = 0;
    public $file;
    public $del_img;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contracts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'user_id', 'car_id', 'customer_id', 'place_id', 'status', 'gasoline', 'car_clean', 'baby_seat'], 'integer'],
            [['time', 'car_id', 'date_start', 'date_stop', 'customer_id', 'place_id' ], 'required'],
            [['number', 'date_create', 'date_update', 'date_start', 'date_stop', 'user_id', 'date_create', 'date_update',  'location', 'description', 'status', 'photos'], 'safe'],
            [['description', 'photos'], 'string'],
            [['number', 'time'], 'string', 'max' => 10],
            [['location'], 'string', 'max' => 250],
            [['room'], 'string', 'max' => 45],
            ['date_start','compare','compareAttribute'=>'date_stop','operator'=>'<'],
            ['date_stop','compare','compareAttribute'=>'date_start','operator'=>'>'],
            [['file'], 'file', 'maxFiles' => 10, 'extensions' => 'png, jpg'],
            [['del_img'], 'boolean'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cars::className(), 'targetAttribute' => ['car_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'number' => 'Number',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
            'time' => 'Time',
            'user_id' => 'User ID',
            'car_id' => 'Car',
            'date_start' => 'Date Start',
            'date_stop' => 'Date Stop',
            'customer_id' => 'Customer',
            'place_id' => 'Place ID',
            'location' => 'Location',
            'description' => 'Description',
            'status' => 'Status',
            'photos' => 'Photos',
            'gasoline' => 'Gasoline',
            'room' => 'Room',
            'car_clean' => 'Car Clean',
            'baby_seat' => 'Baby Seat',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Cars::className(), ['id' => 'car_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'customer_id']);
    }

    public function getStatusName(){
     if (isset(self::$statuses[$this->status]))
       return self::$statuses[$this->status];
     else
       return 'unknown';
    }

    public function getGasolineName(){
     if (isset(self::$gasolines[$this->gasoline]))
       return self::$gasolines[$this->gasoline];
     else
       return 'unknown';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(Places::className(), ['id' => 'place_id']);
    }
    
    
    public function getImage()
    {
        if (isset($this->photos) && $this->photos != '') {
            $photos = explode(';', $this->photos);
            if (is_array($photos)) {
              $return = array();
              foreach ($photos as $photo) {
                $return[] =  '/upload/contracts/'.$photo;
              }
              return $return;
            } else {
              return '/upload/contracts/'.$photos;
            }
        } 
        return '/images/no-foto.jpg';
    }

    public function thumb($width = null, $height = null, $crop = true)
    {
        if($this->image && ($width || $height)){
            if (is_array($this->image)) {
              $return = '';
              foreach ($this->image as $photo) {
                return Image::thumb($photo, $width, $height, $crop);
              }
              return $return;
            } else {
              return Image::thumb($this->image, $width, $height, $crop);
            }
        }
        return Image::thumb('/images/no-foto.jpg', $width, $height, $crop);
    }

    public function getSlider()
    {
        if (isset($this->photos) && $this->photos != '') {
            $photos = explode(';', $this->photos);
            if (is_array($photos)) {
              $return = array();
              foreach ($photos as $photo) {
                $return[] = Html::img(Image::thumb('/upload/contracts/'.$photo, 728));
              }
              return $return;
            } else {
              return '/upload/contracts/'.$photos;
            }
        } 
        return '/images/no-foto.jpg';
    }
}
