<?php

namespace denisok94\helper\yii2;

use yii\web\Controller;
use denisok94\helper\Helper as H;

/**
 * 
 * @author vitaliy-pashkov 
 */
class StatusController extends Controller
{

    public $post = [];

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;

        header('Access-Control-Allow-Origin: *');
        $this->post = json_decode(\Yii::$app->request->rawBody, true);

        return parent::beforeAction($action);
    }

    /**
     * 
     */
    public function getPost($path)
    {
        return H::get($this->post, $path);
    }

    /**
     * custom responses
     */
    protected function send($data = [])
    {
        return $data;
    }

    /**
     * 
     */
    protected function success($data = [])
    {
        return ['status' => 'OK', 'data' => $data];
    }

    /**
     * 
     */
    protected function error($error = 'customError', $text = '', $data = [])
    {
        if ($error == 'customError') {
            $error = ['text' => $text];
        }
        return ['status' => 'FAIL', 'error' => $error, 'errorText' => $text, 'data' => $data];
    }
}
