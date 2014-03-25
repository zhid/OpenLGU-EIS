<?php

/**
 * This is the model class for table "column_dimension".
 *
 * The followings are the available columns in table 'column_dimension':
 * @property integer $measure_id
 * @property integer $column_id
 * @property string $column_name
 * @property string $column_data_type
 *
 * The followings are the available model relations:
 * @property Measure $measure
 * @property Threshold[] $thresholds
 */
class ColumnDimension extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'column_dimension';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('measure_id', 'numerical', 'integerOnly'=>true),
			array('column_name', 'length', 'max'=>100),
			array('column_data_type', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('measure_id, column_id, column_name, column_data_type', 'safe', 'on'=>'search'),
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
			'thresholds' => array(self::HAS_MANY, 'Threshold', 'column_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'measure_id' => 'Measure',
			'column_id' => 'Column',
			'column_name' => 'Column Name',
			'column_data_type' => 'Column Data Type',
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
		$criteria->compare('column_id',$this->column_id);
		$criteria->compare('column_name',$this->column_name,true);
		$criteria->compare('column_data_type',$this->column_data_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ColumnDimension the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
