<?php

/**
 * This is the model class for table "row_hierarchy".
 *
 * The followings are the available columns in table 'row_hierarchy':
 * @property integer $row_hierarchy_id
 * @property integer $category_id
 * @property integer $parent_id
 * @property boolean $top_flag
 * @property boolean $bottom_flag
 */
class RowHierarchy extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'row_hierarchy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id', 'required'),
			array('category_id, parent_id', 'numerical', 'integerOnly'=>true),
			array('top_flag, bottom_flag', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('row_hierarchy_id, category_id, parent_id, top_flag, bottom_flag', 'safe', 'on'=>'search'),
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
			'row_hierarchy_id' => 'Row Hierarchy',
			'category_id' => 'Category',
			'parent_id' => 'Parent',
			'top_flag' => 'Top Flag',
			'bottom_flag' => 'Bottom Flag',
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

		$criteria->compare('row_hierarchy_id',$this->row_hierarchy_id);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('top_flag',$this->top_flag);
		$criteria->compare('bottom_flag',$this->bottom_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RowHierarchy the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
