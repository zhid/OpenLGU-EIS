<?php

/**
 * This is the model class for table "indicator".
 *
 * The followings are the available columns in table 'indicator':
 * @property integer $indicator_id
 * @property integer $area_id
 * @property integer $column_id
 * @property double $threshold
 * @property string $indicator_operator
 * @property string $indicator_type
 */
class Indicator extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'indicator';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('area_id, column_id', 'required'),
			array('area_id, column_id', 'numerical', 'integerOnly'=>true),
			array('threshold', 'numerical'),
			array('indicator_operator, indicator_type', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('indicator_id, area_id, column_id, threshold, indicator_operator, indicator_type', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'indicator_id' => 'Indicator',
			'area_id' => 'Area',
			'column_id' => 'Column',
			'threshold' => 'Threshold',
			'indicator_operator' => 'Indicator Operator',
			'indicator_type' => 'Indicator Type',
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

		$criteria->compare('indicator_id',$this->indicator_id);
		$criteria->compare('area_id',$this->area_id);
		$criteria->compare('column_id',$this->column_id);
		$criteria->compare('threshold',$this->threshold);
		$criteria->compare('indicator_operator',$this->indicator_operator,true);
		$criteria->compare('indicator_type',$this->indicator_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Indicator the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
