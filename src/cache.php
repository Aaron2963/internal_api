<?php

namespace Lin\IAPI;

use Lin\AppPhp\Server\RestfulApp;

class Cache extends RestfulApp
{
    /**
     * Link
     *
     * @var \PDO
     */
    public $Link;

    /**
     * 
     *
     * @var string
     */
    public $RootPath;

    public function __construct($Link, $RootPath)
    {
        $this->Link = $Link;
        $this->RootPath = $RootPath;
        parent::__construct();
    }

    public function OnPost()
    {
        try {
            // validation
            if (empty($_POST['fromDB']) || empty($_POST['storePath']) || empty($_POST['keys']) || empty($_POST['values'])) {
                throw new \Exception('lack of mandatory parameters: fromDB, storePath, keys, filenames');
            }
            if (!preg_match('/^[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)+$/', $_POST['fromDB'])) {
                throw new \Exception('fromDB format error, should be like `table_name.column_name`');
            }
            if (!is_writable($_POST['storePath']) || !is_dir($_POST['storePath']) || !mkdir($_POST['storePath'], 0777, true)) {
                throw new \Exception('storePath not exists and can not create, or not writable');
            }
            $Keys = explode(',', $_POST['keys']);
            $Keys = array_map('trim', $Keys);
            $Filenames = explode(',', $_POST['filenames']);
            $Filenames = array_map('trim', $Filenames);
            if (count($Keys) !== count($Filenames)) {
                throw new \Exception('keys and filenames count not match');
            }
            // get table primary key column name
            $Table = explode('.', $_POST['fromDB'])[0];
            $Column = explode('.', $_POST['fromDB'])[1];
            $TablePK = $this->Link->query("SHOW KEYS FROM $Table WHERE Key_name = 'PRIMARY';");
            $TablePK = $TablePK->fetchAll()['Column_name'];
            // get data from db
            $Sql = "SELECT $Column FROM $Table WHERE ";
            $KeyValues = [];
            foreach ($Keys as $Key) {
                $Sql .= "$TablePK = ? OR ";
                $KeyValues[] = $Key;
            }
            $Sql = substr($Sql, 0, -4);
            $Sql .= ';';
            $Stmt = $this->Link->prepare($Sql);
            $Stmt->execute($KeyValues);
            $Result = $Stmt->fetchAll();
            $Result = array_column($Result, $Column);
            // save to file
            $SavedFiles = [];
            foreach ($Filenames as $i => $Filename) {
                $FullPath = $this->RootPath . '/' .  $_POST['storePath'] . '/' . $Filename;
                $File = fopen($FullPath, 'w');
                fwrite($File, $Result[$i]);
                fclose($File);
                $SavedFiles[] = $FullPath;
            }
            // response
            $ResponseBody = $this->Psr17Factory->createStream(json_encode($SavedFiles, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES));
            $Response = $this->Psr17Factory->createResponse(200)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withBody($ResponseBody);
            return $Response;
        } catch (\Exception $Error) {
            $ResponseBody = $this->Psr17Factory->createStream(json_encode([
                'status' => 'error',
                'message' => $Error->getMessage(),
            ], JSON_UNESCAPED_UNICODE));
            $Response = $this->Psr17Factory->createResponse(400)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withBody($ResponseBody);
            return $Response;
        }
    }
}
