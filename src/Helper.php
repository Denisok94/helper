<?php

namespace denisok94\helper;

use denisok94\helper\traits\ArrayHelper;
use denisok94\helper\traits\DataHelper;
use denisok94\helper\traits\HtmlHelper;
use denisok94\helper\traits\OtherHelper;
use denisok94\helper\traits\StringHelper;

/**
 * Helper
 * @author Denisok94
 * @link https://s-denis.ru/git/helper
 * @version 0.2
 */
class Helper
{
    use ArrayHelper, DataHelper, HtmlHelper, OtherHelper, StringHelper;
}

//--------------------------------------------------------

use denisok94\helper\traits\yii2\MetaTag;
use denisok94\helper\traits\yii2\Other;

/**
 * YiiHelper
 * @author Denisok94
 * @link https://s-denis.ru/git/helper
 * @version 0.2
 */
class YiiHelper extends Helper
{
    use MetaTag, Other;
}