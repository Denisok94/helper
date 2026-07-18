<?php

namespace denisok94\helper\other;

use Exception, Throwable;
use Aws\Result;
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartUploadException;
use Aws\Exception\IncalculablePayloadException;
use Psr\Log\LoggerInterface;

/**
 * Class S3DataService
 * @package denisok94\helper\other
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_basic-usage.html
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/php_s3_code_examples.html
 * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-examples-creating-buckets.html
 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/
 * https://github.com/awsdocs/aws-doc-sdk-examples/tree/main/php/example_code/s3/s3_basics
 */
class S3DataService
{
    private string $aws_url, $access_key, $secret_access_key, $default_region;
    private bool $aws_use_path_style_endpoint;
    /** @var S3Client|null  */
    private $s3Client = null;
    /** @var string|null  */
    private $bucket;
    /** @var LoggerInterface|null */
    private $logger;

    /**
     * Summary of __construct
     * @param string $aws_url
     * @param string $access_key
     * @param string $secret_access_key
     * @param string $default_region
     * @param bool $aws_use_path_style_endpoint
     */
    public function __construct(
        string $aws_url,
        string $access_key,
        string $secret_access_key,
        string $default_region = 'us-east-1',
        bool $aws_use_path_style_endpoint = true
    ) {
        $this->aws_url = $aws_url;
        $this->access_key = $access_key;
        $this->secret_access_key = $secret_access_key;
        $this->default_region = $default_region;
        $this->aws_use_path_style_endpoint = $aws_use_path_style_endpoint;
    }

