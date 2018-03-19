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
