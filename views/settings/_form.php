<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\tinymce\TinyMce;
use app\modules\admin\models\Settings;

/* @var $this yii\web\View */
/* @var $model Settings */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-lg-5">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?php if ($model->type == Settings::TYPE_TEXT) : ?>
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
        <?php elseif ($model->type == Settings::TYPE_IMAGE) : ?>
            <?= Html::img($model->getUploadUrl('value'), ['class' => 'img-thumbnail']) ?>
            <?= $form->field($model, 'value')->fileInput(['accept' => 'image/*']) ?>
        <?php else: ?>
            <?= $form->field($model, 'value')->textInput() ?>
        <?php endif; ?>
    </div>
    <div class="col-md-4">

        <button type="button" class="btn btn-default"
                data-toggle="collapse" data-target="#advancedSettings" aria-expanded="false"
                aria-controls="advancedSettings">
            Advanced settings
        </button>

        <div id="advancedSettings" class="collapse">
            <br/>

            <div class="alert alert-danger">
                <b>Warning!</b> Don't edit, if you are not familiar what those do!
            </div>

            <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(Settings::$types) ?>

            <?= $form->field($model, 'type_settings')->textarea(['rows' => 6]) ?>
        </div>
    </div>
</div>
<div class="form-group clearfix">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
