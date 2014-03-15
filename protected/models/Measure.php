<?php

/**
 * This is the model class for table "measure".
 *
 * The followings are the available columns in table 'measure':
 * @property integer $measure_id
 * @property string $measure_name
 * @property string $measure_data_type
 * @property double $threshold
 * @property integer $alert_level
 * @property string $alert_time
 * @property integer $area_id
 *
 * The followings are the available model relations:
 * @property Area $area
 * @property ColumnTable[] $columnTables
 * @property RowTable[] $rowTables
 */
class Measure extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'measure';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('measure_data_type, area_id', 'required'),
			array('alert_level, area_id', 'numerical', 'integerOnly'=>true),
			array('threshold', 'numerical'),
			array('measure_name, alert_time', 'length', 'max'=>100),
			array('measure_data_type', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('measure_id, measure_name, measure_data_type, threshold, alert_level, alert_time, area_id', 'safe', 'on'=>'search'),
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
			'area' => array(self::BELONGS_TO, 'Area', 'area_id'),
			'columnTables' => array(self::HAS_MANY, 'ColumnTable', 'measure_id'),
			'rowTables' => array(self::HAS_MANY, 'RowTable', 'measure_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'measure_id' => 'Measure',
			'measure_name' => 'Measure Name',
			'measure_data_type' => 'Measure Data Type',
			'threshold' => 'Threshold',
			'alert_level' => 'Alert Level',
			'alert_time' => 'Alert Time',
			'area_id' => 'Area',
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

		$criteria->compare('measure_id',$this->measure_id);
		$criteria->compare('measure_name',$this->measure_name,true);
		$criteria->compare('measure_data_type',$this->measure_data_type,true);
		$criteria->compare('threshold',$this->threshold);
		$criteria->compare('alert_level',$this->alert_level);
		$criteria->compare('alert_time',$this->alert_time,true);
		$criteria->compare('area_id',$this->area_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Measure the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
