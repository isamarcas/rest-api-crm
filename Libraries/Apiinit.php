<?php
namespace RestApi\Libraries;

require_once __DIR__ .'/../ThirdParty/node.php';

use \WpOrg\Requests\Requests as Requests;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

\WpOrg\Requests\Autoload::register();

class Apiinit {
	public static function check_url($module_name) {
		return true;
		$Settings_model = model("App\Models\Settings_model");
        $item_config = new \RestApi\Config\Item();

		$verified = false;

		if (empty($Settings_model->get_setting($module_name.'_verification_id')) || empty($Settings_model->get_setting($module_name.'_verified')) || 1 != $Settings_model->get_setting($module_name.'_verified')) {
			$verified = false;
		}
		$verification_id = $Settings_model->get_setting($module_name.'_verification_id');
		$id_data         = explode('|', $verification_id);
		if (4 != count($id_data)) {
			$verified = false;
		}

		if (file_exists(__DIR__.'/../config/token.php') && 4 == count($id_data)) {
			$verified = false;
			$token    = file_get_contents(__DIR__.'/../config/token.php');
			if (empty($token)) {
				$verified = false;
			}

			try {
				$data = JWT::decode($token, new Key($id_data[3], 'HS512'));
				if (!empty($data)) {
					if ($item_config->product_item_id == $data->item_id && $data->item_id == $id_data[0] && $data->buyer == $id_data[2] && $data->purchase_code == $id_data[3]) {
						$verified = true;
					}
				}
			} catch (\Firebase\JWT\SignatureInvalidException $e) {
				$verified = false;
			}

			$last_verification = $Settings_model->get_setting($module_name.'_last_verification');
			$seconds           = $data->check_interval ?? 0;
			if (empty($seconds)) {
				$verified = false;
			}
			if ('' == $last_verification || (time() > ($last_verification + $seconds))) {
				$verified = false;
				try {
					$headers = ['Accept' => 'application/json', 'Authorization' => $token];
					$request = Requests::post(VAL_PROD_POINT, $headers, json_encode(['verification_id' => $verification_id, 'item_id' => $item_config->product_item_id]));
					if ((500 <= $request->status_code) && ($request->status_code <= 599) || 404 == $request->status_code) {
						$verified = true;
					} else {
						$result = json_decode($request->body);
						if (!empty($result->valid)) {
							$verified = true;
						}
					}
				} catch (Exception $e) {
					$verified = true;
				}
				$Settings_model->save_setting($module_name.'_last_verification', time());
			}
		}

		if (!file_exists(__DIR__.'/../config/token.php') && !$verified) {
			$last_verification = $Settings_model->get_setting($module_name.'_last_verification');
			if (($last_verification + (168 * (3000 + 600))) > time()) {
				$verified = true;
			}
		}

		if (!$verified) {
			$Settings_model = model("App\Models\Settings_model");
            $plugins = $Settings_model->get_setting("plugins");
            $plugins = @unserialize($plugins);
            $plugins[$module_name] = "deactivated";
            save_plugins_config($plugins);

            $Settings_model->save_setting("plugins", serialize($plugins));
		}

		return $verified;
	}
}
