<?php

namespace App\Services;

use Aws;
use Exception;
use App\Traits\FileOperation;

/**
 * Class S3Service
 * This service allows to send and receive a file on Amazon S3
 */
class S3Service
{
    /**
     * File management tools
     */
    use FileOperation;

    /**
     * The Bucket
     *
     * @var string
     */
    public $bucket;

    /**
     * The file
     *
     * @var string
     */
    public $file;

    /**
     * Initializes connection to Amazon S3
     *
     * @var integer
     */
    public $sdk;

    /**
     * Initializes s3Client to Amazon S3
     *
     * @var integer
     */
    public $s3Client;


    /**
     * S3Service constructor
     * @param $region
     * @param $version
     * @param $access_key
     * @param $secret_key
     * @throws Exception
     */
    public function __construct($region, $version, $access_key, $secret_key)
    {
        /**
         * |-------------------------------------------
         * | These fields are required
         * |-------------------------------------------
         */
        if (empty($region)) {
            throw new Exception('S3Service: empty region');
        }
        if (empty($version)) {
            throw new Exception('S3Service: empty version');
        }
        if (empty($access_key)) {
            throw new Exception('S3Service: empty access_key');
        }
        if (empty($secret_key)) {
            throw new Exception('S3Service: empty secret_key');
        }

        /**
         * |-------------------------------------------
         * | Amazon instance
         * |-------------------------------------------
         */
        try {
            $this->sdk = new Aws\Sdk(
                [
                    'region'      => $region,
                    'version'     => $version,
                    'credentials' => [
                        'key'    => $access_key,
                        'secret' => $secret_key,
                    ],
                ]
            );
        } catch (\Exception $e) {
            Throw new Exception($e->getMessage());
        }

        /**
         * |-------------------------------------------
         * | S3 instance
         * |-------------------------------------------
         */
        try {
            $this->s3Client = $this->sdk->createS3();
        } catch (\Exception $e) {
            Throw new Exception($e->getMessage());
        }
    }

    /**
     * Adds an object to a bucket
     * @return bool
     * @throws Exception
     */
    public function put()
    {
        /**
         * |-------------------------------------------
         * | These fields are required
         * |-------------------------------------------
         */
        if (empty($this->bucket)) {
            throw new Exception();
        }
        if (empty($this->file)) {
            throw new Exception();
        }

        /**
         * |-------------------------------------------
         * | The file must exist
         * |-------------------------------------------
         */
        try {
            $this->isFile($this->file);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        /**
         * |-------------------------------------------
         * | Recover mime type file
         * |-------------------------------------------
         */
        try {
            $contentType = $this->mime($this->file);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        /**
         * |-------------------------------------------
         * | If the key is not set,
         * | the key will be the name of the file
         * |-------------------------------------------
         */
        if (empty($this->key)) {
            $this->key = $this->file;
        }

        /**
         * |-------------------------------------------
         * | Sending the file to Amazon S3
         * |-------------------------------------------
         */
        try {
            $this->s3Client->putObject(
                [
                    'Bucket'      => $this->bucket,
                    'Key'         => $this->key,
                    'SourceFile'  => $this->file,
                    'ContentType' => $contentType,
                    'ACL'         => AWS_ACL,
                    "Metadata"      => [
                        "Content-MD5" => base64_encode($this->file)
                    ],
                    "ContentSHA256" => hash_file("sha256", $this->file)
                ]
            );
        } catch (\Exception $e) {
            die($e->getMessage());
            throw new Exception($e->getMessage());
        }

        return true;

    }

    /**
     * Retrieves objects from Amazon S3
     * @return array
     * @throws Exception
     */
    public function get()
    {
        /**
         * |-------------------------------------------
         * | These fields are required
         * |-------------------------------------------
         */
        if (empty($this->bucket)) {
            throw new Exception();
        }
        if (empty($this->key)) {
            throw new Exception();
        }


        /**
         * |-------------------------------------------
         * | Recovering the file on Amazon S3
         * |-------------------------------------------
         */
        try {
            $getObject = $this->s3Client->getObject(
                [
                    'Bucket' => $this->bucket,
                    'Key'    => $this->key,
                ]
            );
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$getObject) {
            throw new Exception(404);
        }

        return $getObject;

    }

    /**
     * Removing an object from Amazon S3
     * @throws Exception
     */
    public function delete()
    {
        /**
         * |-------------------------------------------
         * | These fields are required
         * |-------------------------------------------
         */
        if (empty($this->bucket)) {
            throw new Exception();
        }
        if (empty($this->key)) {
            throw new Exception();
        }


        /**
         * |-------------------------------------------
         * | Deleting the file on Amazon S3
         * |-------------------------------------------
         */
        try {
            $this->s3Client->deleteObject(
                [
                    'Bucket' => $this->bucket,
                    'Key'    => $this->key,
                ]
            );
        } catch (\Exception $e) {
            throw new Exception();
        }

    }

}
