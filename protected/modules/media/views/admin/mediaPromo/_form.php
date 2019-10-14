<?php $form=$this->beginWidget('ext.bootstrap.widgets.BootActiveForm',array(
	'id'=>'media-promo-form',
	'enableAjaxValidation'=>false,
	'stacked' => true
)); ?>


	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'title',array('class'=>'span7','maxlength'=>255)); ?>

	<?php echo $form->textAreaRow($model,'lead',array('class'=>'span7', 'rows' => 4, 'maxlength'=>500)); ?>

	<?php echo $form->textFieldRow($model,'url',array('class'=>'span7','maxlength'=>255)); ?>

	<?php
	if ($model->image_id)
		$this->renderPartial('_imagePreview', array('model' => $model, 'image' => UploadedFile::model()->findByPk($model->image_id)));
	else
		echo $form->fileFieldRow($model,'image_id',array('class'=>'span7', 'id' => 'preview_image'));
	?>

	<script>
		$(document).ready(function(){

			$('#preview_image').live('change', function(){
				var file = this.files[0];
				var url = '/media/admin/mediaPromo/uploadPreview/id/<?php echo $model->id;?>';

				uploadFile(file, url, function(response){
					$('#preview_image').parents('.clearfix').replaceWith(response);

				});
			});


			function uploadFile(file, url, callback) {
				var xhr = new XMLHttpRequest();
				var formData = new FormData();
				// Событие, вызванное по итогу отправки очередного файла
				xhr.onreadystatechange = function(){
					if(this.readyState == 4) {
						if(this.status == 200) {
							// some handler
						}
						delete file;
						delete this;
						if(callback != undefined) callback(this.responseText);
					}
				}
				xhr.open("POST", url);
				formData.append('MediaPromo[upload]', file);
				xhr.send(formData);
			}


			$('.delete_preview').live('click', function(){
				var $this = $(this);
				$.post(
					'/media/admin/mediaPromo/deletePreview/',
					$this.data(),
					function(response){
						if (response == 'success') {
							$this.parents('.clearfix').remove();
						} else {
							alert('Ошибка при удалении Превью');
						}

					}
				);

				return false;
			})

		});
	</script>


	<?php echo $form->dropDownListRow($model, 'status', MediaPromo::$statusNames); ?>

	<div class="actions">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить',array('class'=>'btn primary')); ?>
	</div>

<?php $this->endWidget(); ?>
