<?php
/**
 *
 * @Author: Carl
 * @Since: 2017-08-17 10:25
 * Created by PhpStorm.
 */

/**
 * friendly var dump
 * @param $var
 */
function fn_dump($var) {
    echo '<pre>';
    if (is_array($var)) {
        print_r($var);
    } else {
        var_dump($var);
    }
    echo '</pre>';
}

/**
 * current timestamp
 * @return bool|string
 */
function fn_get_curr_time() {
    return date('Y-m-d H:i:s');
}

/**
 * curl post
 * @param       $url
 * @param       $data
 * @param array $headers
 *
 * @return mixed
 */
function fn_curl_post($url, $data, $headers=[]) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,               $url        );
    curl_setopt($ch, CURLOPT_POST,              TRUE        );
    curl_setopt($ch, CURLOPT_HEADER,            FALSE       );
    curl_setopt($ch, CURLOPT_POSTFIELDS,        $data       );
    curl_setopt($ch, CURLOPT_FRESH_CONNECT,     TRUE	    );	/* no cache				*/
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,    TRUE	    );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    FALSE       ); // 设置为不验证SSL证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    FALSE       ); // 设置为不验证SSL域名
    curl_setopt($ch, CURLOPT_TIMEOUT,           600	        ); // 设置超时限制防止死循环
    if (is_array($headers) && count($headers)>0 ) {
        curl_setopt($ch, CURLOPT_HTTPHEADER,        $headers    );
    }
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}

/**
 * curl get
 * @param       $url
 * @param array $headers
 *
 * @return mixed
 */
function fn_curl_get($url, $headers=[]) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,               $url        );
    curl_setopt($ch, CURLOPT_POST,              FALSE       );
    curl_setopt($ch, CURLOPT_HEADER,            FALSE       );
    curl_setopt($ch, CURLOPT_FRESH_CONNECT,     TRUE	    );	/* no cache				*/
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,    TRUE	    );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    FALSE       ); // 设置为不验证SSL证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    FALSE       ); // 设置为不验证SSL域名
    curl_setopt($ch, CURLOPT_TIMEOUT,           600	        ); // 设置超时限制防止死循环
    if (is_array($headers) && count($headers)>0 ) {
        curl_setopt($ch, CURLOPT_HTTPHEADER,    $headers    );
    }
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}



