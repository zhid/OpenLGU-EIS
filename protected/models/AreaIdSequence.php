<?php

/**
 * This is the model class for table "area_id_sequence".
 *
 * The followings are the available columns in table 'area_id_sequence':
 * @property string $sequence_name
 * @property string $last_value
 * @property string $start_value
 * @property string $increment_by
 * @property string $max_value
 * @property string $min_value
 * @property string $cache_value
 * @property string $log_cnt
 * @property boolean $is_cycled
 * @property boolean $is_called
 */
class AreaIdSequence extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'area_id_sequence';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sequence_name, last_value, start_value, increment_by, max_value, min_value, cache_value, log_cnt, is_cycled, is_called', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('sequence_name, last_value, start_value, increment_by, max_value, min_value, cache_value, log_cnt, is_cycled, is_called', 'safe', 'on'=>'search'),
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
			'sequence_name' => 'Sequence Name',
			'last_value' => 'Last Value',
			'start_value' => 'Start Value',
			'increment_by' => 'Increment By',
			'max_value' => 'Max Value',
			'min_value' => 'Min Value',
			'cache_value' => 'Cache Value',
			'log_cnt' => 'Log Cnt',
			'is_cycled' => 'Is Cycled',
			'is_called' => 'Is Called',
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

		$criteria->compare('sequence_name',$this->sequence_name,true);
		$criteria->compare('last_value',$this->last_value,true);
		$criteria->compare('start_value',$this->start_value,true);
		$criteria->compare('increment_by',$this->increment_by,true);
		$criteria->compare('max_value',$this->max_value,true);
		$criteria->compare('min_value',$this->min_value,true);
		$criteria->compare('cache_value',$this->cache_value,true);
		$criteria->compare('log_cnt',$this->log_cnt,true);
		$criteria->compare('is_cycled',$this->is_cycled);
		$criteria->compare('is_called',$this->is_called);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AreaIdSequence the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
