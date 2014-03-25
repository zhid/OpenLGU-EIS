<?php

/**
 * This is the model class for table "threshold".
 *
 * The followings are the available columns in table 'threshold':
 * @property integer $threshold_id
 * @property integer $measure_id
 * @property integer $column_id
 * @property double $lowthreshold
 * @property string $lowthreshold_operator
 * @property string $threshold_type
 * @property double $highthreshold
 * @property string $highthreshold_operator
 *
 * The followings are the available model relations:
 * @property ColumnDimension $column
 * @property Measure $measure
 */
class Threshold extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'threshold';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('measure_id, column_id', 'required'),
			array('measure_id, column_id', 'numerical', 'integerOnly'=>true),
			array('lowthreshold, highthreshold', 'numerical'),
			array('lowthreshold_operator, highthreshold_operator', 'length', 'max'=>5),
			array('threshold_type', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('threshold_id, measure_id, column_id, lowthreshold, lowthreshold_operator, threshold_type, highthreshold, highthreshold_operator', 'safe', 'on'=>'search'),
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
			'column' => array(self::BELONGS_TO, 'ColumnDimension', 'column_id'),
			'measure' => array(self::BELONGS_TO, 'Measure', 'measure_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'threshold_id' => 'Threshold',
			'measure_id' => 'Measure',
			'column_id' => 'Column',
			'lowthreshold' => 'Lowthreshold',
			'lowthreshold_operator' => 'Lowthreshold Operator',
			'threshold_type' => 'Threshold Type',
			'highthreshold' => 'Highthreshold',
			'highthreshold_operator' => 'Highthreshold Operator',
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

		$criteria->compare('threshold_id',$this->threshold_id);
		$criteria->compare('measure_id',$this->measure_id);
		$criteria->compare('column_id',$this->column_id);
		$criteria->compare('lowthreshold',$this->lowthreshold);
		$criteria->compare('lowthreshold_operator',$this->lowthreshold_operator,true);
		$criteria->compare('threshold_type',$this->threshold_type,true);
		$criteria->compare('highthreshold',$this->highthreshold);
		$criteria->compare('highthreshold_operator',$this->highthreshold_operator,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Threshold the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
