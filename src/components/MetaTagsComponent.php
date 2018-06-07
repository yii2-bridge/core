<?php
/**
 * Created by PhpStorm.
 * User: rocketscientist
 * Date: 28.04.2018
 * Time: 17:57
 */

namespace naffiq\bridge\components;

use naffiq\bridge\behaviors\MetaTagBehavior;
use naffiq\bridge\models\MetaPage;
use naffiq\bridge\models\MetaTagTranslation;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;

class MetaTagsComponent extends Component
{
    /**
     * Изображение по-умолчанию для мета-тега 'og:image'
     * @var string
     */
    public $defaultMetaImage = '';

    /**
     * Задаем мета-теги для model-страницы (страница которая связана конкретно с моделю)
     * Убедитесь что вы подключили поведение мета-тегов в вашей модели
     * Также можно задавать атрибуты мета-тегов по-умолчанию, если они не заданы
     *
     * Пример:
     *  public function behaviors()
     *  {
     *      return [
     *          ...
     *          'metaTag' => [
     *              'class' => 'naffiq\bridge\behaviors\MetaTagBehavior',
     *              'titleColumn' => 'title',
     *              'descriptionColumn' => 'description',
     *          ],
     *          ...
     *      ];
     *  }
     *
     * @param ActiveRecord $model
     * @param string $metaTagBehaviorName
     * @param string $imageUploadBehaviorName
     * @internal param string $behaviorName
     */
    public function registerModel(ActiveRecord $model, string $metaTagBehaviorName = 'metaTag', string $imageUploadBehaviorName = 'imageUpload')
    {
        $metaTagBehavior = $model->getBehavior($metaTagBehaviorName);

        if (!$metaTagBehavior || !(get_class($metaTagBehavior) === MetaTagBehavior::class)) {
            throw new InvalidArgumentException('Вы не указали поведение MetaTagBehavior в модели ' . get_class($model));
        }

        $this->registerMetaTitle($this->getModelMetaTitle($model, $metaTagBehavior));
        $this->registerMetaDescription($this->getModelMetaDescription($model, $metaTagBehavior));
        $this->registerMetaUrl();
        $this->registerMetaSiteName();
        $this->registerMetaImage($this->getModelMetaImage($model, $metaTagBehavior, $imageUploadBehaviorName));
    }

    /**
     * Задаем мета-теги для action-страницы (страница которая не связана с какой либо моделю)
     *
     * Можно задавать мета-теги по-умолчанию, если еще для этой страницы не заданы мета-теги
     * Потом их можно отредактировать в административном панели, в меню 'Мета-теги'
     *
     * Пример для значении по-умолчанию:
     * [
     *  'en-US' => [
     *      'lang' => 'en-US',
     *      'title' => 'Title'
     *  ],
     *  'ru-RU' => [
     *      'lang' => 'ru-RU',
     *      'title' => 'Заголовок'
     *      ],
     *  'kk-KZ' => [
     *      'lang' => 'kk-KZ',
     *      'title' => 'Тақырып'
     *  ]
     * ]
     *
     * @param array $defaultParams
     */
    public function registerAction($defaultParams = [])
    {
        $module = Yii::$app->controller->module->id;
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;

        $metaPage = MetaPage::getOrCreate($module, $controller, $action, $defaultParams);

        $this->registerMetaTitle($metaPage->metaTag->translation->title);
        $this->registerMetaDescription($metaPage->metaTag->translation->description);
        $this->registerMetaUrl();
        $this->registerMetaSiteName();
        $this->registerMetaImage($this->getActionMetaImage($metaPage->metaTag->translation));
    }

    /**
     * Задаем для страницы Meta Title, Meta Open Graph Title, Meta Twitter Card Title
     * Пример:
     *  <title>Главная страница</title>
     *  <meta property="og:title" content="Главная страница">
     *  <meta property="twitter:title" content="Главная страница">
     *
     * @param string $title
     */
    public function registerMetaTitle(string $title)
    {
        Yii::$app->view->title = $title;

        // Open Graph data
        Yii::$app->view->registerMetaTag(['property' => 'og:title', 'content' => $title], 'og:title');

        // Twitter Card data
        Yii::$app->view->registerMetaTag(['property' => 'twitter:title', 'content' => $title], 'twitter:title');
    }

    /**
     * Задаем для страницы Meta Description, Meta Open Graph Description, Meta Twitter Card Description
     * Пример:
     *  <meta name="description" content="Описание">
     *  <meta property="og:description" content="Описание">
     *  <meta property="twitter:description" content="Описание">
     *
     * @param string $description
     */
    public function registerMetaDescription(string $description)
    {
        $description = StringHelper::truncate(strip_tags(html_entity_decode(str_replace('&nbsp;', ' ', $description))), 255);

        Yii::$app->view->registerMetaTag(['name' => 'description', 'content' => $description], 'description');

        // Open Graph data
        Yii::$app->view->registerMetaTag(['property' => 'og:description', 'content' => $description], 'og:description');

        // Twitter Card data
        Yii::$app->view->registerMetaTag(['property' => 'twitter:description', 'content' => $description], 'twitter:description');
    }

