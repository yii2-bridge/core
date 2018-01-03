<?php
/**
 * @var \yii\web\View $this
 */

use yii\helpers\Url;

$this->title = \Yii::t('bridge', 'File manager');
$this->params['breadcrumbs'][] = $this->title;
?>

<iframe src="<?= Url::to(['/admin/elfinder/manager']) ?>" frameborder="0" width="100%" height="600px"></iframe>
