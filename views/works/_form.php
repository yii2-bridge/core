<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\tinymce\TinyMce;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Works */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-9">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'is_active')->checkbox() ?>

        <?= $form->field($model, 'description')->widget(TinyMce::className(), [
            'options' => ['rows' => 6],
            'clientOptions' => [
                'plugins' => ['image', 'link'],
                'branding' => false,
                'images_upload_handler' => new \yii\web\JsExpression(<<<JS
                function (blobInfo, success, failure) {
    var xhr, formData;

    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '/admin/default/image-upload');

    xhr.onload = function() {
      var json;

      if (xhr.status != 200) {
        failure('HTTP Error: ' + xhr.status);
        return;
      }

      json = JSON.parse(xhr.responseText);

      if (!json || typeof json.location != 'string') {
        failure('Invalid JSON: ' + xhr.responseText);
        return;
      }

      success(json.location);
    };

    formData = new FormData();
    formData.append(yii.getCsrfParam(), yii.getCsrfToken());
    formData.append('file', blobInfo.blob(), blobInfo.filename());

    xhr.send(formData);
  }
JS
),
                'file_picker_types' => 'image',
                'file_picker_callback' => new \yii\web\JsExpression(<<<JS
 function(cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function() {
                var file = this.files[0];

                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
      };
            };

            input.click();
        }
JS
                )
            ]
        ]) ?>

        <?= $form->field($model, 'excerpt')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    </div>
    <div class="col-md-3">
        <?= Html::img($model->getUploadUrl('thumb'), ['class' => 'img-thumbnail']) ?>
        <?= $form->field($model, 'thumb')->fileInput(['accept' => 'image/*']) ?>

        <?= Html::img($model->getUploadUrl('thumb'), ['class' => 'img-thumbnail']) ?>
        <?= $form->field($model, 'thumb_2x')->fileInput(['accept' => 'image/*']) ?>
    </div>
</div>


<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>
