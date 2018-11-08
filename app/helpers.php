<?php

/**
 * @desc generate a random string with specified length
 * @author Tom 2017-07-18
 * @return string
 */
function random_string($length = 32) {
    return substr(sha1(rand()), 0, $length);
}

/**
 * @desc filt query params avoid of xss attacking
 * @author Tom 2017-07-20
 * @param request Request object
 * @param name specified query param name
 * @param type 0:string, default 1:int
 * @return string or int
 */
function query_param($request, $name, $type = 0) {
    return $request->input($name);
}

/**
 * TODO: filt querying params in routes
 * @author Tom 2017-07-25
 * @param param_name param name
 * @return string or int
 */
function clean_param($param_name) {
    return $param_name;
}
