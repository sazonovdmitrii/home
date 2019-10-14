<?php

/**
 * This is the model class for table "media_knowledge".
 *
 * The followings are the available columns in table 'media_knowledge':
 * @property integer $id
 * @property integer $status
 * @property integer $author_id
 * @property integer $author_name
 * @property integer $author_url
 * @property integer $author_image_id
 * @property integer $image_id
 * @property integer $whom_interest
 * @property string $title
 * @property string $lead
 * @property string $meta_desc
 * @property string $content
 * @property string $read_more
 * @property integer $genre
 * @property string $cat_category_name
 * @property string $section_url
 * @property integer $public_time
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $count_comment
 * @property integer $count_view
 * @property integer $article_first
 * @property integer $article_second
 * @property integer $article_third
 * @property string $selected_category
 * @property integer $model_first
 * @property integer $model_second
 * @property integer $model_third
 */
class MediaKnowledge extends EActiveRecord implements IComment, IUploadImage
{
	// --- СТАТУСЫ ---
	const STATUS_PUBLIC 	= 1; // Опубикован
	const STATUS_HIDE 	= 2; // Скрыт
	const STATUS_DELETED 	= 3; // Удален

	public static $statusNames = array(
		self::STATUS_PUBLIC => 'Опубликован',
		self::STATUS_HIDE => 'Скрыт'
	);

	// --- ЖАНРЫ ---
	const GENRE_INTERVIEW 	= 1; // Интервью
	const GENRE_PHOTO 	= 2; // Фотоленты
	const GENRE_VIDEO 	= 3; // Видео
	const GENRE_REPORT 	= 4; // Репортаж
	const GENRE_REPAIR 	= 5; // Академия ременота
	const GENRE_OTHER 	= 6; // Другие статьи

	public static $genreNames = array(
		self::GENRE_OTHER 	=> 'Статьи',
		self::GENRE_PHOTO 	=> 'Фотоленты',
		self::GENRE_VIDEO 	=> 'Видео',
		self::GENRE_INTERVIEW 	=> 'Интервью',
		self::GENRE_REPORT 	=> 'Репортажи',
		//self::GENRE_REPAIR	=> 'Академия ремонта',
	);

	// Набор css классов для вывода в списках
	public static $genreCssClass = array(
		self::GENRE_INTERVIEW 	=> 'interview',
		self::GENRE_PHOTO 	=> 'photo',
		self::GENRE_VIDEO 	=> 'video',
		self::GENRE_REPORT 	=> 'reportage',
		self::GENRE_REPAIR	=> 'maintenance',
		self::GENRE_OTHER 	=> 'other_genre',
	);

	public static $preview = array(
		'crop_60' => array(60, 60, 'crop', 80),
		'crop_80' => array(80, 80, 'crop', 80), // in view
		'crop_210' => array(210, 210, 'crop', 80), // preview
		'crop_160x110' => array(160, 110, 'crop', 80),
		'crop_280x200' => array(280, 200, 'crop', 80),
		'crop_220x130' => array(220, 130, 'crop', 80),
		'crop_220x175' => array(220, 175, 'crop', 80),
		'crop_300x213' => array(300, 213, 'crop', 80),
		'crop_460x340' => array(460, 340, 'crop', 90),
		'crop_460x365' => array(460, 365, 'crop', 90),
		'crop_700x450' => array(700, 450, 'crop', 90),
		'width_700'    => array(700, 0, 'resize', 90, false, 'descrease' => true)
	);

	// Констнаты для поля "Кому это интересно"
	const WHOM_SPEC = 1; // Только специалистам
	const WHOM_USER = 2; // Только владельцам квартир
	const WHOM_SPEC_USER = 3; // И специалистам и владельцам квартир


	const UPLOAD_IMAGE_DIR = 'uploads/public/media_knowledge/';

	// Типы сортировок в списке Статей
	const SORT_NEWER = 1;
	const SORT_COMMENT = 2;
	const SORT_VIEW = 3;

