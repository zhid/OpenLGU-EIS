<?php

/**
 * This is the model class for table "area".
 *
 * The followings are the available columns in table 'area':
 * @property integer $area_id
 * @property string $area_name
 * @property integer $color_rating
 * @property string $managing_office
 * @property string $officer_in_charge
 * @property boolean $visible
 * @property integer $service_area
 * @property string $area_logo
 *
 * The followings are the available model relations:
 * @property Measure[] $measures
 */
class Area extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('area_name', 'required'),
			array('color_rating, service_area', 'numerical', 'integerOnly'=>true),
			array('area_name, managing_office, officer_in_charge', 'length', 'max'=>100),
			array('area_logo', 'length', 'max'=>30),
			array('visible', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('area_id, area_name, color_rating, managing_office, officer_in_charge, visible, service_area, area_logo', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'measures' => array(self::HAS_MANY, 'Measure', 'area_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'area_id' => 'Area',
			'area_name' => 'Area Name',
			'color_rating' => 'Color Rating',
			'managing_office' => 'Managing Office',
			'officer_in_charge' => 'Officer In Charge',
			'visible' => 'Visible',
			'service_area' => 'Service Area',
			'area_logo' => 'Area Logo',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('area_id',$this->area_id);
		$criteria->compare('area_name',$this->area_name,true);
		$criteria->compare('color_rating',$this->color_rating);
		$criteria->compare('managing_office',$this->managing_office,true);
		$criteria->compare('officer_in_charge',$this->officer_in_charge,true);
		$criteria->compare('visible',$this->visible);
		$criteria->compare('service_area',$this->service_area);
		$criteria->compare('area_logo',$this->area_logo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Area the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
