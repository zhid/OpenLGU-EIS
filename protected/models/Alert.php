<?php

/**
 * This is the model class for table "alert".
 *
 * The followings are the available columns in table 'alert':
 * @property integer $alert_id
 * @property integer $measure_id
 * @property integer $column_id
 * @property string $alert_type
 * @property string $description
 * @property string $date
 * @property boolean $read
 *
 * The followings are the available model relations:
 * @property Measure $measure
 */
class Alert extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'alert';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('measure_id, column_id, alert_type, description, date', 'required'),
			array('measure_id, column_id', 'numerical', 'integerOnly'=>true),
			array('alert_type', 'length', 'max'=>30),
			array('read', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('alert_id, measure_id, column_id, alert_type, description, date, read', 'safe', 'on'=>'search'),
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
			'measure' => array(self::BELONGS_TO, 'Measure', 'measure_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'alert_id' => 'Alert',
			'measure_id' => 'Measure',
			'column_id' => 'Column',
			'alert_type' => 'Alert Type',
			'description' => 'Description',
			'date' => 'Date',
			'read' => 'Read',
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

		$criteria->compare('alert_id',$this->alert_id);
		$criteria->compare('measure_id',$this->measure_id);
		$criteria->compare('column_id',$this->column_id);
		$criteria->compare('alert_type',$this->alert_type,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('read',$this->read);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Alert the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
