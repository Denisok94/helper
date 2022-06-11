<h1 align = "center"> Helper Class </h1>

Класс с набором полезных функций, по мнению автора.
Не претендует на идеальность и единственное верное решение.

A class with a set of useful functions, according to the author.
It does not pretend to be ideal and the only correct solution.

![https://img.shields.io/badge/license-BSD-green](https://img.shields.io/badge/license-BSD-green)
___

0. [Установка](#Установка)
1. [Использование](#Использование)
    1. [ArrayHelper](#ArrayHelper)
    2. [DataHelper](#DataHelper)
    3. [StringHelper](#StringHelper)
    4. [FileHelper](#FileHelper)
    5. [HtmlHelper](#HtmlHelper)
    6. [OtherHelper](#OtherHelper)
2. [Other Class](#Other-Class)
    1. [MicroTimer](#MicroTimer)
    2. [Console](#Console)
3. [Framework Integration](#Framework-Integration)
    1. [Yii2](#Yii2)

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

## Console

| Method | Description |
|----------------|:----------------|
| get | Получить аргумент/опцию |
| getArgument | Получить аргумент |
| getArguments | Получить аргументы |
| getOption | Получить опцию |
| getOptions |  Получить опции |
| hasArgument | |
| hasOption | |
| show |  |

```php
use \denisok94\helper\other\Console;
// php console.php arg1 arg2=val -o -a5 --option --option1=6 --option1=3
$console = new Console();
$console->getArguments(); // [arg1,arg2=>val]
$console->getArgument(0); // arg1
$console->getArgument('arg2'); // val
$console->getOptions(); // [o=>null,a=>5,option=>null,option1=>[6,3]]
$console2 = new Console([], true);
$console2->getOptions(); // [o=>true,option=>true,...]
```

ToDo:

- [ ] required параметры
___

# Framework Integration

## Yii2

Deletes in v0.7.8 (12.06.2022)

New separate repository:
- [MetaTag](https://github.com/Denisok94/yii-metatag)
- [ConsoleController and StatusController](https://github.com/Denisok94/yii-helper)