    /**
     * @param string $bucket AWS_BUCKET|S3_BUCKET
     * @return self
     */
    public function setBucket(string $bucket): self
    {
        $this->bucket = $bucket;
        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return S3DataService
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    private function getClient(): ?S3Client
    {
        // Если уже есть — возвращаем
        if ($this->s3Client !== null) {
            return $this->s3Client;
        }
        if ($this->bucket == null) {
            throw new Exception('задайте bucket к которому нужно обращаться setBucket($bucket)');
        }

        try {
            $this->s3Client = new S3Client([
                'version'   => 'latest',
                'region'    => $this->default_region,
                'endpoint'  => $this->aws_url,
                'use_path_style_endpoint' => $this->aws_use_path_style_endpoint,
                'credentials' => [
                    'key'       => $this->access_key,
                    'secret'    => $this->secret_access_key,
                ]
            ]);
            return $this->s3Client;
        } catch (Throwable $e) {
            $this->log('connect', $this->aws_url,  $e);
            $this->s3Client = null;
            return null;
        }
    }

    /**
     * @param string $method
     * @param string $params
     * @param AwsException|MultipartUploadException|IncalculablePayloadException|Throwable $e
     * @return void
     */
    private function log(string $method, string $params, $e)
    {
        $msg = sprintf("S3DataService::%s(%s) - %s(%s:%s)", $method, $params, $e->getMessage(), $e->getFile(), $e->getLine());
        if ($this->logger) {
            $this->logger->error($msg);
        } else {
            error_log('error|' . $msg);
        }
    }

    //------------------------

    /**
     * @return string|null
     */
    public function getBucket(): ?string
    {
        return $this->bucket;
    }

    /**
     * @return S3Client|null
     */
    public function getS3Client(): ?S3Client
    {
        return $this->s3Client;
    }

    //---------------------

    /**
     * @param string|null $prefix
     * @return array
     */
    public function getList(?string $prefix = null): array
    {
        $objects = [];
        try {
            if (!$this->getClient()) {
                return [];
            }
            /** @var Result $result */
            $result = $this->s3Client->listObjects([
                'Bucket' => $this->bucket,
                'Prefix' => $prefix
            ]);

            if ($result->get('Contents') !== null) {
                $objects = $result->get('Contents');
            }
        } catch (AwsException | IncalculablePayloadException $e) {
            $this->log('getList', $prefix,  $e);
        }
        return $objects;
    }

    /**
     * @param string|null $prefix
     * @return Result[]
     * [key=>object]
     */
    public function getListObjects(?string $prefix = null): array
    {
        $objects = [];
        try {
            if (!$this->getClient()) {
                return [];
            }
            /** @var Result $result */
            $result = $this->s3Client->listObjects([
                'Bucket' => $this->bucket,
                'Prefix' => $prefix
            ]);

            if ($result->get('Contents') !== null) {
                foreach ($result->get('Contents') as $object) {
                    $objects[$object['Key']] = $this->getObject($object['Key']);
                }
            }
        } catch (AwsException | IncalculablePayloadException $e) {
            $this->log('getListObjects', $prefix,  $e);
        }
        return $objects;
    }

    /**
     * @param string|null $key
     * @return Result|null 
     */
    public function getObject(?string $key = null): ?Result
    {
        try {
            if (!$this->getClient()) {
                return null;
            }
            /** @var Result $result */
            $result = $this->s3Client->getObject([
                'Bucket'    => $this->bucket,
                'Key'       => $key
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            $this->log('getObject', $key,  $e);
        }
        return null;
    }

    /**
     * @param string|null $key
     * @return bool
     */
    public function doesObjectExists(?string $key): bool
    {
        if (!$this->getClient()) {
            return false;
        }
        if (!empty($key) || $key != '') {
            return $this->s3Client->doesObjectExist($this->bucket, $key);
        } else {
            return false;
        }
    }

    /**
     * @param string|null $key
     * @param mixed  $body
     * @return Result|null
     */
    public function setObject(?string $key = null, $body): ?Result
    {
        try {
            if (!$this->getClient()) {
                return null;
            }
            /** @var Result $result */
            $result = $this->s3Client->putObject([
                'Bucket'    => $this->bucket,
                'Key'       => $key,
                'Body'      => $body
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            $this->log('setObject', $key,  $e);
        }
        return null;
    }

    /**
     * @param string|null $oldKey
     * @param string|null $newKey
     * @return Result|null 
     */
    public function copyObject(?string $oldKey = null, ?string $newKey = null): ?Result
    {
        try {
            if (!$this->getClient()) {
                return null;
            }
            /** @var Result $result */
            $result = $this->s3Client->copyObject([
                'Bucket'        => $this->bucket,
                'CopySource'    => $this->bucket . ($oldKey ? '/' . $oldKey : ''),
                'Key'           => $newKey,
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            $this->log('setObject', "$oldKey, $newKey",  $e);
        }
        return null;
    }

    /**
     * @param string|null $key
     * @return Result|null 
     */
    public function deleteObject(?string $key = null): ?Result
    {
        try {
            if (!$this->getClient()) {
                return null;
            }
            /** @var Result $result */
            $result = $this->s3Client->deleteObject([
                'Bucket'    => $this->bucket,
                'Key'       => $key
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            $this->log('deleteObject', $key,  $e);
        }
        return null;
    }

    //---------------------

    /**
     * MultipartUploader
     * @param string $filePath
     * @param string|null $key
     * @return Result|null
     */
    public function setFile(string $filePath, ?string $key = null): ?Result
    {
        try {
            if (!$this->getClient()) {
                return null;
            }
            $uploader = new MultipartUploader($this->s3Client, $filePath, [
                'Bucket' => $this->bucket,
                'Key' => $key
            ]);
            return $uploader->upload();
        } catch (AwsException | MultipartUploadException | IncalculablePayloadException $e) {
            $this->log('setFile', $key,  $e);
        }
        return null;
    }

    /**
     * @param string $filePath
     * @param string|null $key
     * @return Result|null
     */
    public function setFile2(string $filePath, ?string $key = null): ?Result
    {
        try {
            if (!$this->getClient()) {
                return null;
            }
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
                // 'ACL'    => 'public-read',
            ]);
            return $result;
        } catch (AwsException | MultipartUploadException | IncalculablePayloadException $e) {
            $this->log('setFile2', $key,  $e);
        }
        return null;
    }

    /**
     *
     * @param string $localDirPath
     * @param string|null $key
     * @return string|boolean
     */
    public function getFile(string $localDirPath, ?string $key = null, ?string $newFileName = null)
    {
        $file = $this->getObject($key);
        if ($file) {
            $this->s3Client->registerStreamWrapper();
            $fileUrl = str_replace('//', '/', $this->bucket . '/' . $key);
            if (empty($newFileName)) {
                $newFile = str_replace('//', '/', $localDirPath . '/' . $key);
                $path = pathinfo($newFile, PATHINFO_DIRNAME);
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
            } else {
                $newFile = str_replace('//', '/', $localDirPath . '/' . $newFileName);
            }
            //скачать объект
            $newFile  = $newFileName ? str_replace('//', '/', $localDirPath . '/' . $newFileName) : $newFile;
            if ($this->downloadFile($fileUrl, $newFile)) {
                return $newFile;
            } else {
                $msg = "не удалось скачать файл по ссылке 's3://$fileUrl'";
                if ($this->logger) {
                    $this->logger->error($msg);
                } else {
                    error_log('error|' . $msg);
                }
            }
        } else {
            $msg = sprintf('объект файла не найден: key-%s bucket-%s', $key, $this->bucket);
            if ($this->logger) {
                $this->logger->error($msg);
            } else {
                error_log('error|' . $msg);
            }
        }
        return false;
    }

    /**
     * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-stream-wrapper.html
     * @param string $url
     * @param string $path
     * @return boolean
     */
    private function downloadFile(string $url, string $path): bool
    {
        $newFile = $path;

        // Open a stream in read-only mode
        if ($stream = fopen("s3://$url", 'rb')) {
            $newf = fopen($newFile, 'wb');
            if ($newf) {
                // While the stream is still open
                while (!feof($stream)) {
                    // Read 1,024 bytes from the stream
                    fwrite($newf, fread($stream, 1024 * 8), 1024 * 8);
                }
            }
            // Be sure to close the stream resource when you're done with it
            fclose($stream);
            if ($newf) {
                fclose($newf);
            }
        }
        if (file_exists($newFile)) {
            return true;
        } else {
            return false;
        }
    }
}
