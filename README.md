Класс с набором полезных функций.

Доки чуть позже)

![https://img.shields.io/badge/license-BSD-green](https://img.shields.io/badge/license-BSD-green)
___

0. [Установка](#Установка)
1. [Helper](#Helper)
2. [ArrayHelper](#ArrayHelper)
3. [DataHelper](#DataHelper)
4. [StringHelper](#StringHelper)
5. [FileHelper](#FileHelper)
6. [HtmlHelper](#HtmlHelper)
7. [OtherHelper](#OtherHelper)
8. [Yii2](#Yii2)
    1. [MetaTag](#MetaTag)
    2. [Other](#Other)

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

# Использование:
```php
use \denisok94\helper\Helper as H;
```

Можно создать в любом удобном месте своего приложения файл `H.php` с классом `H` и унаследовать его от `Helper`.

Внутри класса `H` добавить свои функции с повторяющемся действиями или перезаписать имеющиеся в `Helper`.

```php
namespace app\componets;
use \denisok94\helper\Helper;
class H extends Helper {}
```

И в дальнейшем использовать его.

```php
use app\componets\H;
```

# include

Если вы скачали репозиторий архивом (zip/tar).

Можно создать в любом удобном месте своего приложения файл `H.php` с классом `H` и унаследовать его от `FHelper`.

```php
include_once '{path to repository}/src/FHelper.php';
class H extends FHelper {}
```

___
## ArrayHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| get |  | Найти в массиве |
| set |  | Добавить/заменить в массиве |
| parse |  | Заменить шаблон |
| implodeWrap |  |  Объединяет элементы массива в строку, обернуть текст в кавычки |
| implodeWith |  |  Объединяет элементы массива в строку, с пред обработкой |
| toJson |  |  |
| toArray |  |  |
| arrayOrderBy |  |  |

___
## DataHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| currentDate |  |  |
| currentDt |  |  |
| toMysqlDate |  |  |
| toMysqlDt |  |  |
| toRuDate |  |  |
| toRuDt |  |  |
| getTodayDb |  |  |
| stampToDtU |  |  |
| stampToDt |  |  |

___
## StringHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| uuid |  | Сгенерировать uuid v4 |
| random |  | Рандомная строка |

___
## FileHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| readFile |  | Показать содержимое файла |
| getFileDt |  | Получить дату последнего изменения |
| dirSize |  | Получить размер папки |
| ext |  | Получить расширение файла |
| fileType |  | Получить тип файла |
| fileIcon |  | Получить название иконки для файла |
| fileIconFa |  | Получить название иконки для файла в формате Font Awesome 4/5 |

___
## HtmlHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| video |  |  |

___
## OtherHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| curl |  | curl для большинства простых запросов |
| getUserIp |  | получить IP пользователя |
| isBot |  | Проверка пользователя на бота |
| msleep |  | уснуть на N секунд |

___
### Yii2
```php
use \denisok94\helper\YiiHelper;
```

`YiiHelper` наследует все от `Helper`.

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| exec |  | Выполнить консольную команду |
| setCache |  |  |
| getCache |  |  |

Можно создать в папке `componets` файл `H.php` и унаследовать его от `YiiHelper`.

```php
namespace app\componets;
use \denisok94\helper\YiiHelper;
class H extends YiiHelper {}
```
И подключать его более лаконичным путём )
```php
use app\componets\H;
```

___
## MetaTag
```php
use \denisok94\helper\yii2\MetaTag;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| tag |  | Установить MetaTag на страницу |