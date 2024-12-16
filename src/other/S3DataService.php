<?php

namespace denisok94\helper\other;

use Throwable;
use Aws\Result;
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\AwsException;
use Aws\Exception\MultipartUploadException;
use Aws\Exception\IncalculablePayloadException;

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
    /** @var S3Client|null  */
    private $s3Client;
    /** @var string|null  */
    private $bucket;

    /**
     *
     * @param string|null $bucket AWS_BUCKET|S3_BUCKET_PHOTO
     */
    public function __construct(?string $bucket = null)
    {
        $this->bucket = ($bucket != null) ? $bucket : $_ENV('AWS_BUCKET');
        try {
            $this->s3Client = new S3Client([
                'version'   => 'latest',
                'region'    => $_ENV['S3_REGION'] ?? 'us-east-1',
                'endpoint'  => $_ENV['S3_HOST'],
                'use_path_style_endpoint' => $_ENV['S3_USE_PATH_STYLE_ENDPOINT'] ? true : false,
                'credentials' => [
                    'key'       => $_ENV['S3_ACCESS_KEY'],
                    'secret'    => $_ENV['S3_SECRET_KEY'],
                ]
            ]);
        } catch (Throwable $e) {
            error_log('warning|' . sprintf("S3DataService::connect %s - %s(%s:%s)", $_ENV['S3_HOST'], $e->getMessage(), $e->getFile(), $e->getLine()));
            $this->s3Client = null;
            //throw $th;
        }
    }

    /**
     * @return string
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * @param string $bucket 
     * @return self
     */
    public function setBucket(string $bucket): self
    {
        $this->bucket = $bucket;
        return $this;
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
            /** @var Result $result */
            $result = $this->s3Client->listObjects([
                'Bucket' => $this->bucket,
                'Prefix' => $prefix
            ]);

            if ($result->get('Contents') !== null) {
                $objects = $result->get('Contents');
            }
        } catch (AwsException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::getList(%s) - %s(%s:%s)", $prefix, $e->getMessage(), $e->getFile(), $e->getLine()));
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
            error_log('warning|' . sprintf("S3DataService::getListObjects(%s) - %s(%s:%s)", $prefix, $e->getMessage(), $e->getFile(), $e->getLine()));
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
            /** @var Result $result */
            $result = $this->s3Client->getObject([
                'Bucket'    => $this->bucket,
                'Key'       => $key
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::getObject(%s) - %s(%s:%s)", $key, $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        return null;
    }

    /**
     * @param string|null $key
     * @return bool
     */
    public function doesObjectExists(?string $key): bool
    {
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
            /** @var Result $result */
            $result = $this->s3Client->putObject([
                'Bucket'    => $this->bucket,
                'Key'       => $key,
                'Body'      => $body
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::setObject(%s) - %s(%s:%s)", $key, $e->getMessage(), $e->getFile(), $e->getLine()));
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
            /** @var Result $result */
            $result = $this->s3Client->copyObject([
                'Bucket'        => $this->bucket,
                'CopySource'    => $this->bucket . ($oldKey ? '/' . $oldKey : ''),
                'Key'           => $newKey,
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::copyObject(%s,%s) - %s(%s:%s)", $oldKey, $newKey, $e->getMessage(), $e->getFile(), $e->getLine()));
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
            /** @var Result $result */
            $result = $this->s3Client->deleteObject([
                'Bucket'    => $this->bucket,
                'Key'       => $key
            ]);
            return $result;
        } catch (AwsException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::deleteObject(%s) - %s(%s:%s)", $key, $e->getMessage(), $e->getFile(), $e->getLine()));
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
            $uploader = new MultipartUploader($this->s3Client, $filePath, [
                'Bucket' => $this->bucket,
                'Key' => $key
            ]);
            return $uploader->upload();
        } catch (AwsException | MultipartUploadException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::setFile() - %s(%s:%s)", $e->getMessage(), $e->getFile(), $e->getLine()));
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
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
                // 'ACL'    => 'public-read',
            ]);
            return $result;
        } catch (AwsException | MultipartUploadException | IncalculablePayloadException $e) {
            error_log('warning|' . sprintf("S3DataService::setFile2() - %s(%s:%s)", $e->getMessage(), $e->getFile(), $e->getLine()));
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
                error_log('warning|' . "не удалось скачать файл по ссылке 's3://$fileUrl'");
            }
        } else {
            error_log('warning|' . sprintf('объект файла не найден: key-%s bucket-%s', $key, $this->bucket));
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

    //---------------------
}
