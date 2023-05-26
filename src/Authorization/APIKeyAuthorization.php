<?php

namespace Lin\IAPI\Authorization;

use Lin\AppPhp\Authorization\AuthorizationInterface;

class APIKeyAuthorization implements AuthorizationInterface {

    protected $StorageFilename = 'api_keys.json';
    protected $StorageDIRPath;
    protected $Salt = '';

    public function __construct($StorageDIRPath, $Salt)
    {
        $this->StorageDIRPath = $StorageDIRPath;
        $this->Salt = $Salt;
        $Filename = $this->StorageDIRPath . '/' . $this->StorageFilename;
        if (!file_exists($Filename)) {
            throw new \Exception('API Key storage file not found.');
        }
        if (!is_writable($Filename)) {
            throw new \Exception('API Key storage file is not writable.');
        }
    }


    public function Authorize($Token, $RequestScopes = [])
    {
        $StorageFullPath = $this->StorageDIRPath . '/' . $this->StorageFilename;
        $Storage = file_get_contents($StorageFullPath);
        $Storage = json_decode($Storage, true);
        if ($Storage === false) return false;

        foreach ($Storage as $i => $Item) {
            // 檢查 $Token 是否正確
            if (
                $Item['key'] !== $this->EncryptKey($Token) ||
                $Item['requestLimit'] <= $Item['requestCount']
            ) {
                continue;
            }
            // 檢查 $RequestScopes 是否正確
            if (
                !empty($RequestScopes) &&
                is_array($Item['scopes']) && !empty($Item['scopes']) &&
                count(array_intersect($RequestScopes, $Item['scopes'])) === 0
            ) {
                break;
            }
            // 更新資料
            $Storage[$i]['lastAccess'] = time();
            $Storage[$i]['requestCount']++;
            file_put_contents($StorageFullPath, json_encode($Storage));
            return true;
        }
        return false;
    }

    protected function EncryptKey($Key)
    {
        return crypt($Key, $this->Salt);
    }
}