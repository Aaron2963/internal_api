<?php

namespace Lin\IAPI\EndPoint;

use Lin\AppPhp\Server\RestfulApp;

class FileEndPoint extends RestfulApp
{
    /**
     * Directory allow to save file
     *
     * @var string
     */
    public $RootPath;
    
    /**
     * Filename allowed patterns
     *
     * @var array
     */
    public $AllowPathPatterns = [];

    public function __construct($RootPath, $AllowPathPatterns = [])
    {
        $this->RootPath = $RootPath;
        $this->AllowPathPatterns = $AllowPathPatterns;
        parent::__construct();
    }

    public function OnGet()
    {
        try {
            // validate if filename matches any of the allowed patterns
            $Allow = false;
            foreach ($this->AllowPathPatterns as $Pattern) {
                if (preg_match($Pattern, $_GET['filename'])) {
                    $Allow = true;
                    break;
                }
            }
            if (!$Allow) {
                throw new \Exception('filename not allowed');
            }
            $FullFilename = $this->RootPath . $_GET['filename'];
            if (!file_exists($FullFilename)) {
                throw new \Exception('file not exists');
            }
            $ResponseBody = $this->Psr17Factory->createStreamFromFile($FullFilename);
            $Response = $this->Psr17Factory->createResponse(200)
                ->withHeader('Content-Type', \mime_content_type($FullFilename))
                ->withBody($ResponseBody);
            return $Response;
        } catch (\Throwable $Exception) {
            $ResponseBody = $this->Psr17Factory->createStream(json_encode([
                'status' => 'error',
                'message' => $Exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE));
            $Response = $this->Psr17Factory->createResponse(400)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withBody($ResponseBody);
            return $Response;
        }
    }

    public function OnPost()
    {
        try {
            // validate if filename matches any of the allowed patterns
            $Allow = false;
            foreach ($this->AllowPathPatterns as $Pattern) {
                if (preg_match($Pattern, $_POST['filename'])) {
                    $Allow = true;
                    break;
                }
            }
            if (!$Allow) {
                throw new \Exception('filename not allowed');
            }
            // validate if file can be written
            $FullFilename = $this->RootPath . $_POST['filename'];
            if (file_exists($FullFilename) && !is_writable($FullFilename)) {
                throw new \Exception('file already exists, and cannot be overwritten');
            }
            $Directory = dirname($FullFilename);
            if (!is_dir($Directory) && !mkdir($Directory, 0777, true)) {
                throw new \Exception('cannot create directory');
            }
            // save file from $_FILES
            move_uploaded_file($_FILES['content']['tmp_name'], $FullFilename);
            $Response = $this->Psr17Factory->createResponse(204);
            return $Response;
        } catch (\Throwable $Exception) {
            $ResponseBody = $this->Psr17Factory->createStream(json_encode([
                'status' => 'error',
                'message' => $Exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE));
            $Response = $this->Psr17Factory->createResponse(400)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withBody($ResponseBody);
            return $Response;
        }
    }
}
