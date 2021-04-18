<?php

namespace denisok94\helper\traits;

/**
 * HtmlHelper trait
 * @author Denisok94
 * @link https://s-denis.ru/git/helper
 * @version 0.1
 */
trait HtmlHelper
{
    /**
     * HTML тэг видео
     * @param string $src url video,
     * @param array|string $attribution Атрибуты,
     * @return string
     * @link http://htmlbook.ru/html/video
     */
    public static function video($src, $attribution = null)
    {
        $video = ' ';
        // ----------------------------------
        // базавые параметры
        $default = [
            'width' => '500px',
            'height' => "300px",
            'controls' => "controls",
            'preload' => "metadata",
        ];
        // ----------------------------------
        //  информация о файле
        $fname = basename($src);
        $filetupe = pathinfo($fname);
        $ftupe = strtolower($filetupe['extension']); //расширение файла в нижнем регистре
        // указываем тип файла и кодек, если известен
        $a_type = [
            'mp4' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
            'webm' => 'video/webm; codecs="vp8, vorbis"',
            'ogv' => 'video/ogg; codecs="theora, vorbis"'
        ];
        $type = isset($a_type[$ftupe]) ? 'type="' . $a_type[$ftupe] . '"' : '';

        // если заданы параметры
        if ($attribution) {
            // это массив
            if (is_array($attribution)) {
                // совмещаем с базавыми
                $default = array_merge($default, $attribution);
                // генерем строку
                foreach ($default as $Key => $Value) {
                    $video .= $Key . '="' . $Value . '" ';
                }
            } else {
                // генерем строку
                $video .= "$attribution ";
            }
        } else {
            // генерем строку из базавых параметров
            foreach ($default as $Key => $Value) {
                $video .= $Key . '="' . $Value . '" ';
            }
        }
        // 
        $source = '<source src="' . $src . '"' . $type . ' >';

        return '<video ' . $video . '>' . $source . ' Тег video не поддерживается вашим браузером. <a href="' . $src . '">Скачайте видео</a></video>';
    }
}