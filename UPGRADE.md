Инструкции по обновлению для Helper Class
=========================================


Upgrade from Helper 0.7.5
-----------------------
re `yii2/MetaTag`
```php
// Before 0.7.5
MetaTag::tag($this->view, [tags]);
// Since 0.7.5
(new MetaTag($this->view))->tag([tags]);
```

Upgrade from Helper 0.7.4
-----------------------
- add in ArrayHelper: implodeMulti(), arrayToObject(), array2Object(), objectToArray(), object2Array().
- edit in StringHelper: replaceBBCode() - add: `hr, h1-6, ul, ol,li`.

## If you use Yii2 and class YiiHelper
- rename YiiHelper → Helper
    ```php
    // Before 0.7.4
    use \denisok94\helper\YiiHelper;
    // Since 0.7.4
    use \denisok94\helper\yii2\Helper;
    ```