    /**
     * Задаем для страницы Meta Open Graph Url
     * По-умолчанию задается текущая ссылка
     * Пример:
     *  <meta name="og:url" content="http://site.kz">
     *
     * @param string|null $url
     */
    public function registerMetaUrl(string $url = null)
    {
        // Open Graph data
        Yii::$app->view->registerMetaTag(['property' => 'og:url', 'content' => $url ?? Url::to('', true)], 'og:url');
    }

    /**
     * Задаем для страницы Meta Open Graph Site Name
     * По-умолчанию задается название приложения, которую вы указали в конфигурациия приложения, в ключе 'name' ('name' => 'My Application')
     * Пример:
     *  <meta name="og:site_name" content="My Application">
     *
     * @param string|null $siteName
     */
    public function registerMetaSiteName(string $siteName = null)
    {
        // Open Graph data
        Yii::$app->view->registerMetaTag(['property' => 'og:site_name', 'content' => $siteName ?? Yii::$app->name], 'og:site_name');
    }

    /**
     * Задаем для страницы Meta Open Graph Image
     * По-умолчанию задается null
     * Пример:
     *  <meta name="og:image" content="http://example.com/image.jpg">
     *
     * @param string|null $image
     */
    public function registerMetaImage(string $image = null)
    {
        // Open Graph data
        Yii::$app->view->registerMetaTag(['property' => 'og:image', 'content' => $image], 'og:image');
    }

    /**
     * Получаем Meta Title для страницы модели
     * Если для данной модели незадано Meta Title, то получаем значение из ключа 'titleColumn', который указано в поведении MetaTagBehavior
     * Если незадано ни Meta Title, ни 'titleColumn' в поведении MetaTagBehavior,
     * то задаем название приложения, которую вы указали в конфигурациия приложения, в ключе 'name' ('name' => 'My Application')
     *
     * @param ActiveRecord $model
     * @param MetaTagBehavior $metaTagBehavior
     * @return string
     */
    private function getModelMetaTitle(ActiveRecord $model, MetaTagBehavior $metaTagBehavior)
    {
        $title = $metaTagBehavior->metaTag->translation->title
            ? $metaTagBehavior->metaTag->translation->title
            : ArrayHelper::getValue($model, $metaTagBehavior->titleColumn, Yii::$app->name);

        return $title;
    }

    /**
     * Получаем Meta Description для страницы модели
     * Если для данной модели незадано Meta Description, то получаем значение из ключа 'descriptionColumn', который указано в поведении MetaTagBehavior
     * Если незадано ни Meta Description, ни 'descriptionColumn' в поведении MetaTagBehavior,
     * то задаем название приложения, которую вы указали в конфигурациия приложения, в ключе 'name' ('name' => 'My Application')
     *
     * @param ActiveRecord $model
     * @param MetaTagBehavior $metaTagBehavior
     * @return string
     */
    private function getModelMetaDescription(ActiveRecord $model, MetaTagBehavior $metaTagBehavior)
    {
        $description = $metaTagBehavior->metaTag->translation->description
            ? $metaTagBehavior->metaTag->translation->description
            : ArrayHelper::getValue($model, $metaTagBehavior->descriptionColumn, Yii::$app->name);

        return $description;
    }

    /**
     * Получаем Meta Image для страницы модели
     * Если для данной модели незадано Meta Image, то получаем значение из ключа 'imageColumn', который указано в поведении MetaTagBehavior
     * Если незадано ни Meta Image, ни 'imageColumn' в поведении MetaTagBehavior,
     * то задается null
     *
     * @param ActiveRecord $model
     * @param MetaTagBehavior $metaTagBehavior
     * @return string
     */
    private function getModelMetaImage(ActiveRecord $model, MetaTagBehavior $metaTagBehavior, $imageUploadBehaviorName)
    {
        if ($metaTagBehavior->metaTag->translation->image) {
            return Url::to($metaTagBehavior->metaTag->translation->getUploadUrl('image'), true);
        }
        $image = $model->getBehavior($imageUploadBehaviorName) ? Url::to($model->getUploadUrl($model->getBehavior($imageUploadBehaviorName)->attribute), true) : null;

        return $image;
    }

    /**
     * Получаем Meta Image для страницы экшен (страница которая не связано конкретно с моделью)
     * Если для данного экщега незадано Meta Image, то получаем значение из атрибута компонента мета-тегов $defaultMetaImage
     *
     * @param MetaTagTranslation $metaTagTranslationModel
     * @return string
     */
    private function getActionMetaImage(MetaTagTranslation $metaTagTranslationModel) {
        if ($metaTagTranslationModel->image) {
            return Url::to($metaTagTranslationModel->getUploadUrl('image'), true);
        }

        return $this->defaultMetaImage;
    }
}