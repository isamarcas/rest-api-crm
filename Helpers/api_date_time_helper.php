<?php 

	/**
 * get user's time zone offset 
 * 
 * @return active users timezone
 */

function api_get_timezone_offset($date = "now") {
//Custom modification done by CIJAGANI
	$timeZone = new DateTimeZone("UTC");       
	$dateTime = new DateTime($date, $timeZone);
	return $timeZone->getOffset($dateTime);
}

function api_get_current_utc_time($format = "Y-m-d H:i:s") {
    $d = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
    $d->setTimeZone(new DateTimeZone("UTC"));
    return $d->format($format);
}

function api_get_my_local_time($format = "Y-m-d H:i:s") {
    return date($format, strtotime(api_get_current_utc_time()) + api_get_timezone_offset());
}

?>