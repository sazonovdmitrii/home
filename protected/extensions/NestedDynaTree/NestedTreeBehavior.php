<?php

/**
 * NestedTreeBehavior class file.
 *
 * @author Szincsák András <andras@szincsak.hu>
 */

/**
 * Provides nested tree functionality for a model.
 *
 */
class NestedTreeBehavior extends CActiveRecordBehavior
{

	public $titleAttribute = 'name';
	public $levelAttribute = 'level';

	/**
	 * Get tree items in array format
	 * @param string $url url to item href
	 * @return array In tree format.
	 */
	public function getTree($url = "")
	{

		/** @var $owner CActiveRecord */
		$owner = $this->getOwner();
		$models = $owner::model()->findAll(array('order'=>$owner->leftAttribute));
		if (count($models) == 0)
			throw new CDbException(Yii::t('tree', 'There must be minimum one root record in model `' . $owner->className . '`'));
		$data = $this->loadTree($models);
		return $data;
	}

	/**
	 * Load items to an array recrusively
	 * @param CActiveRecord  $models
	 * @param string $url url to item href
	 * @param array $NodeArray the recrusive array
	 * @return array of the item
	 */
	/*private function loadTree($models, $url = "", $NodeArray = null)
	{
		$NodeArray['count'] = count($models);
		unset($NodeArray['data']);
		foreach ($models as $i => $node) {
			if (!isset($NodeArray['pk']) || !in_array($node->primaryKey, $NodeArray['pk'])) {
				$NodeArray['pk'][] = $node->primaryKey;
				$NodeArray['data'][$i] = array(
					'key'     => $node->primaryKey,
					'id'      => $node->primaryKey,
					'title'   => $node->nodeTitle,
					//'tooltip' => $node->nodeTitle,
				);
				if ($url && !$node->isRoot())
					$NodeArray['data'][$i]['href'] = $url . $node->primaryKey;
				if (!$node->isLeaf()) {
					$inter = $this->loadTree($node->children()->findAll(), $url, $NodeArray);
					$NodeArray['pk'] = $inter['pk'];
					$NodeArray['data'][$i]['isFolder'] = 'true';
					$NodeArray['data'][$i]['children'] = $inter['data'];
				}
				if ($node->isRoot())
					$NodeArray['data'][$i]['isFolder'] = 'true';
			}
		}
		return $NodeArray;
	}*/

	/**
	 * Новая реализация построителя дерева
	 * Строит по выборке без подзапросов
	 * @note Использует только целые или строковые ключи
	 * @param $models
	 * @return array
	 */
	private function loadTree($models) {
		if (!empty($models)) {
			$item = reset($models);
			if (is_array( $item->getPrimaryKey() ))
				throw new CException(500);
		}

		$levels = array();
		$items = array();
		$tree = array();

		foreach ($models as $node) {
			$primary = $node->getPrimaryKey();
			$currentLevel = $node->{$this->levelAttribute};
			$item = array(
				'id' => $primary,
				'key' => $primary,
				'title' => $node->getNodeTitle(),
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
	 * Generate the next value of the prefixed attribute
	 *
	 * @param string $attribute the name of attribute
	 * @param string $prefix the prefix of the value
	 * @return string the next value of the prefixed attribute
	 */
	public function getNextMaxValue($attribute = 'name', $prefix = 'new_')
	{
		$SSQL = "SELECT MAX(" . $attribute . ") FROM " . $this->tableName() . " WHERE " . $attribute . " like '$prefix%'";
		$value = Yii::app()->db->createCommand($SSQL)->queryScalar();
		$parts = explode($prefix, $value);
		$value = (isset($parts[1])) ? intval($parts[1]) : 0;
		return $prefix . ($value + 1);
	}

	/**
	 * Get node titlte by titleAttribute property
	 * @return string the title of the item
	 */
	public function getNodeTitle()
	{
		$owner = $this->getOwner();
		return $owner->{$this->titleAttribute};
	}

}