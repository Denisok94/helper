<?php

namespace denisok94\helper;

include_once __DIR__ . '/traits/ArrayHelper.php';
include_once __DIR__ . '/traits/DataHelper.php';
include_once __DIR__ . '/traits/FileHelper.php';
include_once __DIR__ . '/traits/HtmlHelper.php';
include_once __DIR__ . '/traits/OtherHelper.php';
include_once __DIR__ . '/traits/StringHelper.php';

use denisok94\helper\traits\ArrayHelper;
use denisok94\helper\traits\DataHelper;
use denisok94\helper\traits\FileHelper;
use denisok94\helper\traits\HtmlHelper;
use denisok94\helper\traits\OtherHelper;
use denisok94\helper\traits\StringHelper;

/**
 * Helper
 * @author Denisok94
 * @version 0.8.1
 * 
 * ```php
 * use \denisok94\helper\Helper as H;
 * H::methodName($arg);
 * ```
 */
class Helper
{
    use ArrayHelper, DataHelper, FileHelper, HtmlHelper, OtherHelper, StringHelper;
}