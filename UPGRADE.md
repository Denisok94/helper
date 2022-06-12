Инструкции по обновлению для Helper Class
=========================================

Upgrade from Helper 0.8.0
-----------------------
remove yii2 (12.06.2022)
A new separate repository has been created:
- class [MetaTag](https://github.com/Denisok94/yii-metatag)
    ```bash
    composer require --prefer-dist denisok94/yii-metatag
    ```
- class [ConsoleController and StatusController](https://github.com/Denisok94/yii-helper)
    ```bash
    composer require --prefer-dist denisok94/yii-helper
    ```

Upgrade from Helper 0.7.7
-----------------------
- `OtherHelper::curl` - fix post fields
- update `yii2\StatusController`

Upgrade from Helper 0.7.6
-----------------------
- `MicroTimer` namespace \helper → \helper\other
    ```php
    // Before 0.7.6
    use \denisok94\helper\MicroTimer;
    // Since 0.7.6
    use \denisok94\helper\other\MicroTimer;
    ```

Upgrade from Helper 0.7.5
-----------------------
update `yii2/MetaTag`
```php
// Before 0.7.5
MetaTag::tag($this->view, [tags]);
// Since 0.7.5
(new MetaTag($this->view))->tag([tags]);
```

Upgrade from Helper 0.7.4
-----------------------
## If you use Yii2 and class YiiHelper
- rename YiiHelper → Helper
    ```php
    // Before 0.7.4
    use \denisok94\helper\YiiHelper;
    // Since 0.7.4
    use \denisok94\helper\yii2\Helper;
    ```
