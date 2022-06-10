<?php

namespace denisok94\helper\yii2;

use yii\web\Controller;
use denisok94\helper\Helper as H;

/**
 * StatusController
 * 
 * Для общения по формату json. (REST API)
 * 
 * @example Пример:
 * ```php
 * // получить все сообщение полностью
 * $message = $this->post;
 * // получить параметр из сообщения
 * $phone = $this->getPost('phone');
 * ```
 * 
 * @example Пример:
 * ```php
 * // Сообщить об успешной обработки
 * return $this->success(); // ['status' => 'OK', 'data' => []];
 * // Вернуть результат работы
 * return $this->success($data); // ['status' => 'OK', 'data' => $data];
 * 
 * // Сообщить об ошибке
 * \Yii::$app->response->statusCode = 400; // or status http code
 * return $this->error($error, $text, $data); // ['status' => 'FAIL', ...]
 * 
 * // Собственный формат ответа 
 * $responses = [];
 * return $this->send($responses);
 * ```
 * 
 * @author vitaliy-pashkov 
 */
class StatusController extends Controller
{
    /**
     * @var array
     */
    public $post = [];

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;

        header('Access-Control-Allow-Origin: *');
        $this->post = H::toArray(\Yii::$app->request->rawBody);

        return parent::beforeAction($action);
    }

    /**
     * 
     * @param string $path
     */
    public function getPost(string $path)
    {
        return H::get($this->post, $path);
    }

    /**
     * custom responses
     * @param array|string $data
     * ```php
     * $responses = [];
     * // ...
     * return $this->send($responses);
     * ```
     */
    protected function send($data = [])
    {
        return $data;
    }

    /**
     * success responses
     * @param array|string $data
     * @return array
     *
     * ```php
     * return $this->success(); // ['status' => 'OK', 'data' => []];
     * return $this->success($data); // ['status' => 'OK', 'data' => $data];
     * ```
     */
    protected function success($data = [])
    {
        return ['status' => 'OK', 'data' => $data];
    }

    /**
     * error responses
     * @param array|string $error что не так
     * @param array|string $text где не так
     * @param array|string $data ~тело запроса
     * @return array ['status'=>'FAIL', 'error', 'errorText', 'data']
     * ```php
     * \Yii::$app->response->statusCode = 400; // or status http code
     * return $this->error($error, $text, $data);
     * ```
     * 
     * @example Пример:
     * ```php
     * $messages = $this->post;
     * if ($messages === null) {
     *  $jsonError = json_last_error();
     *  $jsonErrorMsg = json_last_error_msg();
     *  \Yii::$app->response->statusCode = 400;
     *  return $this->error('messages is null', "json error: " . $jsonError, [
     *      'raw' => \Yii::$app->request->rawBody,
     *      'error_msg' => $jsonErrorMsg
     *  ]);
     * }
     * ```
     */
    protected function error($error = 'customError', $text = '', $data = [])
    {
        if ($error == 'customError') {
            $error = ['text' => $text];
        }
        return ['status' => 'FAIL', 'error' => $error, 'errorText' => $text, 'data' => $data];
    }
}
