<?php

namespace denisok94\helper\traits;

/**
 * FileHelper trait
 */
trait FileHelper
{
	//----------------------------------
	// FileTypeIcons

	private static $type_list = [
		'image' => [
			'ico' => 'image',
			'ext' => ['png', 'jpg', 'jpeg', 'bmp', 'psd', 'icon', 'gif', 'ico', 'svg', 'webp'],
		],
		'video' => [
			'ico' => 'video',
			'ext' =>  ['mp4', 'avi', 'webm', 'mkv', '3gp', 'f4v', 'flv', 'moov', 'mov', 'mpeg', 'mpg'],
		],
		'audio' =>  [
			'ico' => 'audio',
			'ext' => ['mp3', 'mpa', 'weba', 'wav', 'wma', 'wave', 'ogg', 'm4a', 'mid', 'midi', 'aac'],
		],
		'word' => [
			'ico' => 'word',
			'ext' =>  ['doc', 'docm', 'docx', 'dot', 'dotm', 'dotx', 'odt', 'rtf', 'dotx'],
		],
		'excel' => [
			'ico' => 'excel',
			'ext' =>  ['xml', 'xls', 'xlsx', 'xlsm', 'xlt', 'csv'],
		],
		'powerpoint' => [
			'ico' => 'powerpoint',
			'ext' =>  ['ppa', 'ppt', 'pptx', 'pptm', 'xps', 'pot', 'potx', 'potm', 'pps', 'ppsx', 'ppsm'],
		],
		'archive' => [
			'ico' => 'archive',
			'ext' =>  ['zip', 'rar', '7z', 'gzip', 'tar'],
		],
		'js' => [
			'ico' => 'code',
			'ext' =>  ['js'],
		],
		'css' => [
			'ico' => 'code',
			'ext' =>  ['css'],
		],
		'php' => [
			'ico' => 'code',
			'ext' =>  ['php'],
		],
		'html' => [
			'ico' => 'code',
			'ext' =>  ['html', 'htm', 'mht'],
		],
		'python' => [
			'ico' => 'code',
			'ext' =>  ['py'],
		],
		'text' => [
			'ico' => 'text',
			'ext' =>  ['txt', 'log'],
		],
		'pdf' => [
			'ico' => 'pdf',
			'ext' =>  ['pdf'],
		],
	];

	/**
	 * поиск
	 * @param string $ext
	 * @return array|boolean
	 */
	private static function search($ext)
	{
		foreach (self::$type_list as $key => $value) {
			$index = array_search($ext, $value['ext']);
			if ($index !== FALSE) return [$key, $value['ico']];
		}
		return false;
	}

	/**
	 * Получить расширение файла
	 * 
	 * ```php
	 * $ext = H::ext('example.xml'); // xml
	 * $ext = H::ext('example.PDF'); // pdf
	 * ```
	 * 
	 * @param string $file файл,
	 * @return string расширение файла
	 * 
	 * @example:
	 * ```php
	 * $ext = H::ext('example.xml'); // xml
	 * echo "<div class='my-ico'><i class='$ext'></i></div>";
	 * ```
	 * 
	 * css
	 * ```css
	 * .my-ico>i:before {
	 * 	background-size: 30px 30px;
	 * 	height: 30px;
	 * 	width: 30px;
	 * }
	 * .xml:before {
	 * 	display: block;
	 * 	content: ' ';
	 * 	background-image: url('xml.svg');
	 * }
	 * ```
	 * 
	 */
	public static function ext(string $file)
	{
		$extension = pathinfo(basename($file), PATHINFO_EXTENSION);
		return strtolower($extension ?? ''); // нижний регистр (PNG → png)
	}

	/**
	 * to do
	 * @param string $file_path
	 *
	 * https://www.iana.org/assignments/media-types/media-types.xhtml
	 * https://www.php.net/manual/ru/function.mime-content-type.php
	 */
	public static function mimeType(string $file_path)
	{
	}

