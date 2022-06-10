<?php

namespace denisok94\helper\yii2;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use denisok94\helper\traits\ArrayHelper;

/**
 * Для общения по формату json.
 * 
 * @example Пример:
 * ```php
 * // получить все данные
 * $message = $this->post; // array
 * // получить параметр из данных
 * $phone = $this->getPost('phone'); // phone or null
 * 
 * // return ['code', 'status', 'message', 'data'];
 * return $this->buildSuccess(); // http status code 200
 * return $this->buildSuccess($data); // 200
 * return $this->buildResponse($data); // 200
 * return $this->buildError(); // 400
 * return $this->buildError($message); // 400
 * return $this->buildError($message, $data, 999); // 999
 * 
 * // return ['code', 'status', 'message'];
 * return $this->buildBadRequest(); // 400
 * return $this->buildUnauthorized(); // 401
 * return $this->buildForbidden(); // 403
 * return $this->buildNotFound(); // 404
 * return $this->buildInternalServerError(); // 500
 * 
 * if (!$this->post) {
 *  return $this->buildBadRequest("Request is null"); // 400
 * }
 * 
 * try {
 *  //code...
 * } catch (\Exception $e) {
 *  return $this->buildInternalServerError($e->getMessage()); // 500
 * }
 * 
 * // Собственный формат ответа / Custom responses
 * return $this->buildResponse($data, $message); // 200
 * return $this->buildResponse($data, $message, $status, 999); // 999
 * 
 * return $this->send([]); // 200
 * return $this->send(['code' => 204]); // 204
 * return $this->send(['code' => 201, 'data' => $data]); // 201
 * ```
 */
class StatusController extends Controller
{
    /**
     * @var array
     */
    public $post = [];

    public const CODE_OK = 200;
    public const CODE_CREATED = 201;
    public const CODE_NO_CONTENT = 204;
    public const CODE_BAD_REQUEST = 400;
    public const CODE_UNAUTHORIZED = 401;
    public const CODE_FORBIDDEN = 403;
    public const CODE_NOT_FOUND = 404;
    public const CODE_INTERNAL_SERVER_ERROR = 500;

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;

        header('Access-Control-Allow-Origin: *');
        $this->post = ArrayHelper::toArray(Yii::$app->request->rawBody);

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        if (isset($result['code'])) {
            Yii::$app->response->statusCode = (int)$result['code'];
        }
        return parent::afterAction($action, $result);
    }

    /**
     * 
     * @param string $path
     */
    public function getPost(string $path)
    {
        return ArrayHelper::get($this->post, $path);
    }

    //-------------------------------
    // build

    /**
     * Custom responses
     * @param mixed $data
     * @param string $message
     * @param string $status
     * @param integer $code
     * @return array
     * 
     * @example Пример:
     * ```php
     * return $this->buildResponse($data); // 200
     * return $this->buildResponse($data, $message); // 200
     * return $this->buildResponse($data, $message, $status, 999); // 999
     * ```
     */
    protected function buildResponse($data = [], string $message = '', string $status = 'OK', int $code = StatusController::CODE_OK)
    {
        return [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * 200
     * @param mixed $data
     * @return array
     */
    protected function buildSuccess($data = [])
    {
        return [
            'code' => self::CODE_OK,
            'status' => 'OK',
            'data' => $data
        ];
    }

    /**
     * @param string $message
     * @param mixed $data
     * @param integer $code
     * @return array
     */
    protected function buildError(string $message = 'Error', $data = [], int $code = StatusController::CODE_BAD_REQUEST)
    {
        return [
            'code' => $code,
            'status' => 'FAIL',
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * 400
     * @param string $message
     * @return array
     */
    protected function buildBadRequest(string $message = 'Bad Request')
    {
        return [
            'code' => self::CODE_BAD_REQUEST,
            'status' => 'FAIL',
            'message' => $message
        ];
    }

    /**
     * 401
     * @param string $message
     * @return array
     */
    protected function buildUnauthorized(string $message = "User not Authentication")
    {
        return [
            'code' => self::CODE_UNAUTHORIZED,
            'status' => 'FAIL',
            'message' => $message
        ];
    }

    /**
     * 403
     * @param string $message
     * @return array
     */
    protected function buildForbidden(string $message = 'Forbidden')
    {
        return [
            'code' => self::CODE_FORBIDDEN,
            'status' => 'FAIL',
            'message' => $message
        ];
    }

    /**
     * 404
     * @param string $message
     * @return array
     */
    protected function buildNotFound(string $message = 'Not Found')
    {
        return [
            'code' => self::CODE_NOT_FOUND,
            'status' => 'FAIL',
            'message' => $message
        ];
    }

    /**
     * 500
     * @param string $message
     * @return array
     * 
     * @example Пример:
     * ```php
     * try {
     *  //code...
     * } catch (\Exception $e) {
     *  return $this->buildInternalServerError($e->getMessage()); // 500
     * }
     * ```
     */
    protected function buildInternalServerError(string $message = 'Internal Server Error')
    {
        return [
            'code' => self::CODE_INTERNAL_SERVER_ERROR,
            'status' => 'FAIL',
            'message' => $message
        ];
    }

    // end build
    //-------------------------------

    /**
     * custom responses
     * @param mixed $data
     * ```php
     * $responses = [];
     * // ...
     * return $this->send($responses);
     * ```
     * @author vitaliy-pashkov 
     */
    protected function send($data = [])
    {
        return $data;
    }
    
    //-------------------------------
    // old

    /**
     * success responses
     * @param mixed $data
     * @return array
     *
     * ```php
     * return $this->success(); // ['status' => 'OK', 'data' => []];
     * return $this->success($data); // ['status' => 'OK', 'data' => $data];
     * ```
     * --@deprecated Не актуален, используйте: `buildSuccess()`
     * @author vitaliy-pashkov 
     */
    protected function success($data = [])
    {
        return ['status' => 'OK', 'data' => $data];
    }

    /**
     * error responses
     * @param array|string $error что не так
     * @param array|string $text где не так
     * @param mixed $data
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
     * --@deprecated Не актуален, используйте: `buildError()` или `buildBadRequest()` или другой
     * @author vitaliy-pashkov 
     */
    protected function error($error = 'customError', $text = '', $data = [])
    {
        if ($error == 'customError') {
            $error = ['text' => $text];
        }
        return ['status' => 'FAIL', 'error' => $error, 'errorText' => $text, 'data' => $data];
    }
}
