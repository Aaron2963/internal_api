<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/EndPoint/CacheEndPoint.php';
require_once __DIR__ . '/../src/EndPoint/FileEndPoint.php';
require_once __DIR__ . '/../src/Authorization/APIKeyAuthorization.php';

use Lin\IAPI\EndPoint\CacheEndPoint;
use Lin\IAPI\EndPoint\FileEndPoint;
use Lin\IAPI\Authorization\APIKeyAuthorization;
use Lin\AppPhp\Server\App;

$EndPoint = null;

$DB_HOST = 'db';
$DB_TABLE = 'test';
$DB_USER = 'test';
$DB_PASSWORD = 'test';
$KeySalt = 'salt';

$Link = new PDO("mysql:host=$DB_HOST;dbname=$DB_TABLE", $DB_USER, $DB_PASSWORD);
$Auth = new APIKeyAuthorization(__DIR__ . '/public/keys/', $KeySalt);

if (strpos($_SERVER['REQUEST_URI'], '/example/cache') === 0) {
    $EndPoint = new CacheEndPoint($Link, __DIR__ . '/public/cache/');
} else if (strpos($_SERVER['REQUEST_URI'], '/example/file') === 0) {
    $EndPoint = new FileEndPoint(__DIR__, ['/^public\//']);
}

if ($EndPoint === null) {
    header('HTTP/1.1 404 Not Found');
    exit();
}

$EndPoint->WithAuthorization($Auth);
$EndPoint->HandleRequest(App::CreateServerRequest());
$EndPoint->SendResponse();
exit();