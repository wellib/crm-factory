<?php

namespace common\models;

use Yii;
use common\helpers\Image;

/**
 * This is the model class for table "cars".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $body_type_id
 * @property integer $brand_id
 * @property integer $model_id
 * @property integer $color_id
 * @property string $number
 * @property integer $mileage
 * @property integer $last_payment_id
 * @property string $start_lease
 * @property string $paid_up_to
 * @property string $year
 * @property string $description
 * @property integer $status
 * @property integer $oil_change
 *
 * @property User $owner
 * @property CarsBrands $brand
 * @property CarsModels $model
 * @property CarsColors $color
 */
class Cars extends \yii\db\ActiveRecord
{
    public $file;
    public $del_img;
    
    public static  $statuses = [1=>'available', 2=>'leased', 3=>'booked', 4=>'under repair', 5=>'unavailable'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['owner_id', 'body_type_id', 'model_id', 'color_id', 'number',  'year',  'status'], 'required'],
            [['owner_id', 'body_type_id', 'brand_id', 'model_id', 'color_id', 'mileage', 'last_payment_id', 'status', 'oil_change'], 'integer'],
            [['start_lease', 'paid_up_to', 'mileage', 'description', 'photo', 'details', 'type', 'price'], 'safe'],
            [['description'], 'string'],
            [['number'], 'string', 'max' => 10],
            [['year'], 'string', 'max' => 255],
            [['file'], 'file', 'extensions' => 'png, jpg'],
            [['del_img'], 'boolean'],
            ['start_lease','compare','compareAttribute'=>'paid_up_to','operator'=>'<'],
            ['paid_up_to','compare','compareAttribute'=>'start_lease','operator'=>'>'],

        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_id' => 'Owner',
            'body_type_id' => 'Body Type',
            'brand_id' => 'Brand',
            'model_id' => 'Model',
            'color_id' => 'Color',
            'number' => 'Number',
            'mileage' => 'Mileage',
            'last_payment_id' => 'Last Payment',
            'start_lease' => 'Start Lease',
            'paid_up_to' => 'Paid Up To',
            'year' => 'Year',
            'description' => 'Description',
            'status' => 'Status',
            'oil_change' => 'Oil Change',
            'details' => 'Details',
            'type' => 'Type',
            'price' => 'Price',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(CarsBrands::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(CarsModels::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColor()
    {
        return $this->hasOne(CarsColors::className(), ['id' => 'color_id']);
    }

    public function getStatusName(){
     if (isset(self::$statuses[$this->status]))
       return self::$statuses[$this->status];
     else
       return 'unknown';
    }

    public function getTitle(){
     return $this->model->brand->name.' '.$this->model->name;
    }


    public function getImage()
    {
        if (isset($this->photo) && $this->photo != '') {
            return '/upload/cars/'.$this->photo;
        } 
        return '/images/no-foto.jpg';
    }

    public function thumb($width = null, $height = null, $crop = true)
    {
        if($this->image && ($width || $height)){
            return Image::thumb($this->image, $width, $height, $crop);
        }
        return Image::thumb('/images/no-foto.jpg', $width, $height, $crop);
        return '[]';
    }
    
    public function gettypeCar()
    {
        if (isset($this->type) && $this->type) {
            return $this->type;
        } 
        return 'Standard';
    }
}