	/**
	 * Получить тип файла
	 * 
	 * ```php
	 * $type = H::type('example.swf'); // file
	 * $type = H::type('example.JPG'); // image
	 * $type = H::type('example.rar'); // archive
	 * $type = H::type('example.xml'); // excel
	 * ```
	 * 
	 * @param string $file файл,
	 * @return string тип файла
	 * 
	 * @example:
	 * ```php
	 * $type = H::type('example.xml'); // excel
	 * echo "<div class='office'><i class='$type'></i></div>";
	 * ```
	 * 
	 * css
	 * ```css
	 * .office>i:before {
	 * 	background-size: 30px 30px;
	 * 	height: 30px;
	 * 	width: 30px;
	 * }
	 * .excel:before {
	 * 	display: block;
	 * 	content: ' ';
	 * 	background-image: url('excel.svg');
	 * }
	 * ```
	 * 
	 */
	public static function fileType(string $file)
	{
		$ext = self::ext($file);  // расширение файла
		$type = 'file'; // базовая информация
		$search = $ext ? self::search($ext) : false;
		if ($search) $type = $search[0];
		return $type;
	}

	/**
	 * Получить название иконки для файла в формате Font Awesome 4/5
	 * @param string $file файл,
	 * @param bool $pro 
	 * @return string
	 * 
	 * @example:
	 * ```php
	 * $fa = H::fileIconFa('example.xml'); // file-excel-o
	 * $fa_5 = H::fileIconFa('example.xml', true); // file-excel
	 * $fa = H::fileIconFa('example.PDF'); // file-pdf-o
	 * $fa_5 = H::fileIconFa('example.PDF', true); // file-pdf
	 * $fa = H::fileIconFa('example.gif'); // file-image-o
	 * $fa_5 = H::fileIconFa('example.gif', true); // file-image
	 * $fa = H::fileIconFa('example.mp3'); // file-audio-o
	 * $fa_5 = H::fileIconFa('example.mp3', true); // file-audio
	 * 
	 * echo "<i class='fas fa fa-$fa'></i>"; // Font Awesome 4
	 * echo "<i class='fas fa-$fa_5'></i>"; // Font Awesome 5
	 * ```
	 */
	public static function fileIconFa(string $file, bool $pro = false)
	{
		$ext = self::ext($file); // расширение файла
		$fa = 'file'; // базовая иконка
		$search = $ext ? self::search($ext) : false;
		if ($search) $fa = $search[1];
		return (($fa != 'file') ? ('file-' . $fa) : ($fa)) . ((!$pro) ? ('-o') : (''));
	}

	/**
	 * Получить название иконки для файла
	 * @param string $file файл,
	 * @return string
	 * 
	 * @example:
	 * ```php
	 * $icon = H::fileIcon('example.xml'); // excel
	 * $icon = H::fileIcon('example.PDF'); // pdf
	 * $icon = H::fileIcon('example.gif'); // image
	 * $icon = H::fileIcon('example.mp3'); // audio
	 * echo "<div class='file-icon'><i class='$icon'></i></div>";
	 * ```
	 * 
	 * css
	 * ```css
	 * .file-icon>i:before {
	 * 	background-size: 30px 30px;
	 * 	height: 30px;
	 * 	width: 30px;
	 * }
	 * .excel:before {
	 * 	display: block;
	 * 	content: ' ';
	 * 	background-image: url('excel.svg');
	 * }
	 * .image:before {
	 * 	display: block;
	 * 	content: ' ';
	 * 	background-image: url('image.svg');
	 * }
	 * ```
	 */
	public static function fileIcon(string $file)
	{
		$ext = self::ext($file); // расширение файла
		$icon = 'file'; // базовая иконка
		$search = $ext ? self::search($ext) : false;
		if ($search) $icon = $search[1];
		return $icon;
	}

	//----------------------------------

