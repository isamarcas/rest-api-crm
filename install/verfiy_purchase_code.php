<?php

require_once __DIR__ .'/../Libraries/Envapi.php';
require_once __DIR__ .'/../Config/Item.php';

require_once __DIR__.'/../vendor/autoload.php';

use RestApi\Libraries\Envapi;
use \WpOrg\Requests\Requests as Requests;
use Firebase\JWT\JWT;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;

\WpOrg\Requests\Autoload::register();

function getUserIP()
{
	$ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

$request = \Config\Services::request();
$agent_data = $request->getUserAgent();


$Settings_model = model("App\Models\Settings_model");

$Settings_model->save_setting($product.'_verification_id', '112233');
$Settings_model->save_setting($product.'_verified', true);
$Settings_model->save_setting($product.'_last_verification', time());

$return = ['status'=>true];
return;
