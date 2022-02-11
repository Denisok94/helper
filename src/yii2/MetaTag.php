<?php

namespace denisok94\helper\yii2;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * MetaTag class
 * 
 * Установить MetaTag на страницу
 * 
 * @author Denisok94
 * @link https://developers.facebook.com/tools/debug/
 *  
 * @example Пример:
 * ```php
 * namespace app\controllers;
 * use \denisok94\helper\yii2\MetaTag;
 * class NewsController extends Controller
 * {
 *      public function actionView($id)
 *      {
 *          $model = $this->findModel($id);
 *          $meta = new MetaTag($this->view, $model->image->url);
 *          $meta->tag([
 *              'title' => $model->title,
 *              'description' => substr($model->text, 0, 100),
 *              'keywords' => $model->tags, // string
 *          ]);
 *          return $this->render('view', [
 *              'model' => $model,
 *          ]);
 *      }
 * }
 * ```
 */
class MetaTag
{
    private $view,
        $defaultTag,
        $title,
        $language,
        $name,
        $domain,
        $image,
        $favicon,
        $init = false;
    private $twitterTag = [
        'title', 'description', 'url', 'domain', 'site', 'image', 'image:src', 'creator', 'card'
    ];
    private $ogTag = [
        'title', 'description', 'url', 'locale', 'type', 'image', 'image:src', 'image:type', 'image:width', 'image:height', 'site_name',
    ];

    /**
     * @param mixed $view $this->view
     * @param string $image ~"/30.jpg",
     * @param string $favicon ~"/favicon.png",
     */
    public function __construct($view, $image = null, $favicon = null)
    {
        $this->image = $image;
        $this->favicon = $favicon;
        $this->view = $view;
        $this->init();
        return $this;
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
     * - card - summary or summary_large_image - default: `summary_large_image`
     * - url - default: `Url::to([], true)`
     * - locale - default: `Yii::$app->language` or `'en-EN'`
     * - site - default: `Yii::$app->name`
     * - domain - default: `Yii::$app->domain` or `Url::home(true)`
     * - type - website or profile - default: `website`.
     * @example 1:
     * ```php
     * class NewsController extends Controller {
     * public function actionView($id) {
     *    $model = $this->findModel($id);
     *    $meta = new MetaTag($this->view, $model->image->url);
     *    $meta->tag([
     *        'title' => $model->title,
     *        'description' => substr($model->text, 0, 100),
     *        'keywords' => $model->tagsToString,
     *    ]);
     *    return $this->render('view', ['model' => $model]);
     * }}
     * ```
     */
    public function tag($tags = [])
    {
        $this->tags($tags);
    }

    //-----------------------------------------------

    private function init()
    {
        $this->title = isset($this->view->title) ? Html::encode($this->view->title) : Yii::$app->name;
        $this->name = Yii::$app->name;
        $this->language = Yii::$app->language ?? 'en-EN';

        if (!isset(Yii::$app->domain)) {
            $urlData = parse_url(Url::home(true));
            $this->domain = $urlData['host'];
        } else {
            $this->domain = Yii::$app->domain;
        }
        $this->init = true;

        $this->defaultTag = [
            'title' => $this->title,
            // 'description' => "",
            // 'keywords' => "",
            'locale' => $this->language,
            'url' => Url::to([], true), // Url::base(true) ,
            'domain' => $this->domain, // 
            'site' => "@" . str_replace(' ', '_', ucwords($this->name)),
            // 'creator' => '@Denisok1494', // автор статьи
            'site_name' =>  ucwords($this->name), // 
            'card' => 'summary_large_image', // summary
            'type' => 'website', //website, profile
        ];
        if ($this->image && file_exists(Yii::$app->getBasePath() . '/web' . $this->image)) {
            list($width, $height, $type, $attr) = getimagesize(Yii::$app->getBasePath() . '/web' . $this->image);
            $this->defaultTag['image'] =  Url::to($this->image, true);
            $this->defaultTag['image:src'] =  Url::to($this->image, true);
            $this->defaultTag['image:width'] = $width;
            $this->defaultTag['image:height'] = $height;
        }
        if ($this->favicon) {
            $this->setFavicon();
        }
    }

    /**
     * @param array $tags [name => content]
     * name: title, description, keywords, author/creator, image(image:src, image:width, image:height), card: summary/summary_large_image, type: website/profile
     */
    private function tags($tags = [])
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

    //-----------------------------------------------

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
     * 
     */
    private function setFavicon()
    {
        $this->view->registerLinkTag([
            'rel' => 'icon', 'type' => 'image/png', 'href' => Url::to($this->favicon, true)
        ]);
    }
}
