<?php

namespace denisok94\helper\traits;

/**
 * FileHelper trait
 */
trait FileHelper
{
	/**
	 * Функция, которая открывает файл, читает его и возвращает 
	 */
	public static function readFile($file_path)
	{
		if (!file_exists($file_path))
			throw new \Exception("Ошибка: файл $file_path не существует!");
		if (!filesize($file_path))
			throw new \Exception("Файл $file_path пустой!");
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


	public static function getFileDt($file_path, $toFormat = 'd.m.Y H:i:s')
	{
		if (file_exists($file_path)) {
			return (new \DateTime())->setTimestamp(filectime($file_path))->format($toFormat);
		} else {
			return null;
		}
	}

	public static function shortSize($file_path, $si_prefix = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'])
	{
		if (file_exists($file_path)) {
			$value = filesize($file_path);
			$base = 1024;
            $class = min((int)log($value, $base), count($si_prefix) - 1);
            $value = sprintf('%1.2f', $value / pow($base, $class)) . ' ' . $si_prefix[$class];
			return $value;
		} else {
			return null;
		}
	}

	public static function dirSize($path)
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
