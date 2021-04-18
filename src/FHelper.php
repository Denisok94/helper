<?php

include_once __DIR__ . '/traits/ArrayHelper.php';
include_once __DIR__ . '/traits/DataHelper.php';
include_once __DIR__ . '/traits/HtmlHelper.php';
include_once __DIR__ . '/traits/OtherHelper.php';
include_once __DIR__ . '/traits/StringHelper.php';

use denisok94\helper\traits\ArrayHelper;
use denisok94\helper\traits\DataHelper;
use denisok94\helper\traits\HtmlHelper;
use denisok94\helper\traits\OtherHelper;
use denisok94\helper\traits\StringHelper;

/**
 * Helper
 * @author Denisok94
 * @link https://s-denis.ru/git/helper
 * @version 0.3
 */
class FHelper
{
    use ArrayHelper, DataHelper, HtmlHelper, OtherHelper, StringHelper;
}