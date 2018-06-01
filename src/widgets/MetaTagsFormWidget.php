<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/20/18
 * Time: 16:57
 */

namespace naffiq\bridge\widgets;

class MetaTagsFormWidget extends TranslationFormWidget
{
    public function run()
    {
        return $this->render('meta-tags-form', [
            'languages' => $this->languages,
            'form' => $this->form,
            'model' => $this->model,
            'viewName' => $this->viewName
        ]);
    }
}