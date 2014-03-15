<?php

/**
 * This is the model class for table "row_dimension".
 *
 * The followings are the available columns in table 'row_dimension':
 * @property integer $measure_id
 * @property integer $row_id
 * @property string $row_name
 * @property string $row_data_type
 *
 * The followings are the available model relations:
 * @property Measure $measure
 */
class RowDimension extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'row_dimension';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('row_data_type', 'required'),
			array('measure_id', 'numerical', 'integerOnly'=>true),
			array('row_name', 'length', 'max'=>100),
			array('row_data_type', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('measure_id, row_id, row_name, row_data_type', 'safe', 'on'=>'search'),
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
			'measure_id' => 'Measure',
			'row_id' => 'Row',
			'row_name' => 'Row Name',
			'row_data_type' => 'Row Data Type',
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
		$criteria->compare('row_id',$this->row_id);
		$criteria->compare('row_name',$this->row_name,true);
		$criteria->compare('row_data_type',$this->row_data_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RowDimension the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
