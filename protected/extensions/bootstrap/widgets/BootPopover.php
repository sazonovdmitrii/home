<?php
/**
 * BootPopover class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @since 0.9.2
 */

Yii::import('ext.bootstrap.widgets.BootWidget');
class BootPopover extends BootWidget
{
	/**
	 * @property string the CSS selector to use for selecting the pop-over elements.
	 */
	public $selector = '.pop';
	
	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		parent::init();
		$this->registerScriptFile('jquery.ui.boottwipsy.js');
		$this->registerScriptFile('jquery.ui.bootpopover.js');
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$id = $this->getId();
		$options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
        $this->registerScript(__CLASS__.'#'.$id,"jQuery('{$this->selector}').bootPopover($options);");
	}
}
