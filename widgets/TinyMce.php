<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 7/19/2017
 * Time: 12:24 AM
 */

namespace naffiq\bridge\widgets;


use dosamigos\tinymce\TinyMce as BaseTinyMce;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class TinyMce
 *
 * Overrides dosamigos\tinymce and provides default configuration for image upload handling
 *
 * @package app\widgets
 */
class TinyMce extends BaseTinyMce
{
    /**
     * @var string|array Route or url for image upload callback
     */
    public $imageUploadLink;

    public function __construct(array $config = [])
    {
        $uploadLink = empty($config['imageUploadLink']) ? '/admin/default/image-upload' : $config['imageUploadLink'];
        if (is_array($uploadLink)) {
            $uploadLink = Url::to($uploadLink);
        }

        parent::__construct(ArrayHelper::merge([
            'clientOptions' => [
                'plugins' => ['image', 'link'],
                'branding' => false,
                'images_upload_handler' => new \yii\web\JsExpression(<<<JS
                function (blobInfo, success, failure) {
    var xhr, formData;

    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '$uploadLink');

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
        ], $config));
    }
}