Просто классы с набором функций.

Доки чуть позже)

![https://img.shields.io/badge/license-BSD-green](https://img.shields.io/badge/license-BSD-green)
___
## Установка

Run:
```
php composer require --prefer-dist denisok94/helper
```

or add to the `require` section of your `composer.json` file:

```json
"denisok94/helper": "*"
```
```
php composer update
```
___

0. [Helper](#Helper)
1. [ArrayHelper](#ArrayHelper)
2. [DataHelper](#DataHelper)
3. [StringHelper](#StringHelper)
4. [OtherHelper](#OtherHelper)
5. [HtmlHelper](#HtmlHelper)
6. [Yii2](#Yii2)
    1. [MetaTag](#MetaTag)
    2. [H](#H)

___
## Helper
```php
use \denisok94\helper\Helper as H;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
|  |  |  |
___


___
## ArrayHelper
```php
use \denisok94\helper\ArrayHelper as A;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| get |  | Найти в массиве |
| set |  | Добавить/заменить в массиве |
| parse |  | Заменить шаблон |
| implodeWrap |  |  Объединяет элементы массива в строку, обернуть текст в кавычки |
| implodeWith |  |  Объединяет элементы массива в строку, с пред обработкой |
| toJson |  |  |
| toArray |  |  |
___

## DataHelper
```php
use \denisok94\helper\DataHelper as D;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| currentDate |  |  |
| currentDt |  |  |
| toMysqlDate |  |  |
| toMysqlDt |  |  |
| toRuDate |  |  |
| toRuDt |  |  |
| getTodayDb |  |  |
| toMysqlDtU |  |  |

___
## StringHelper
```php
use \denisok94\helper\StringHelper as S;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| uuid |  | Сгенерировать uuid v4 |
| random |  | Рандомная строка |

___
## OtherHelper
```php
use \denisok94\helper\OtherHelper as O;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| curl |  | curl для большинства простых запросов |

___
## HtmlHelper
```php
use \denisok94\helper\HtmlHelper as HH;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| video |  |  |


___
### Yii2

====
## MetaTag
```php
use \denisok94\helper\yii2\MetaTag;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| tag |  |  |

___
## H
```php
use \denisok94\helper\yii2\H;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| exec |  |  |
| setCache |  |  |
| getCache |  |  |