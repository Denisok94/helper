<?php

namespace denisok94\helper\yii2;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * MetaTag class
 * @author Denisok94
 * @link https://developers.facebook.com/tools/debug/
 */
class MetaTag
{
    private $view,
        $defaultTag,
        $title,
        $language,
        $name,
        $domain,
        $init = false;
    private $twitterTag = [
        'title', 'description', 'url', 'domain', 'site', 'image', 'image:src', 'creator', 'card'
    ];
    private $ogTag = [
        'title', 'description', 'url', 'locale', 'image', 'image:src', 'image:type', 'image:width', 'image:height', 'site_name', 'locale', 'type',
    ];

    /**
     *  @param mixed $view $this->view
     */
    function __construct($view)
    {
        $this->init($view);
    }

    private function init($view)
    {
        $this->view = $view;

        $this->title = isset($this->view->title) ? Html::encode($this->view->title) : Yii::$app->name;
        $this->name = Yii::$app->name;
        $this->language = isset(Yii::$app->language) ? Yii::$app->language : 'en-EN';

        if (!isset(Yii::$app->domain)) {
            $urlData = parse_url(Url::home(true));
            $this->domain = $urlData['host'];
        } else {
            $this->domain = Yii::$app->domain;
        }
        $this->init = true;

        // list($width, $height, $type, $attr) = getimagesize(Yii::$app->getBasePath() . "/web/30.jpg");

        $this->defaultTag = [
            'title' => $this->title,
            // 'description' => "",
            // 'keywords' => "",
            'locale' => $this->language,
            'url' => Url::to([], true), // Url::base(true) ,
            'domain' => $this->domain, // 
            'site' => "@" . ucwords($this->name),
            // 'image' => Url::to('30.jpg', true),
            // 'image:src' => Url::to('30.jpg', true),
            // 'image:width' => $width,
            // 'image:height' => $height,
            // 'creator' => '@Denisok1494', // автор статьи
            'site_name' =>  ucwords($this->name), // 
            'card' => 'summary_large_image', // summary
            'type' => 'website', //website, profile
        ];
    }
    /**
     * 
     */
    private function multineedle_stripos($haystack, $needles, $offset = 0, $flags = false)
    {
        if (is_array($needles)) {
            foreach ($needles as $needle) {
                // $found[$needle] = stripos($haystack, $needle, $offset);
                if (stripos($haystack, $needle, $offset) !== false) {
                    return $flags ? $needle : true;
                }
            }
        } else {
            if (stripos($haystack, $needles, $offset) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 
     */
    private function setTeg($name, $content)
    {
        $this->view->registerMetaTag(
            ['property' => $name, 'content' => $content]
        );
    }

    /**
     * @param array $tags [name => content]
     * name: title, description, keywords, author/creator, image(image:src, image:width, image:height), card: summary/summary_large_image, type: website/profile
     */
    function tags($tags = [])
    {
        $newTags = array_merge($this->defaultTag, $tags);

        $this->setTeg('description', $newTags['description']);
        $this->setTeg('keywords', $newTags['keywords']);
        unset($newTags['keywords']);

        foreach ($newTags as $key => $value) {
            $del = false;
            if ($this->multineedle_stripos($key, $this->twitterTag) !== false) {
                $del = true;
                $this->setTeg("twitter:$key", $value);
            }
            if ($this->multineedle_stripos($key, $this->ogTag) !== false) {
                $del = true;
                $this->setTeg("og:$key", $value);
            }
            if ($del == false) {
                $this->setTeg("$key", $value);
            };
        }
    }

    /**
     * @param mixed $view $this->view
     * @param array $tags ['name' => 'content', 'name' => 'content', ...]
     * 
     * names: 
     * - title - default: `$this->view->title` or `Yii::$app->name`
     * - description
     * - keywords
     * - author/creator
     * - image, image:src, image:width, image:height
     * 
     * ```php
     * list($width, $height, $type, $attr) = getimagesize(Yii::$app->getBasePath() . "/web/image.jpg");
     * 'image' => Url::to('image.jpg', true),
     * 'image:src' => Url::to('image.jpg', true),
     * 'image:width' => $width,
     * 'image:height' => $height,
     * ```
     * - card - summary or summary_large_image - default: `summary_large_image`
     * - url - default: `Url::to([], true)`
     * - locale - default: `Yii::$app->language` or `'en-EN'`
     * - site - default: `Yii::$app->name`
     * - domain - default: `Yii::$app->domain` or `Url::home(true)`
     * - type - website or profile - default: `website`
     * @example 1:
     * in web.php or config.php
     * ```php
     *  $config = [
     *      'name' => 'Site Name',
     *      'language' => 'Site Base Language',
     * ```
     * 
     * @example 2:
     * ```php
     * use \denisok94\helper\yii2\MetaTag;
     * class NewsController extends Controller {
     * public function actionView($id) {
     *    $model = $this->findModel($id);
     *    MetaTag::tag($this->view, [
     *        'title' => $model->title,
     *        'description' => substr($model->text, 0, 100),
     *        'keywords' => $model->tagsToString,
     *        'image' => $model->image->url,
     *    ]);
     *    return $this->render('view', ['model' => $model]);
     * }}
     * ```
     */
    static function tag($view, $tags = [])
    {
        $new = new MetaTag($view);
        $new->tags($tags);
    }
}