	/**
	 * Функция, которая открывает файл, читает его и возвращает 
	 * @param string $file_path ,
	 * @return string
	 * 
	 * @example:
	 * ```php
	 * $file_path = __DIR__.'/files/file.txt';
	 * // Выводим информацию из файла
	 * try {
	 *   echo H::readFile($file_path);    
	 * } catch (Exception $e) {
	 *   echo $e->getMessage();
	 * }
	 * ```
	 */
	public static function fileRead(string $file_path)
	{
		$fname = basename($file_path); // берём имя файла (name.ext)
		if (!file_exists($file_path)) {
			throw new \Exception("Ошибка: файл $fname не существует!");
		}
		if (!filesize($file_path)) {
			throw new \Exception("Файл $fname пустой!");
		}
		// Открываем поток и получаем его дескриптор
		$f = fopen($file_path, "r");
		// В переменную $content запишем то, что прочитали из файла
		$content = fread($f, filesize($file_path));
		// Заменяем переносы строки в файле на тег BR. Заменить можно что угодно
		$content = str_replace(["\r\n", "\n"], "<br>", $content);
		// Закрываем поток
		fclose($f);
		// Возвращаем содержимое
		return $content;
	}

	/**
	 * @param string $file_path
	 * @param string $toFormat
	 * @return string
	 */
	public static function fileGetDt(string $file_path, string $toFormat = 'd.m.Y H:i:s')
	{
		if (file_exists($file_path)) {
			return (new \DateTime())->setTimestamp(filectime($file_path))->format($toFormat);
		} else {
			return null;
		}
	}

	/**
	 * короткий размер файла
	 * @param string $file_path
	 * @param array $si_prefix
	 * @return string
	 */
	public static function fileShortSize(string $file_path, array $si_prefix = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'])
	{
		if (file_exists($file_path)) {
			$value = filesize($file_path);
			return FileHelper::shortSize($value, $si_prefix);
		} else {
			return null;
		}
	}

	/**
	 * 2048 → 2.00 KB
	 * @param float|integer $size
	 * @param array $si_prefix
	 * @return string
	 */
	public static function shortSize($size, array $si_prefix = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'])
	{
		$base = 1024;
		$class = min((int)log($size, $base), count($si_prefix) - 1);
		$size = sprintf('%1.2f', $size / pow($base, $class)) . ' ' . $si_prefix[$class];
		return $size;
	}

	/**
	 * 2.00 KB → 2048
	 * @param string $size
	 * @return float|integer
	 * @link https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21Bytes.php/function/Bytes%3A%3AtoInt/8.2.x
	 */
	public static function parseSize(string $size)
	{
		// Remove the non-unit characters from the size.
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
		// Remove the non-numeric characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size);
		if ($unit) {
			// Find the position of the unit in the ordered string which is the power
			// of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		} else {
			return round($size);
		}
	}

	/**
	 * Получить размер папки
	 * @param string $path
	 * @return float|integer
	 */
	public static function dirSize(string $path)
	{
		$returnSize = 0;
		if (!$h = opendir($path)) return false;
		// В цикле при помощи функции readdir последовательно обрабатываем каждый элемент каталога.
		while (($element = readdir($h)) !== false) {
			//Исключаем директории "." и ".."
			if ($element != "." && $element != "..") {
				// Полный путь к обрабатываемому элементу(файл/папка)
				$all_path = $path . "/" . $element;
				// Если текущий элемент - файл, определяем его размер с помощью filesize() и суммируем его к переменой $returnSize
				if (filetype($all_path) == "file") {
					$returnSize += filesize($all_path);
					// Если текущий элемент - каталог, функция вызывает саму себя, результат суммируется к переменой $returnSize
				} elseif (filetype($all_path) == "dir") {
					$returnSize += self::dirSize($all_path);
				}
			}
		} // end while
		closedir($h);
		return $returnSize;
	}
}