	// Вид списка элементов
	const VIEW_TYPE_LIST = 1;
	const VIEW_TYPE_ELEM = 2;

	// Флаг обозначающий выводить в RSS или нет.
	const FOR_RSS_NO = 0;
	const FOR_RSS_YES = 1;

	// Переменная для множественного свойства "Тематики"
	private $_themes = null;

	// Тип изображения для загрузки
	private $_imageType = null;

	public function init()
	{
		parent::init();

		$this->onBeforeSave = array($this, 'setDate');
		$this->onAfterSave = array($this, 'saveThemes');
		$this->onAfterSave = array($this, 'updateSphinx');
	}

	public function __get($name)
	{
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->$getter();
		return parent::__get($name);
	}

	/**
	 * Update create_time and update_time in object
	 */
	public function setDate()
	{
		if ($this->isNewRecord)
			$this->create_time = $this->update_time = time();
		else
			$this->update_time = time();
	}

	public function updateSphinx()
	{
		Yii::app()->gearman->appendJob('sphinx:media', array('type'=>Media::TYPE_KNOWLEDGE, 'id'=>$this->id));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MediaKnowledge the static model class
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
		return 'media_knowledge';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('themes, title, lead, image_id, content, author_id, genre, whom_interest', 'required'),

			array('rss, status, author_id, image_id, author_image_id, whom_interest, genre, public_time, create_time, update_time, count_comment,
			count_view, article_first, article_second, article_third, model_first, model_second, model_third', 'numerical', 'integerOnly'=>true),
			array('title, cat_category_name, author_url, meta_desc', 'length', 'max'=>255),

			array('author_name', 'length', 'max'=>100),
			array('section_url', 'length', 'max'=>100),
			array('author_url', 'url'),
			array('lead', 'length', 'max'=>500),
			array('read_more', 'length', 'max'=>3000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, status, author_id, image_id, whom_interest, title, lead, content, read_more, genre, cat_category_name, public_time, create_time, update_time, count_comment, count_view', 'safe', 'on'=>'search'),
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
			'author'=>array(self::BELONGS_TO, 'User', 'author_id'),
			'preview'=>array(self::BELONGS_TO, 'UploadedFile', 'image_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'rss'               => 'Добавлять в RSS Яндекса',
			'status'            => 'Статус',
			'author_id'         => 'Автор',
			'author_name'       => 'ФИО автора',
			'author_url'        => 'Url автора',
			'author_image_id'   => 'Фото автора',
			'image_id'          => 'Превью',
			'themes'            => 'Тематики',
			'whom_interest'     => 'Кому это интересно',
			'title'             => 'Заголовок',
			'lead'              => 'Лид',
			'content'           => 'Содержание',
			'read_more'         => 'Читайте также',
			'genre'             => 'Жанр',
			'cat_category_name' => 'Связь с каталогом товаров',
			'public_time'       => 'Дата публикации',
			'create_time'       => 'Дата создания',
			'update_time'       => 'Дата обновления',
			'count_comment'     => 'Кол-во комментариев',
			'count_view'        => 'Количество просмотров',
			'article_first'     => 'id первой статьи',
			'article_second'    => 'id второй статьи',
			'article_third'     => 'id третьей статьи',
			'model_first'       => 'Тип первой статьи',
			'model_second'      => 'Тип второй статьи',
			'model_third'       => 'Тип третьей статьи',
			'section_url'       => 'URL-название раздела',
			'meta_desc'         => 'Meta Description',

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
		$criteria->compare('status',$this->status);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('image_id',$this->image_id);
		$criteria->compare('genre',$this->genre);

		$date_from = Yii::app()->request->getParam('date_from');
		$date_to = Yii::app()->request->getParam('date_to');
		if ($date_from)
			$criteria->compare('t.public_time', '>='.(strtotime($date_from)));
		if ($date_to)
			$criteria->compare('t.public_time', '<='.(strtotime($date_to)+86400));

		$criteria->compare('count_comment',$this->count_comment);
		$criteria->compare('count_view',$this->count_view);

		$criteria->order = 'public_time DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Проверка отображения комментариев
	 * @return boolean
	 * @author Alexey Shvedov
	 */
	public function getCommentsVisibility()
	{
		return true;
	}

	/**
	 * Обработчик события комментирования текущего объекта.
	 * @param $comment Comment
	 * @return Array
	 */
	public function afterComment($comment)
	{
		/**
		 * Обновление кол-ва комментариев текущего объекта
		 */
		$count_comment = Comment::getCountComments($this);
		self::model()->updateByPk($this->id, array(
			'count_comment'  => $count_comment,
		));

		return array(0, $count_comment);
	}

	/**
	 * Проверка владения моделью
	 * @author Alexey Shvedov
	 */
	public function getIsOwner()
	{
		return $this->author_id === Yii::app()->user->id;
	}

	/**
	 * Возвращает имя элемента для списка комментариев в админке
	 * @return string
	 */
	public function getCommentName()
	{
		return $this->title;
	}


	/**
	 * Возвращает значение для множественного поля themes.
	 * Если значение еще не было получено, то получает выставленные
	 * Тематики из модели MediaThemeSelect
	 * @return array|null
	 */
	public function getThemes()
	{
		if ($this->getIsNewRecord())
		{
			$this->_themes = array();
		}
		elseif (is_null($this->_themes))
		{
			$selected = MediaThemeSelect::model()->findAllByAttributes(array(
				'model' => get_class($this),
				'model_id' => $this->id
			));

			if ($selected) {
				foreach($selected as $item) {
					$this->_themes[] = $item->theme_id;
				}
			}
		}

		return $this->_themes;
	}

	/**
	 * Устанавливает значение для множественного поля themes
	 * @param $value
	 * @return bool
	 */
	public function setThemes($value)
	{
		if (is_array($value)) {
			$this->_themes = $value;
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Сохраняет значения мультизначительного свойства "Тематики" в связные таблицу
	 */
	public function saveThemes()
	{
		if ( ! empty($this->_themes)) {

			$className = get_class($this);

			MediaThemeSelect::model()->deleteAllByAttributes(array(
				'model' => $className,
				'model_id' => $this->id
			));

			$sql = 'INSERT INTO '.MediaThemeSelect::model()->tableName().' (`model`, `model_id`, `theme_id`) VALUES';
			foreach($this->_themes as $i=>$theme_id) {
				$id = (int)$theme_id;
				if ($i > 0)
					$sql .= ',';
				$sql .= "('{$className}', '{$this->id}', '{$id}')";
			}
			Yii::app()->getDb()->createCommand($sql)->execute();
		}
	}

	/**
	 * Возвращает ссылку на элемент для фронтенда
	 */
	public function getElementLink()
	{
		if ($this->section_url) {
			return Yii::app()->controller->createUrl('/journal/knowledge/cat/'.$this->section_url.'/'.$this->id);
		} else {
			return Yii::app()->controller->createUrl('/journal/knowledge/'.$this->id);
		}
	}

	/**
	 * Возвращает ссылку на раздел для фронтенда
	 * @static
	 * @return string
	 */
	public static function getSectionLink()
	{
		return '/journal/knowledge';
	}


	// IUploadImage
	public function getImagePath()
	{
		if ( $this->getIsNewRecord())
			return false;

		switch ($this->_imageType) {
			// Фотка превью для всей статьи
			case 'preview': return 'media/knowledge/'.intval($this->id % 10); break;
			case 'authorPhoto': return 'media/knowledgeAuthor/'; break;
			default: throw new CException('Invalid upload image type');
		}
	}

	public function getImageName()
	{
		if ( $this->getIsNewRecord())
			return false;

		switch ($this->_imageType) {
			// Имя превью файла
			case 'preview': return 'knowledge'.$this->id.'_'.time(); break;
			case 'authorPhoto': return 'author'.$this->id.'_'.time(); break;
			default: throw new CException('Invalid upload image type');
		}
	}

	public function getAuthorId()
	{
		return $this->author_id;
	}

	public function setImageType($name)
	{
		$this->_imageType = $name;
	}

	public function flushImageType()
	{
		$this->_imageType = null;
		return true;
	}

	public function checkAccess()
	{
		return !is_null($this->author_id) && $this->author_id == Yii::app()->user->id;
	}

	public function imageConfig()
	{
		switch ($this->_imageType) {
			case 'preview': return array(
				'realtime' => array(
					self::$preview['crop_80'],
					self::$preview['crop_210'],
				),
				'background' => array(
					self::$preview['crop_60'],
					self::$preview['crop_160x110'],
					self::$preview['crop_280x200'],
					self::$preview['crop_300x213'],
					self::$preview['crop_700x450'],
					self::$preview['crop_220x130'],
					self::$preview['width_700']
				),
			);
			case 'authorPhoto': return array(
				'realtime' => array(
					self::$preview['crop_60'],
				),
			);
			default: throw new CException('Invalid upload image type');
		}
	}

	// end of IUploadImage


	public function scopes()
	{
		return array(
			/*
			 * Ограничение, возваращающее только опубликованные
			 * до настоящего времени элементы.
			 */
			'published' => array(
				'order'     => 'public_time DESC',
				'condition' => 'status = :st AND public_time <= :pub_time',
				'params'    => array(
					':st'       => MediaKnowledge::STATUS_PUBLIC,
					':pub_time' => time()
				)
			),
			// Возвращает данные только для RSS канала
			'only_rss' => array(
				'condition' => 'rss = :rss',
				'params'    => array(
					':rss' => MediaKnowledge::FOR_RSS_YES
				)
			)
		);
	}

	/**
	 * Построение дерева
	 *
	 * Новая реализация построителя дерева
	 * Строит по выборке без подзапросов
	 * @note Использует только целые или строковые ключи
	 * @param $models
	 * @return array
	 */
	public static function getTree($selected=array()) {

		$models = Category::model()->findAll(array('order'=>'lft','index'=>'id'));

		$levels = array();
		$items = array();
		$tree = array();

		foreach ($models as $node) {
			$primary = $node->id;
			$currentLevel = $node->level;
			$expand = false;

			foreach($selected as $key => $se)
			{
				if($models[$key]->lft > $node->lft and $models[$key]->rgt < $node->rgt)
				{
					$expand = true;
				}
			}

			$item = array(
				'key' => $primary,
				'title' => $node->name,
				'select' => isset( $selected[$primary] ) , //true,
				'noLink' => true,
				'checkbox' => false,
				'expand' => $expand,
			);
			$items[ $primary ] = $item;

			$levels[ $currentLevel ] = $primary;

			if ( $node->isRoot() ) {
				$tree[] = &$items[ $primary ];
				continue;
			}

			$parent = &$items[ $levels[ $currentLevel-1 ] ];
			$parent['children'][] = &$items[ $primary ];
			//$parent['isFolder'] = true;

		}

		return $tree;
	}


	/**
	 * Наращивает счетчик просмотра элемента
	 * При количестве кратном 10 переносит данные
	 * в базу данных
	 */
	public function incrementView()
	{
		$key = self::getCacheKeyView($this->id);
		if (!$this->getIsNewRecord()) {

			// Наращиваем счетчик просмотра
			$count = Yii::app()->redis->incr($key);

			if ($count % 10 == 0) {
				$this->count_view = (int)$count;
				self::save(false, array('count_view'));
			}
		}
	}


	/**
	 * Возвращает кол-во просмотров статьи
	 *
	 * @return mixed
	 */
	public function getCount_view()
	{
		return Yii::app()->redis->get(self::getCacheKeyView($this->id));
	}


	/**
	 * Возвращает ключ для кеша, в котором находится счетчик просмотра
	 * элемента с идентификатором $id
	 *
	 * @param $id Идентификатор статьи
	 *
	 * @return string
	 */
	static public function getCacheKeyView($id)
	{
		return 'Media:Knowledge:View:' . (int)$id;
	}
}