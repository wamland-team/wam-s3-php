<?php

namespace App\Traits;

/**
 * Class FileOperation
 * @package App\Traits
 */
trait FileOperation {

    /**
     * @param $sourceFile
     * @return string
     * @throws \Exception
     */
    public function mime($sourceFile)
    {
        if (empty($sourceFile)) {
            Throw new \Exception();
        }

        /**
         * |-------------------------------------------
         * | Recover mime type file
         * |-------------------------------------------
         */
        $mime = mime_content_type($sourceFile);
        if (!$mime) {
            Throw new \Exception();
        }

        return $mime;
    }

    /**
     * Verifying that the file exists
     * @param $sourceFile
     * @return bool
     * @throws \Exception
     */
    public function isFile($sourceFile)
    {
        if (empty($sourceFile)) {
            Throw new \Exception("The argument is required");
        }

        if (!is_file($sourceFile)) {
            Throw new \Exception("The file does not exist");
        }

        if (!is_readable($sourceFile)) {
            Throw new \Exception("The file is not readable");
        }

        return true;
    }
}