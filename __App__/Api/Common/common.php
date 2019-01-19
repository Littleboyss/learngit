<?php
/**
 * 用DES算法加密字符串
 * @param string $string 数组可以转化成json格式的字符串
 * @param string $key
 * @return string 
 */
function des_encrypt($string, $key) {
    $size = mcrypt_get_block_size('des', 'ecb');
    $string = mb_convert_encoding($string, 'GBK', 'UTF-8');
    $pad = $size - (strlen($string) % $size);
    $string = $string . str_repeat(chr($pad), $pad);
    $td = mcrypt_module_open('des', '', 'ecb', '');
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
    mcrypt_generic_init($td, $key, $iv);
    $data = mcrypt_generic($td, $string);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $data = base64_encode($data);
    return $data;
}

/**
 * 用DES算法解密字符串
 * @param string $string
 * @param string $key
 * @return mixed 
 */
function des_decrypt($string, $key) {
    $string = base64_decode($string);
    $td = mcrypt_module_open('des', '', 'ecb', '');
    $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
    //$ks = mcrypt_enc_get_key_size($td);
    mcrypt_generic_init($td, $key, $iv);
    $decrypted = mdecrypt_generic($td, $string);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $pad = ord($decrypted{strlen($decrypted) - 1});
    if ($pad > strlen($decrypted)) {
        return false;
    }
    if (strspn($decrypted, chr($pad), strlen($decrypted) - $pad) != $pad) {
        return false;
    }
    $result = substr($decrypted, 0, -1 * $pad);
    $result = mb_convert_encoding($result, 'UTF-8', 'GBK');
    return $result;
}
function curl_request($url, $data=array(), $method='GET',$gzip = false){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20110619 Firefox/5.0');
    #curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.117 Safari/537.36');// chrome
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1');// 360
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)');//ie10
    if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 关键在这里
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36 OPR/20.0.1387.91');//opera
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    } else if ($method == 'GET') {
        curl_setopt($ch, CURLOPT_POST, false);
        $url = $url . '?' . http_build_query($data, '', '&');
    } else {
        // custom method
    }
    //dump($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch) != CURLE_OK || $http_code != 200) {
        $response = '';
    }
    curl_close($ch);
    return $response;
}

/**
* @param $array 需要进行排序的二维数组
* @param $field 需要进行排序的字段
* @param $if_field 数组唯一字段,用来恢复数组原来键值
* @param $sort 排序的方式
*
* @author wh 2017.8.21
*
* @return 返回排序好的数组
*/
function arraySequence($array, $field, $if_field, $sort = 'SORT_DESC'){
    $array1 = $array;
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    $a = array();
    foreach ($array as $key => $value) {
        foreach ($array1 as $k => $v) {
            if($value[$if_field] == $v[$if_field]){
                // echo $array1[$key];
                $a[$k] = $value;
            }
        }
    }

    return $a;
}