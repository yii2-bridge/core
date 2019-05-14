### Version 0.3.0
- Added '$layout' property for `BridgeModule`. If you need, you can install your own layout or edit existing. [#43](https://github.com/yii2-bridge/core/pull/43)
- Now, in your project config you can override bridge views. For [example](https://github.com/yii2-bridge/core/wiki/Custom-view-path). [#45](https://github.com/yii2-bridge/core/pull/45)
- The submenu now also becomes highlighted if it is active. [#42](https://github.com/yii2-bridge/core/pull/42)
- Added a caret icon and animation to menu items which have a sub-menu. [#40](https://github.com/yii2-bridge/core/pull/40)
- Fixed bug with migration. [#46](https://github.com/yii2-bridge/core/pull/46)
- Fixed missing translation in dropdown menu. [#39](https://github.com/yii2-bridge/core/pull/39)

### Version 0.2.5
- Fixed bug with `extraAssets`
- Fixed bug with method `setIsAdmin` in `bridge` component

### Version 0.2.4
- Changed the order of the execution of migration commands
- Removed unused packages
- Fixed bug when extend `ImageColumn` and `TitledImageColumn` classes
- Fixed bug in `I18n` module
- Fixed bug in `MetaTagsComponent`
- Fixed bug when delete file via `fileUpload()` or `imageUpload()` fields 

### Version 0.2.3

- Fixed bug when use `ImageColumn` and `TitledImageColumn`
- Fixed bug when get *og:image* in `MetaTagsComponent`
- Fixed bug in `MetaTags` when use translation model

### Version 0.2.2

- Added default options for `richTextArea` field of `ActiveFiled`. By default, the `allowedContent` and `forcePasteAsPlainText` options are enabled
- Fixed bug `imageUpload` and `fileUpload` fields of `ActiveFiled`
- Fixed bug when delete image in `Settings`
- Updated index in `meta_tag_translations` table
- Fixed bug when get `Settings`
- Fixed close sub-menu when menu is narrow
- Fixed bug when generate rules for `TINYINT` column
- Optimized queries in the preparation of meta-tags

#### Deprecation notice ⚠️
Remove the `imageColumn` attribute from the configuration `MetaTagBehavior`.
When calling `Yii::$app->metaTags->registerModel()`, simply specify as the third parameter the name of the image loading behavior (`BridgeUploadImageBehavior`).<br>
The default name is `imageUpload`.<br>
Example: `Yii::$app->metaTags->registerModel($model, 'metaTag', 'imageUpload')`

### Version 0.2.1

- Fixed clear cache `Setting` and `SettingGroup`
- Fixed create setting translations if they don't exist
- Fixed bug `Invalid path alias: @/Bridge/Core/Controllers` when run command `php yii`

### Version 0.2.0

- The `Settings` value is now multilingual. By default it gets the value for the current language.
If you want to get a value for a specific language, you can simply specify the language code.
For example: `Settings::group('test-group')->get('test-setting')->getTranslation('en-RU')`.
- Caching  `Settings`. By default, settings caching is enabled. If you want to disable caching, add the `'settingsCaching' => false` to the admin module configuration.
Also, you can override the name of the cache key settings in the admin module configuration.
For example: `'settingsCacheKey' => 'my_settings'`
- Web analytics services (`google-analytics-key` and `yandex-metrika-key`) are registered everywhere except the admin area. Earlier they were also registered in the admin panel, which spoiled the statistics.
- Added the "**Clear cache**" button, which clears all caches.
- In `TranslationBehavior`, you can now specify the cache key, which makes it possible to retrieve the translation data from the cache.
Earlier we received data in approximately this way: `$model->translation->title`.
Now, to cache the translation data, it is enough to specify the unique cache key with the second parameter of the `getTranslation()` method: `$model->getTranslation(null, 'post-' . $model->id)->title`. (with the first argument, we set null to get the value for the current language).
- New translations for Russian and Kazakh languages have been added.
- Also fixed minor bugs.
----------------------

# Older versions

### Version 0.9.1

- Fixed admin layout `Settings` usage
- Created `BridgeComponent` that stores app state and settings and accessible by calling `Yii::$app->bridge`. 
Currently allows developer to check if user is in admin panel or not (`Yii::$app->bridge->isAdmin`)
- Fixed #48 — when no translation data is passed in post request behavior was throwing exception.

### Version 0.9.0

#### Deprecation notice ⚠️

Since Bridge v0.9.0 brings Settings group feature it will no longer support
`Settings::getOrCreate('email')` function (as well as `create` and `get` functions). Use `Settings::group('contacts')->getOrCreate('email')` instead.
We do that in order to force developers to make better user interfaces and experience to content moderators.

You can use `Settings::misc()->getOrCreate('settings_i_didn\'t_find_a_place_for')` for
settings that you did not find places for.

Those methods will be deleted in `v1.0.0`. In order to migrate to new syntax you can set
`group_id`.

Thanks.

#### Changelog

- Added `Kolyunya/yii2-map-input-widget` and new widget to `ActiveField`. 
In order to provide Google maps api API key to widget,  
- Added new settings type for maps.
- Added `vlucas/phpdotenv` dependency for local development setup. Refer to `.env.example`.
- Created console Yii2 app in `bin` directory for development purposes (to create migrations and etc). 
You can run it with `php bin/yii.php`.
- Added `./bin/bridge-install-dev` install script for dev and test/CI environment.
- Created `SettingsGroups` model
- Created `FontAwesomePicker` widget and added it to `ActiveField`
- Added `no-panel` View param support in order to hide admin layout's bootstrap panel
- Updated Settings UI
- Changed `ActiveField::relationalDropDown` widget code for flexibility.
- Fixed `Toastr` widget.
- Fixed `TranslationBehavior`.

### Version 0.8.4

- Fixed `TranslationBehavior` save method
- Added ability to set footer text

### Version 0.8.3

- Fixed issue of overwriting app `i18n` config (translations) in BridgeModule
- Fixed issue of duplicating translations in translations view (index)   

### Version 0.8.1

- Fixed `i18n` and `urlManager` components setup

### Version 0.8.0

- Created CHANGELOG
- Added dependency to `zelenin/yii2-i18n-module` ([Repo link](https://github.com/zelenin/yii2-i18n-module))
- Added `BridgeModule::$languageInitHandler` field. It defines the way language is initialized. 
Accepts anything for `call_user_func()` PHP function.
**By default** language is stored in cache under `lang` key.
- Added `BridgeModule::$languages` array. You can pass both associative arrays or array for `call_user_func()` function.
In case `languages` is associative, key is language identifier, such as `ru-RU` or `kk-KZ` and value is label (`Русский`, `Qazaqsha`).
**Default** is ` [ 'en-US' => 'EN', 'ru-RU' => 'RU', 'kk-KZ' => 'KZ' ] `
- Added `BridgeModule::$languageSwitchAction` property. **By default** it's `\naffiq\bridge\controllers\actions\LanguageSwitchAction`.
It stores language code to cache under `lang` key.
- Added `BridgeModule::$showLanguageSwitcher` property. **Default:** `true`
- Added `Translations` menu item to admin panel that is visible, if `BridgeModule::$showLanguageSwitcher` is `true`. 
- Added language switch dropdown to main layout that is visible, if `BridgeModule::$showLanguageSwitcher` is `true`.
- Added `defaultImageWidth` and `defaultImageHeight` support to `options` param of `ActiveField::richTextArea()` method. 
Sets default image size for CKEditor image plugin. Currently limited to same size per view.
- Added `codemix/yii2-localeurls` for managing current language via URLManager.
- Added `TranslationFormWidget` for managing translations and added it to `ActiveForm`
- Added `TranslationBehavior` for saving and getting translations from `ActiveRecord`s
