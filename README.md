<h1 align = "center"> Helper Class </h1>

Класс с набором полезных функций, по мнению автора.
Не претендует на идеальность и единственное верное решение.

A class with a set of useful functions, according to the author.
It does not pretend to be ideal and the only correct solution.

![https://img.shields.io/badge/license-BSD-green](https://img.shields.io/badge/license-BSD-green)
___

0. [Установка](#Установка)
    1. [include](#include)
1. [Использование](#Использование)
    1. [ArrayHelper](#ArrayHelper)
    2. [DataHelper](#DataHelper)
    3. [StringHelper](#StringHelper)
    4. [FileHelper](#FileHelper)
    5. [HtmlHelper](#HtmlHelper)
    6. [OtherHelper](#OtherHelper)
2. [Other](#Other)
    1. [MicroTimer](#MicroTimer)
    2. [Console](#Console)
3. [Framework Integration](#Framework-Integration)
    1. [Yii2](#Yii2)
        - [MetaTag](#MetaTag)
        - [StatusController](#StatusController)
        - [ConsoleController](#ConsoleController)

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

## include

Если вы скачали репозиторий архивом (zip/tar).

```php
include_once '{path to repository}/src/Helper.php';
use \denisok94\helper\Helper;
```

## Использование

```php
use \denisok94\helper\Helper as H;
```
---
Можно создать в любом удобном месте своего приложения, например в папке `components`, файл `H.php` с классом `H` и унаследовать его от `Helper`.

Внутри класса `H` добавить свои функции с повторяющемся действиями или перезаписать имеющиеся в `Helper`.

```php
namespace app\components;
use \denisok94\helper\Helper;
class H extends Helper {}
```

И в дальнейшем использовать его.

```php
use app\components\H;
```
___

# ArrayHelper

Работа с массивами

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| get |  | Найти в массиве по пути |
| set |  | Добавить/заменить элемент в массиве |
| parse |  | Заменить шаблон |
| implodeWrap |  | Объединяет элементы массива в строку + обернуть текст в кавычки |
| implodeWith |  | Объединяет элементы массива в строку, с предпользовательской обработкой |
| implodeByKey |  | Объединяет элементы массива в строку по ключу  |
| implodeByKeyWrap |  | Объединяет элементы массива в строку по ключу  + обернуть текст в кавычки |
| implodeMulti |  | Объединяет элементы многомерного массива в строку |
| isJson |  | проверяет данные на json |
| toJson |  | Преобразовать массив/объект в json |
| toArray |  | Преобразовать json в массив |
| arrayToObject |  | Преобразовать массив в объект |
| array2Object |  | Преобразовать массив в объект, вариант 2 |
| objectToArray |  | Преобразовать объект в массив |
| object2Array |  | Преобразовать объект в массив, вариант 2 |
| arrayOrderBy |  | Сортировка массива |

> arrayToObject и objectToArray - работают быстрее, но могут возникнуть исключения. array2Object и object2Array - использую преобразование через json_decode + json_encode, это более ресурсозатратные, но надёжнее.
___

# DataHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| currentDate |  | Сегодняшняя дата |
| currentDt |  | Сегодняшняя дата и время |
| toMysqlDate |  | Преобразовать дату в формат Mysql |
| toMysqlDt |  | Преобразовать дату и время в формат Mysql |
| toRuDate |  | Русский формат даты |
| toRuDt |  | Русский формат даты и времени |
| stampToDt |  | Преобразовать timestamp в формат даты и времени |
| stampToDtU |  | Преобразовать timestamp в формат даты и времени с миллисекундами |

___

# StringHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| uuid |  | Сгенерировать uuid v4 |
| guid |  | Сгенерировать guid v4 |
| random |  | Сгенерировать рандомную строку |
| spell |  | падежи к числительным |
| slug |  | преобразовать строку в человекопонятный url |
| ru2Lat |  | Транслитирование, ГОСТ 7.79-2000, схема А |
| ruToLat |  | Транслитирование, ГОСТ 7.79-2000, схема Б |
| ru2Slug |  | преобразовать строку, на русском (схема А), в человекопонятный url |
| getClassName |  | Получить имя класса |
| slashes |  | экранирование |
| replaceBBCode |  | Парсинг BB-кодов |

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

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| ext |  | Получить расширение файла |
| fileRead |  | Показать содержимое файла |
| fileGetDt |  | Получить дату последнего изменения |
| fileType |  | Получить тип файла |
| fileIcon |  | Получить название иконки для файла |
| fileIconFa |  | Получить название иконки для файла в формате Font Awesome 4/5 |
| fileShortSize |  | короткий размер файла |
| shortSize |  | 2048 → 2.00 KB |
| parseSize |  | 2.00 KB → 2048 |
| dirSize |  | Получить размер папки |

___

# HtmlHelper

Генерация html тегов
> в разработке... 

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| video |  | видео тег |

___

# OtherHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| curl |  | curl для большинства простых запросов |
| getRequest |  | параметры запроса |
| getUserIp |  | получить IP пользователя |
| isBot |  | Проверка пользователя на бота |
| msleep |  | уснуть на N секунд |

> isBot() не даёт 100% гарантии.
Кому разрешить/запретить доступ/функционал - решать исключительно Вам.
Запрещая всё и всем, Вы можете лишится продвижения сайта в поисковых ресурсах и/или красивых привью в соц сетях =). 
___

# Other

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

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| get |  | Получить аргумент/опцию |
| getArgument |  | Получить аргумент |
| getArguments |  | Получить аргументы |
| getOption |  | Получить опцию |
| getOptions |  |  Получить опции |
| hasArgument |  | |
| hasOption |  | |
| show |  |  |

```php
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

```php
use \denisok94\helper\yii2\Helper;
```

`yii2\Helper` наследует все от `Helper`.

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| exec |  | Выполнить консольную команду |
| log |  | Записать данные в лог файл. Файлы хранятся в `runtime/logs/` |
| setCache |  | Запомнить массив в кэш |
| getCache |  | Взять массив из кэша |
| deleteCache |  | Удалить кэш |
| ~~clearCache~~ | | |

> setCache/getCache.
В кэш можно сохранить результат запроса из бд, который часто запрашивается, например для фильтра.
К тому же, этот фильтр, может быть, использоваться несколько раз на странице или сама страница с ним, может, многократно обновляться/перезагружаться.

```php
namespace app\components;
use app\components\H;

class Filter
{
    //.....
    /**
     * @return array
     */
    public static function getTypes()
    {
        $types = H::getCache('types'); // dir: app/cache/types.json
        if ($types) {
            return $types;
        } else {
            $types = \app\models\Types::find()
                ->select(['id', 'name'])->all();
            $array = [];
            foreach ($types as $key => $value) {
                $array[$value->id] = ucfirst($value->name);
            }
            H::setCache('types', $array);
            return $array;
        }
    }
}
```
___

### **MetaTag**

Генерация мета тегов.

```php
use \denisok94\helper\yii2\MetaTag;
```
> Пока, реализованы простые теги, позже, доработаю остальные.

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| tag |  | Установить MetaTag на страницу |

В в настройках(`config`), где находятся файлы `web.php` или `config.php` укажите название сайта и основной язык
```php
$config = [
    'name' => 'Site Name',
    'language' => 'en-EN', // ru-RU 
    'basePath' => dirname(__DIR__),
    //...
```

Указываются в `action` контроллере, перед `render()`.
```php
$meta = new MetaTag($this->view);
```
Установить изображение
```php
// Before 0.7.5
list($width, $height, $type, $attr) = getimagesize(Yii::$app->getBasePath() . "/web/image.jpg");
$meta = (new MetaTag($this->view))->tag([
    'image' => Url::to('image.jpg', true),
    'image:src' => Url::to('image.jpg', true),
    'image:width' => $width,
    'image:height' => $height,
]);
// Since 0.7.5 
$meta = new MetaTag($this->view, "/image.jpg");
```
Индивидуальная иконка для страницы
```php
// Before
$this->view->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to("/favicon.png", true)]);
// Since MetaTag
$meta = new MetaTag($this->view, null, "/favicon.png");
```

```php
namespace app\controllers;
use \denisok94\helper\yii2\MetaTag;

class NewsController extends Controller
{
    // ...
    public function actionView($id)
    {
        $model = $this->findModel($id);
        //
        (new MetaTag($this->view))->tag([
            'title' => $model->title,
            'description' => substr($model->text, 0, 100),
            'keywords' => $model->tags, // string
        ]);
        // or
        $this->view->title = $model->title;
        $meta = new MetaTag($this->view, $model->image->url);
        $meta->tag([
            'description' => $model->announce,
            'keywords' => implode(', ', $model->tags), // if tags array
        ]);
        //
        return $this->render('view', ['model' => $model]);
    }
}
```
___

### **StatusController**

Для общения по формату json.

| Имя | Параметры | default code | Описание |
|----------------|:---------:|:---------:|:----------------|
| send | $data | 200 | Собственный формат ответа |
| buildResponse | $data, $message, $status, $code | 200 | Собственный формат ответа |
| buildSuccess | $data | 200 | Сообщить об успехе |
| buildError | $message, $status, $code | 400 | Сообщить об ошибке |
| buildBadRequest | $message | 400 |  |
| buildUnauthorized | $message | 401 |  |
| buildForbidden | $message | 403 |  |
| buildNotFound | $message | 404 |  |
| buildInternalServerError | $message | 500 |  |

```php
namespace app\controllers;
use \denisok94\helper\yii2\StatusController;

class MyController extends StatusController
{
    // code
}
```

```php
// получить все данные
$message = $this->post; // array
// получить параметр из данных
$phone = $this->getPost('phone'); // phone or null
```

Сообщить об успехе

```php
// Сообщить об успешной обработки
return $this->buildSuccess(); // http status code 200
// ['code' => '200', 'status' => 'OK', 'data' => []];
// Вернуть результат работы
return $this->buildSuccess($data);  // http status code 200
// ['code' => '200', 'status' => 'OK', 'data' => $data];
```

Сообщить об ошибке

```php
return $this->buildError(); // http status code 400
// ['code' => '400', 'status' => 'FAIL', 'message' => 'Error', 'data' => []]
return $this->buildError($message); // http status code 400
// ['code' => '400', 'status' => 'FAIL', 'message' => $message, 'data' => []]
return $this->buildError($message, $data); // http status code 400
// ['code' => '400', 'status' => 'FAIL', 'message' => $message, 'data' => $data]
return $this->buildError($message, $data, 401); // http status code 401
// ['code' => '401', ...]

// return ['code', 'status', 'message'];
return $this->buildBadRequest(); // http status code 400
return $this->buildUnauthorized(); // http status code 401
return $this->buildForbidden(); // http status code 403
return $this->buildNotFound(); // http status code 404
return $this->buildInternalServerError(); // http status code 500

if (!$this->post) {
    return $this->buildBadRequest("Request is null"); // http status code 400
    // ['code' => '400', 'status' => 'FAIL', 'message' => 'Request is null']
}

try {
    //code...
} catch (\Exception $e) {
    return $this->buildInternalServerError($e->getMessage()); // http status code 500
    // ['code' => '500', 'status' => 'FAIL', 'message' => '...']
}
```

Собственный формат ответа 
```php
// custom responses
return $this->send([...]); // http status code 200
// [...];
return $this->send(['code' => 204]); // http status code 204
// ['code' => '204'];
return $this->send(['code' => 201, 'data' => $data]); // http status code 201
// ['code' => '201', 'data' => $data];

return $this->buildResponse($data); // http status code 200
// ['code' => '200', 'status' => 'OK', 'message' => '', 'data' => $data]
return $this->buildResponse($data, $message); // http status code 200
// ['code' => '200', 'status' => 'OK', 'message' => $message, 'data' => $data]
return $this->buildResponse($data, $message, $status, 999); // http status code 999
// ['code' => '999', 'status' => $status, 'message' => $message, 'data' => $data]
```
___

### **ConsoleController**

```php
namespace app\commands;
use \denisok94\helper\yii2\ConsoleController;

class MyController extends ConsoleController
{
    // code
}
```

Вызвать `action` консольного контроллера:
```php
H::exec('controller/action', [params]);
```
> Консольный контроллер, не подразумевает ответ. 
Вся выводящая информация (echo, print и тд) будет записана в лог файл. 
При вызове через `H::exec()`, по умолчанию логи находятся в `/runtime/logs/consoleOut.XXX.log` (можно переопределить)

Получить переданные параметры
```php
$init = $this->params;
```

Пример:
```php
class MyController extends ConsoleController
{
	public function actionTest()
	{
		$init = $this->params;
		$test = $this->params['test'];
	}
}

H::exec('my/test', ['test' => 'test']);
```