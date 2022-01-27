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
2. [MicroTimer](#MicroTimer)
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

Можно создать в любом удобном месте своего приложения файл `H.php` с классом `H` и унаследовать его от `Helper`.

```php
include_once '{path to repository}/src/Helper.php';
use \denisok94\helper\Helper;
class H extends Helper {}
```

## Использование

```php
use \denisok94\helper\Helper as H;
```

Можно создать в любом удобном месте своего приложения файл `H.php` с классом `H` и унаследовать его от `Helper`.

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
| implodeByKey |  |  |
| implodeByKeyWrap |  |  |
| toJson |  | Преобразовать массив в json |
| toArray |  | Преобразовать json в массив |
| arrayOrderBy |  | Сортировка массива |

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
| random |  | Сгенерировать рандомную строка |
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
- [listn][/listn] - ol
- [\*][\*] - li
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

# MicroTimer

Узнать, сколько времени выполняется код

```php
use \denisok94\helper\MicroTimer;
$queryTimer = new MicroTimer(); // start
// code ...
$queryTimer->stop();

// result:
$time = $queryTimer->elapsed(); 
// or/and
printf($queryTimer);
```
> взято у [phpliteadmin](https://bitbucket.org/phpliteadmin/public/src/master/classes/MicroTimer.php)
___

# Framework Integration

## Yii2

```php
use \denisok94\helper\YiiHelper;
```

`YiiHelper` наследует все от `Helper`.

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| exec |  | Выполнить консольную команду |
| log |  | Записать данные в лог файл. Файлы хранятся в `runtime/logs/` |
| setCache |  | Запомнить массив в кэш |
| getCache |  | Взять массив из кэша |
| deleteCache |  | Удалить кэш |
| ~~clearCache~~ | | |

Можно создать в папке `components` файл `H.php` с классом `H` и унаследовать его от `YiiHelper`.

Внутри класса `H` добавить свои функции с повторяющемся действиями или перезаписать имеющиеся в `YiiHelper`.

```php
namespace app\components;
use \denisok94\helper\YiiHelper;
class H extends YiiHelper {}
```

И в дальнейшем использовать его.

```php
use app\components\H;
```

setCache/getCache

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

Указываются в `action` контроллере, перед `render()`.
```php
namespace app\controllers;
use \denisok94\helper\yii2\MetaTag;

class NewsController extends Controller
{
    // ...
    public function actionView($id)
    {
        $model = $this->findModel($id);
        MetaTag::tag($this->view, [
            'title' => $model->title,
            'description' => substr($model->text, 0, 100),
            'keywords' => $model->tags, // string
            'image' => $model->image->url,
        ]);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    // ...
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $this->view->title = $model->title;
        list($width, $height, $type, $attr) = getimagesize(Yii::$app->getBasePath() . "/web/{$model->image->path}");
        MetaTag::tag($this->view, [
            'description' => $model->announce,
            'keywords' => implode(', ', $model->tags), // if tags array
            'image' => \yii\helpers\Url::to($model->image->path, true),
            'image:src' => \yii\helpers\Url::to($model->image->path, true),
            'image:width' => $width,
            'image:height' => $height,
        ]);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
```
___

### **StatusController**

Для общения по формату json. (REST API)

```php
namespace app\controllers;
use \denisok94\helper\yii2\StatusController;

class MyController extends StatusController
{
    // code
}
```

```php
// получить все сообщение полностью
$message = $this->post;
// получить параметр из сообщения
$phone = $this->getPost('phone');
```

Сообщить об успехе

```php
// Сообщить об успешной обработки
return $this->success(); // ['status' => 'OK', 'data' => []];
// Вернуть результат работы
return $this->success($data); // ['status' => 'OK', 'data' => $data];
```

Сообщить об ошибке

```php
\Yii::$app->response->statusCode = 400; // or status http code
return $this->error($error, $text, $data); // ['status' => 'FAIL', ...]
```

Собственный формат ответа 
```php
// custom responses
$responses = [];
// code
return $this->send($responses);
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
H::exec('сontroller/action', [params]);
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