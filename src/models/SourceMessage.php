<?php

namespace Bridge\Core\Models;

use Bridge\Core\Models\Query\SourceMessageQuery;
use Zelenin\yii\modules\I18n\models\SourceMessage as BaseSourceMessage;

class SourceMessage extends BaseSourceMessage
{
  public static function find()
  {
    return new SourceMessageQuery(get_called_class());
  }
}
