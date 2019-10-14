<?php
$this->breadcrumbs=array(
	'Adv Questions'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List AdvQuestion','url'=>array('index')),
	array('label'=>'Create AdvQuestion','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('adv-question-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Adv Questions</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('ext.bootstrap.widgets.BootGridView',array(
	'id'=>'adv-question-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'author_name',
		'email',
		'question',
		'status',
		'create_time',
		/*
		'update_time',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
