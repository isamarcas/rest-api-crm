<?php

namespace RestApi\Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig {

	/*
	|--------------------
	| JWT Secure Key
	|--------------------------------------------------------------------------
	*/
	public $jwt_key = 'eyJ0eXAiOiJKV1QiLCJhbGciTWeLUzI1NiJ9IiRkYXRhIz';


	/*
	|-----------------------
	| JWT Algorithm Type
	|--------------------------------------------------------------------------
	*/
	public $jwt_algorithm = 'HS512';


	/*
	|-----------------------
	| Token Request Header Name
	|--------------------------------------------------------------------------
	*/
	public $token_header = 'authtoken';


	/*
	|-----------------------
	| Token Expire Time

	| https://www.tools4noobs.com/online_tools/hh_mm_ss_to_seconds/
	|--------------------------------------------------------------------------
	| ( 1 Day ) : 60 * 60 * 24 = 86400
	| ( 1 Hour ) : 60 * 60     = 3600
	| ( 1 Minute ) : 60        = 60
	*/
	public $token_expire_time = 315569260;
}
