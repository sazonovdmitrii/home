<?php

/**
 * This is the model class for table "cat_group_operation".
 *
 * The followings are the available columns in table 'cat_group_operation':
 * @property integer $id
 * @property integer $product_id
 * @property integer $category_id
 * @property integer $user_id
 * @method own
 */
class GroupOperation extends Catalog2ActiveRecord
{
	public function init()
	{
		parent::init();


		$this->onBeforeSave = array($this, 'setUser');
	}

	public function setUser()
	{
		$this->user_id = Yii::app()->user->id;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GroupOperation the static model class
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
		return 'cat_group_operation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, category_id, user_id', 'numerical', 'integerOnly'=>true),
			array('product_id', 'unique', 'message' => 'Товар уже добавлен'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, product_id, category_id', 'safe', 'on'=>'search'),
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
			'category' => array(self::BELONGS_TO, 'Category', 'category_id'),
			'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
			'user_id' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'product_id' => 'Товар',
			'category_id' => 'Категория',
			'user_id' => 'Пользователь',
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
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('user_id',$this->user_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	public function defaultScope()
	{
		return array(
			'condition'=>'user_id = :uid',
			'params' => array(':uid' => Yii::app()->user->id)
		);
	}
}