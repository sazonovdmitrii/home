<?php
/**
 * @var $model IndexProductPhoto
 */


$this->breadcrumbs=array(
	'Изображения для вкладок'=>array('index'),
	'Список',
);

/** @var $cs CustomClientScript */
$cs = Yii::app()->getClientScript();

$cs->registerScript('search', "
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('index-product-photo-grid', {
			data: $(this).serialize()
		});
		return false;
	});
");

$cs->registerScriptFile('/js/context/jquery.jeegoocontext.min.js');
$cs->registerScriptFile('/js/context/jquery.livequery.js');
$cs->registerCssFile('/js/context/skins/cm_default/style.css');

$cs->registerScript('context', "
        $('.item_status').jeegoocontext('status-list', {
                livequery: true,
		startLeftOffset: -187,
		startTopOffset: -85,
                widthOverflowOffset: 0,
                heightOverflowOffset: 1,
                submenuLeftOffset: -4,
                submenuTopOffset: -2,
                event: 'click',
                openBelowContext: false,
                onSelect: function(e, context){
			$.ajax({
                                url: '".$this->createUrl($this->id.'/ajaxStatusUpdate')."',
                                data: {
                                	id: $(this).data('id'),
                                	status: $(this).data('status_id')
                                },
                                async: false,
                                success: function(){
                                	window.location.reload();
                                },
                                error:function(){
                                	//window.location.reload();
                                }
                        });
                },
                onShow: function(e, context){
                        $.ajax({
                                url: '".$this->createUrl($this->id.'/ajaxStatusList')."',
                                data: { id:$(context).data('id') },
                                async: false,
                                dataType:'json',
                                success: function(data){
                                	$('#status-list').html(data.html);
                                },
                                error:function(){
                                	//window.location.reload();
                                }
                        });
                }
            });
", CClientScript::POS_READY);

$cs->registerCss('context', '
	.item_status:hover {
		cursor: pointer;
	}
');

?>

<h1>Список изображений</h1>

<!--  Context menu -->
<ul id="status-list" class="jeegoocontext cm_default"></ul>

<?php echo CHtml::link('Фильтр','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<div style="margin-bottom: 10px;"></div>
<?php echo CHtml::link('Добавить', array('/admin/indexProductPhoto/create'), array('class' => 'primary btn')); ?>

<?php $this->widget('ext.bootstrap.widgets.BootGridView', array(
	'id'           => 'index-product-photo-grid',
	'dataProvider' => $model->search(),
	'columns'      => array(
		'id',
		'name',
		array(
			'name' => 'type',
			'type' => 'raw',
			'value' => 'IndexProductPhoto::$typeName[$data->type]',
		),
		array(
			'name' => 'image_id',
			'type' => 'raw',
			'value' => 'CHtml::image($data->getImageFullPath(), "", array("height" => 80))'
		),
		array(
			'name' => 'status',
			'type' => 'raw',
			'value' => '$data->getStatusHtml()',
		),
		'price',
		array(
			'name' => 'update_time',
			'value' => 'date("d.m.Y H:i", $data->update_time)',
		),
		array(
			'class' => 'CButtonColumn',
			'template' => '{update} {delete}'
		),
	),
)); ?>
