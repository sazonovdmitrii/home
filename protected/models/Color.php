<?php

/**
 * @brief This is the model class for table "color".
 *
 * @details The followings are the available columns in table 'color':
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property integer $update_time
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property SolutionContent[] $solutionContents
 */
class Color extends EActiveRecord
{
        
        public function init()
	{
		parent::init();
		$this->onBeforeSave = array($this, 'setDate');
                $this->onAfterFind = array($this, 'getDate');
	}
	
	/**
	 * Update create_time and update_time in object
	 */
	public function setDate()
	{
		if($this->isNewRecord)
			$this->create_time=$this->update_time=time();
		else
			$this->update_time=time();
	}
        
        /**
	 * Convert create_time and update_time in object
	 */
	public function getDate()
	{
                $this->create_time = date("H:i:s d.m.Y", $this->create_time);
                $this->update_time = date("H:i:s d.m.Y", $this->update_time);
	}
        
        
	/**
	 * Returns the static model of the specified AR class.
	 * @return Color the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'color';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('update_time, create_time', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('desc', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, desc, update_time, create_time', 'safe', 'on'=>'search'),
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
			'solutionContents' => array(self::MANY_MANY, 'SolutionContent', 'solution_content_color(color_id, solution_content_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Наименование',
			'desc' => 'Описание',
			'update_time' => 'Дата обновления',
			'create_time' => 'Дата создания',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}