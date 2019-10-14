<?php

/**
 * This is the model class for table "subdomain".
 *
 * Класс реализуется работу с подддоменами сайта.
 * В него включена обработка всех видов поддоменов любого уровня.
 *
 * The followings are the available columns in table 'subdomain':
 * @property integer $id
 * @property string  $domain
 * @property string  $model
 * @property integer $model_id
 * @property integer $create_time
 * @property integer $update_time
 */
class Subdomain extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Subdomain the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subdomain';
	}

	public function behaviors()
	{
		return array(
			'ModelTimeBehavior' => array(
				'class'     => 'application.components.ModelTimeBehavior',
			)
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('domain, model, model_id', 'required'),
			array('model_id, create_time, update_time', 'numerical', 'integerOnly' => true),
			array('domain, model', 'length', 'max' => 255),
			array('domain', 'length', 'min' => 6),
			array('domain', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, domain, model, model_id, create_time, update_time', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'          => 'ID',
			'domain'      => 'Домен',
			'model'       => 'Модель',
			'model_id'    => 'ID модели',
			'create_time' => 'Дата создания',
			'update_time' => 'Дата обновления',
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

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('domain', $this->domain, true);
		$criteria->compare('model', $this->model, true);
		$criteria->compare('model_id', $this->model_id);
		$criteria->compare('create_time', $this->create_time);
		$criteria->compare('update_time', $this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}