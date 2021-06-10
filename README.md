<h1 align = "center"> Helper Class </h1>

Класс с набором полезных функций, по мнению автора.

Не претендует на идеальность и единственное верное решение.

Доки чуть позже)

![https://img.shields.io/badge/license-BSD-green](https://img.shields.io/badge/license-BSD-green)
___

0. [Установка](#Установка)
    1. [Использование](#Использование)
    2. [include](#include)
1. [ArrayHelper](#ArrayHelper)
2. [DataHelper](#DataHelper)
3. [StringHelper](#StringHelper)
4. [FileHelper](#FileHelper)
5. [HtmlHelper](#HtmlHelper)
6. [OtherHelper](#OtherHelper)
7. [Framework Integration](#Framework-Integration)
    1. [Yii2](#Yii2)
        - [MetaTag](#MetaTag)
        - [StatusController](#StatusController)
        - [ConsoleController](#ConsoleController)

___

# Установка

Run:

```bash
php composer require --prefer-dist denisok94/helper
```

or add to the `require` section of your `composer.json` file:

```json
"denisok94/helper": "*"
```

```bash
php composer update
```

## Использование

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

## include

Если вы скачали репозиторий архивом (zip/tar).

Можно создать в любом удобном месте своего приложения файл `H.php` с классом `H` и унаследовать его от `Helper`.

```php
include_once '{path to repository}/src/Helper.php';
use \denisok94\helper\Helper;
class H extends Helper {}
```

___

# ArrayHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| get |  | Найти в массиве |
| set |  | Добавить/заменить в массиве |
| parse |  | Заменить шаблон |
| implodeWrap |  |  Объединяет элементы массива в строку, обернуть текст в кавычки |
| implodeWith |  |  Объединяет элементы массива в строку, с пред обработкой |
| toJson |  |  |
| toArray |  |  |
| arrayOrderBy |  | Сортировка массива |

___

# DataHelper

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

# StringHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| uuid |  | Сгенерировать uuid v4 |
| random |  | Рандомная строка |
| spell |  | падежи к числительным |

___

# FileHelper

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

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| video |  | Сгенерировать видео тег |

___

# OtherHelper

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| curl |  | curl для большинства простых запросов |
| getUserIp |  | получить IP пользователя |
| isBot |  | Проверка пользователя на бота |
| msleep |  | уснуть на N секунд |

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
| setCache |  |  |
| getCache |  |  |

Можно создать в папке `componets` файл `H.php` с классом `H` и унаследовать его от `YiiHelper`.

Внутри класса `H` добавить свои функции с повторяющемся действиями или перезаписать имеющиеся в `YiiHelper`.

```php
namespace app\componets;
use \denisok94\helper\YiiHelper;
class H extends YiiHelper {}
```

И в дальнейшем использовать его.

```php
use app\componets\H;
```

___

### **MetaTag**

```php
use \denisok94\helper\yii2\MetaTag;
```

| Имя | Параметры | Описание |
|----------------|:---------:|:----------------|
| tag |  | Установить MetaTag на страницу |


```php
namespace app\controllers;
use app\componets\MetaTag;

class SiteController extends Controller
{
    // ...
    public function actionAbout()
    {
        MetaTag::tag($this->view, [
            'title' => "About My site",
            'description' => "About my site",
            'url' => "https://s-denis.ru/site/about",
        ]);
        return $this->render('about');
    }
    // ...
}
```
___

### **StatusController**

```php
namespace app\controllers;
use \denisok94\helper\yii2\StatusController;

class MyController extends StatusController
{
    // code
}
```

```php
// получить все сообщения
$message = $this->post;
// получить значение из сообщения
$phone = $this->getPost('phone');
```

Сообщить об успехе

```php
// Сообщить об успешной обработки
return $this->success(); // ['status' => 'OK', 'data' => []];
// Вернуть результат
return $this->success($data); // ['status' => 'OK', 'data' => $data];
```

Сообщить об ошибке

```php
\Yii::$app->response->statusCode = 400; // or status http code
return $this->error($error, $text, $data);
```


Свой ответ 
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
Получить переданные параметры
```php
$init = $this->params;
```
Вызвать `action` консольного контроллера:
```php
H::exec('сontroller/action', [params]);
```
Консольный контроллер, не подразумевает ответ. Вся выводящая информация (echo, print и тд) будет записана в лог файл.


Пример:
```php
class MyController extends ConsoleController
{
	public function actionTest()
	{
		$init = $this->params;
	}
}

H::exec('my/test', ['test' => 'test']);
```