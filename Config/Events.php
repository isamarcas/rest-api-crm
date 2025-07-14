<?php

namespace RestApi\Config;

use CodeIgniter\Events\Events;
use RestApi\Libraries\Envapi;

// load all helers on pre_system
Events::on('pre_system', function () {
	helper([
		'general',
		'app_files_helper',
		'currency',
		'api_date_time'
	]);
});
