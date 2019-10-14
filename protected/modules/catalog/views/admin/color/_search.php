<?php $form=$this->beginWidget('ext.bootstrap.widgets.BootActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<?php echo $form->textFieldRow($model,'id',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'desc',array('class'=>'span5','maxlength'=>3000)); ?>

	<?php echo $form->textFieldRow($model,'param',array('class'=>'span5','maxlength'=>45)); ?>


	<div class="actions">
		<?php echo CHtml::submitButton('Поиск',array('class'=>'btn primary')); ?>
	</div>

<?php $this->endWidget(); ?>
