<h1 align = "center"> Helper Class </h1>

Класс с набором полезных функций, по мнению автора.
Не претендует на идеальность и единственное верное решение.

A class with a set of useful functions, according to the author.
It does not pretend to be ideal and the only correct solution.

![https://img.shields.io/badge/license-BSD-green](https://img.shields.io/badge/license-BSD-green)
___

0. [Установка](#установка)
1. [Использование](#использование)
    1. [ArrayHelper](#arrayhelper)
    2. [DataHelper](#datahelper)
    3. [StringHelper](#stringhelper)
    4. [FileHelper](#filehelper)
    5. [HtmlHelper](#htmlhelper)
    6. [OtherHelper](#otherhelper)
2. [Other Class](#other-class)
    1. [MicroTimer](#microtimer)
    2. [Console](#console)
    3. [Session](#session)
    4. [S3DataService](#s3dataservice)
    5. [CloneObject](#cloneobject)
3. [Framework Integration](#framework-integration)
    1. [Yii2](#yii2)
    2. [Symfony](#symfony)

___

# Установка

Run:

```bash
composer require --prefer-dist denisok94/helper
# or
php composer.phar require --prefer-dist denisok94/helper
```

or add to the `require` section of your `composer.json` file:

```json
"denisok94/helper": "*"
```

```bash
composer update
# or
php composer.phar update
```

## Использование

```php
use \denisok94\helper\Helper as H;
H::methodName($arg);
```
___

# ArrayHelper

Работа с массивами

| Method | Description |
|----------------|:----------------|
| get | Найти в массиве по пути |
| set | Добавить/заменить элемент в массиве |
| parse | Заменить шаблон |
| implodeWrap | Объединяет элементы массива в строку + обернуть текст в кавычки |
| implodeWith | Объединяет элементы массива в строку, с предпользовательской обработкой |
| implodeByKey | Объединяет элементы массива в строку по ключу  |
| implodeByKeyWrap | Объединяет элементы массива в строку по ключу  + обернуть текст в кавычки |
| implodeMulti | Объединяет элементы многомерного массива в строку |
| isJson | проверяет данные на json |
| toJson | Преобразовать массив/объект в json |
| toArray | Преобразовать json в массив |
| arrayToObject | Преобразовать массив в объект |
| array2Object | Преобразовать массив в объект, вариант 2 |
| objectToArray | Преобразовать объект в массив |
| object2Array | Преобразовать объект в массив, вариант 2 |
| arrayOrderBy | Сортировка массива |
| unique_multidim_array | получить уникальные значения |

> arrayToObject и objectToArray - работают быстрее, но могут возникнуть исключения. array2Object и object2Array - использую преобразование через json_decode + json_encode, это более ресурсозатратные, но надёжнее.
___

# DataHelper

| Method | Description |
|----------------|:----------------|
| currentDate | Текущая дата |
| currentDt | Текущая дата и время |
| toMysqlDate | Преобразовать дату в формат Mysql |
| toMysqlDt | Преобразовать дату и время в формат Mysql |
| toRuDate | Русский формат даты |
| toRuDt | Русский формат даты и времени |
| stampToDt | Преобразовать timestamp в формат даты и времени |
| stampToDtU | Преобразовать timestamp в формат даты и времени с миллисекундами |
| yesterdayDate | Получить вчерашнюю дату |
| createDate |  |
| modifyDate |  |
| getStamp |  |

```php
H::createDate('yesterday'); // yesterday
H::createDate('-1 day'); // yesterday
H::createDate('1 day'); // tomorrow 
```
```php
H::modifyDate('2006-12-12', '-1 day'); // 2006-12-11
H::modifyDate(H::currentDate(), '+1 day'); // tomorrow
H::modifyDate(H::currentDt(), '-1 day', 'Y-m-d H:i:s'); // yesterday
```
```php
H::getStamp('22-09-2008 00:00:00', 'd-m-Y H:i:s'); // 1222030800 (This will differ depending on your server time zone...)
H::getStamp('22-09-2008 00:00:00', 'd-m-Y H:i:s', 'UTC'); // 1222041600
H::getStamp(H::currentDt())
```
___

# StringHelper

| Method | Description |
|----------------|:----------------|
| uuid | Сгенерировать uuid v4 |
| guid | Сгенерировать guid v4 |
| random | Сгенерировать рандомную строку |
| spell | падежи к числительным |
| slug | преобразовать строку в человекопонятный url |
| ru2Lat | Транслитирование, ГОСТ 7.79-2000, схема А |
| ruToLat | Транслитирование, ГОСТ 7.79-2000, схема Б |
| ru2Slug | преобразовать строку, на русском (схема А), в человекопонятный url |
| getClassName | Получить имя класса |
| slashes | экранирование |
| replaceBBCode | Парсинг BB-кодов |

## replaceBBCode

Поддержка:
- [hr]
- [h1-6]заголовок[/h1-6]
- [b]жирный[/b]
- \*\*жирный\*\*
- [i]курсив[/i]
- [u]подчеркнутый[/u]
- \_\_Подчеркнутый\_\_
- [s]зачеркнутый[/s]
- \~\~зачеркнутый\~\~
- [code]code[/code]
- [code=php]code[/code]
- \`\`\`code\`\`\`
- ||spoiler||
- [spoiler=спойлер]спойлер[/spoiler]
- [quote][/quote]
- [quote=][/quote]
- [url=][/url]
- [url][/url]
- [img][/img]
- [img=]
- [size=2][/size] в %
- [color=][/color]
- [list][/list] - ul
- [ul][/ul] - ul
- [listn][/listn] - ol
- [ol][/ol] - ol
- [\*][\*] - li
- [li][/li] - li
___

# FileHelper

Работа с файлами

| Method | Description |
|----------------|:----------------|
| ext | Получить расширение файла |
| fileRead | Показать содержимое файла |
| fileGetDt | Получить дату последнего изменения |
| fileType | Получить тип файла |
| fileIcon | Получить название иконки для файла |
| fileIconFa | Получить название иконки для файла в формате Font Awesome 4/5 |
| fileShortSize | короткий размер файла |
| shortSize | 2048 → 2.00 KB |
| parseSize | 2.00 KB → 2048 |
| dirSize | Получить размер папки |

___

# HtmlHelper

Генерация html тегов
> в разработке... 

| Method | Description |
|----------------|:----------------|
| video | видео тег |

___

# OtherHelper

| Method | Description |
|----------------|:----------------|
| curl | curl для большинства простых запросов |
| getRequest | параметры запроса |
| getUserIp | получить IP пользователя |
| isBot | Проверка пользователя на бота |
| msleep | уснуть на N секунд |
| percent | Высчитать процент |
| float | округлить до N знака |

> isBot() не даёт 100% гарантии.
Кому разрешить/запретить доступ/функционал - решать исключительно Вам.
Запрещая всё и всем, Вы можете лишится продвижения сайта в поисковых ресурсах и/или красивых привью в соц сетях =).
___

# Other Class

## MicroTimer

Узнать, сколько времени выполняется код

```php
use \denisok94\helper\other\MicroTimer;
$queryTimer = new MicroTimer(); // start
// code ...
$queryTimer->stop();

// result:
$time = $queryTimer->elapsed(); 
// or/and
printf($queryTimer);
```
> взято у [phpLiteAdmin](https://bitbucket.org/phpliteadmin/public/src/master/classes/MicroTimer.php)

___

## Console

| Method | Parameters | Return | Description |
|----------------|:---------:|:---------:|:----------------|
| __construct | ?array, ?mixed | throws | set required parameters and/or default value |
| get | string, ?default | value/default | get argument or option |
| getArgument | string, ?default | value/default | get argument |
| getArguments | - | array | get arguments |
| getOption | string, ?default | value/default | get option |
| getOptions | - | array | get options |
| hasArgument | string | true/false |  |
| hasOption | string | true/false |  |
| show | string | string |  |

```php
use \denisok94\helper\other\Console;
// php console.php arg1 arg2=val -o -a5 --option --option1=6 --option1=3
$console = new Console();
$console->getArguments(); // [arg1,arg2=>val]
$console->getArgument(0); // arg1
$console->getArgument('arg2'); // val
$console->getOptions(); // [o=>null,a=>5,option=>null,option1=>[6,3]]
```
Required parameters
```php
use \denisok94\helper\other\Console;
try {
    $console = new Console([
        'test', // required arguments and/or options
        'options' => ['test', ],  // required options
        'arguments' => ['test', ],  // required arguments
    ]);
} catch (\Exception $th) {
    die($th->getMessage());
}
```
Default value
```php
use \denisok94\helper\other\Console;
$console = new Console(null, true);
$console->getOptions(); // [o=>true,option=>true,...]
```
___

## Session

Класс по работе с сессиями
___

## S3DataService

Класс по работе с S3 хранилкой
___

## CloneObject

Класс по... хз зачем делал, но сохраню =)
___

# Framework Integration

## Yii2

Deletes in version 0.8.0 (12.06.2022)

A new separate repository has been created:
- class [MetaTag](https://github.com/Denisok94/yii-metatag)
- class [ConsoleController and StatusController](https://github.com/Denisok94/yii-helper)

## Symfony

[Symfony Helper](https://github.com/Denisok94/symfony-helper)