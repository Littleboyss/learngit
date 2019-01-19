<?php
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if($code == 'UTF-8')
    {
       $pa ="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);
        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
        return join('', array_slice($t_string[0], $start, $sublen));
    }else{
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';
        for($i=0; $i< $strlen; $i++)
        {
            if($i>=$start && $i< ($start+$sublen)){
                if(ord(substr($string, $i, 1))>129){
                    $tmpstr.= substr($string, $i, 2);
                }else{
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
        return $tmpstr;
    }
}


function dhtmlspecialchars($string) {
if(is_array($string)) {
foreach($string as $key => $val) {
  $string[$key] = dhtmlspecialchars($val);
}
} else {
$string = str_replace('&', '&', $string);
$string = str_replace('"', '"', $string);
$string = str_replace('<', '<', $string);
$string = str_replace('>', '>', $string);
$string = preg_replace('/&(#\d;)/', '&\1', $string);
}
return $string;
}

function open($file,$type=''){
    global $fromurl;
    $cachename=$file;
          if($type){
              $file=$fromurl.'/'.$type.'/'.$file;
          }else{
              $file=$fromurl.$file;
          }
          if($open=file($file)){
                          $count=count($open);
                          for($i=0;$i<$count;$i++){
                              $theget.=$open[$i];
                          }                           
                    }else{
                          die('请求过多，超时，请刷新');
                    }           
    return $theget;
}

function isUTF8($str) {
    if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8") , "UTF-8", "UTF-32")) {
        return true;
    } else {
        return false;
    }
}

function curl_request($url, $data=array(), $method='GET')
    {
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
function cut($start, $end, $file) {
    $content = explode($start, $file);
    $content = explode($end, $content[1]);
    return $content[0];
}
function cutUTF8String($string, $cut = "|") {
    $length = strlen($string);
    $retstr = '';
    for ($i = 0; $i < $length; $i++) {
        // dump(ord($string[$i]));
        if (ord($string[$i]) > 127) {
            if (ord($string[$i]) == 194 || ord($string[$i]) == 160) {
                if ($i == 6) $retstr.= $cut;
            } else {
                $retstr.= $string[$i];
                $retstr.= $string[++$i];
                $retstr.= $string[++$i];
                if ($i <= $length - 3) $retstr.= $cut;
            }
        } else {
            $retstr.= $string[$i];
            if ($i <= $length - 1) $retstr.= $cut;
        }
    }
    return $retstr;
}
function str_replace_once($find, $replace, $string) {
    $pos = strpos($string, $find);
    if ($pos === false) {
        return $string;
    }
    return substr_replace($string, $replace, $pos, strlen($find));
}

 function str_replace_odds($search, $replace, $subject)
{
    return strtr( $subject, array_combine($search, $replace) );
}
function DateDiff($date1, $date2, $unit = "") { // 时间比较函数，返回两个日期相差几秒、几分钟、几小时或几天
    switch ($unit) {
        case 's':
            $dividend = 1;
            break;
        case 'i':
            $dividend = 60;
            break;
        case 'h':
            $dividend = 3600;
            break;
        case 'd':
            $dividend = 86400;
            break;
        default:
            $dividend = 86400;
    }
    $time1 = strtotime($date1);
    $time2 = strtotime($date2);
    if ($time1 && $time2) return (float)($time1 - $time2) / $dividend;
    return false;
}
function computeWeek($date1, $date2) {
    $diff = strtotime($date2) - strtotime($date1);
    $res = ceil($diff / (24 * 60 * 60 * 7));
    if ($res < 0) $res = 0;
    return $res;
}
// echo strtotime('20140302')-strtotime('20140303');
function calDate($w) {
    // 参数值0表示周日，1表示周一
    return date('Y-m-d', (time() + ($w - date('w')) * 24 * 60 * 60));
}
// echo calDate(6); //输出本周6的日期
// 输入的$data参数为，yy/mm/dd  或者 yy-mm-dd，返回星期几
function getWeekDay($date) {
    $date = str_replace('/', '-', $date);
    $dateArr = explode("-", $date);
    /*
    N  ISO-8601 格式数字表示的星期中的第几天（PHP 5.1.0 新加） 1（表示星期一）到 7（表示星期天）
     w 	星期中的第几天，数字表示 	0（表示星期天）到 6（表示星期六）
    */
    return date("N", mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]));
}
function addZero($N) {
    if ($N == 0) $N = 1;
    if ($N < 10) {
        $N = '00' . $N;
    } elseif ($N <= 99 && $N >= 10) {
        $N = '0' . $N;
    }
    return $N;
}
function istrue_user() {
    $result = false;
    $true_arr = array(
        'MSIE',
        'Firefox',
        'Chrome',
        'Safari',
        'Opera'
    );
    foreach ($true_arr as $v) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $v) !== false) {
            $result = true;
        }
    }
    return $result;
}
function getstr($n) {
    $a = array(
        "A",
        "B",
        "C",
        "D",
        "E",
        "F",
        "G",
        "H",
        "I",
        "J",
        "K",
        "L",
        "M",
        "N",
        "O",
        "P",
        "Q",
        "R",
        "S",
        "T",
        "U",
        "V",
        "W",
        "X",
        "Y",
        "Z"
    );
    $str = '';
    if ($n < 26) {
        $str = $a[$n];
    } else {
        $i = 0;
        while ($i < strlen($n)) {
            $num = substr($n, $i, 1);
            $str.= $a[$num];
            $i++;
        }
    }
    return $str;
}
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash.= $chars[mt_rand(0, $max) ];
    }
    return $hash;
}

function randomstr($length, $chars = 'abcdefghijklmnopqrstuvwxyz0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash.= $chars[mt_rand(0, $max) ];
    }
    return $hash;
}





/**
 * 将字符串转换为数组
 *
 * @param string $data 字符串
 * @return array 返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
    if ($data == '') return array();
    @eval("\$array = $data;");
    return $array;
}
/**
 * 将数组转换为字符串
 *
 * @param array $data 数组
 * @param bool $isformdata 如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return string 返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
    if ($data == '') return '';
    if ($isformdata) $data = new_stripslashes($data);
    return addslashes(var_export($data, TRUE));
}
function unique_arr($array2D) {
    foreach ($array2D as $v) {
        $v = join(",", $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        $temp[] = $v;
    }
    $temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
    foreach ($temp as $k => $v) {
        $temp[$k] = explode(",", $v); //再将拆开的数组重新组装
        
    }
    return $temp;
}
/**
 * iconv 编辑转换
 */
if (!function_exists('iconv')) {
    function iconv($in_charset, $out_charset, $str) {
        $in_charset = strtoupper($in_charset);
        $out_charset = strtoupper($out_charset);
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, $out_charset, $in_charset);
        } else {
            pc_base::load_sys_func('iconv');
            $in_charset = strtoupper($in_charset);
            $out_charset = strtoupper($out_charset);
            if ($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312')) {
                return utf8_to_gbk($str);
            }
            if (($in_charset == 'GBK' || $in_charset == 'GB2312') && $out_charset == 'UTF-8') {
                return gbk_to_utf8($str);
            }
            return $str;
        }
    }
}
/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 *
 * @param  $string
 * @return bool
 */
function is_utf8($string) {
    return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}
/**
 * 根据指定的键值对数组排序
 *
 * @param array $array 要排序的数组
 * @param string $keyname 键值名称
 * @param int $sortDirection 排序方向
 * @return array
 */
function array_column_sort($array, $keyname, $sortDirection = SORT_ASC) {
    return array_sortby_multifields($array, array(
        $keyname => $sortDirection
    ));
}
/**
 * 将一个二维数组按照指定列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * @param array $rowset
 * @param array $args
 */
function array_sortby_multifields($rowset, $args) {
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir) {
        foreach ($rowset as $offset => $row) {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule.= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) {
        return $rowset;
    }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}
/**
 * 对二维数组进行排序
 * @param $array
 * @param $keyid 排序的键值
 * @param $order 排序方式 'asc':升序 'desc':降序
 * @param $type  键值类型 'number':数字 'string':字符串
 */
function sort_array(&$array, $keyid, $order = 'asc', $type = 'number') {
    if (is_array($array)) {
        foreach ($array as $val) {
            $order_arr[] = $val[$keyid];
        }
        $order = ($order == 'asc') ? SORT_ASC : SORT_DESC;
        $type = ($type == 'number') ? SORT_NUMERIC : SORT_STRING;
        array_multisort($order_arr, $order, $type, $array);
    }
}
/**
 * 将一个二维数组按照指定字段的值分组
 *
 * @param array $arr
 * @param string $keyField
 * @return array
 */
function array_group_by(&$arr, $keyField) {
    $ret = array();
    foreach ($arr as $row) {
        $key = $row[$keyField];
        $ret[$key][] = $row;
    }
    return $ret;
}
function dump($vars, $label = '', $return = false) {
    if (ini_get('html_errors')) {
        $content = "<pre>\n";
        if ($label != '') {
            $content.= "<strong>{$label} :</strong>\n";
        }
        $content.= htmlspecialchars(print_r($vars, true));
        $content.= "\n</pre>\n";
    } else {
        $content = $label . " :\n" . print_r($vars, true);
    }
    if ($return) {
        return $content;
    }
    echo $content;
    return null;
}
function del_cache($directory) {
    if (file_exists($directory)) {
        if ($dir_handle = @opendir($directory)) {
            while ($filename = readdir($dir_handle)) {
                if ($filename != "." && $filename != "..") {
                    $subFile = $directory . "/" . $filename;
                    if (is_dir($subFile)) del_cache($subFile);
                    if (is_file($subFile)) unlink($subFile);
                }
            }
            closedir($dir_handle);
            rmdir($directory);
        }
    }
}
// 获取ip
function get_ip() {
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}
function get_browsers() {
    global $_SERVER;
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return "Unknow browser";
    }
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "";
    $browser_ver = "";
    if (preg_match("/MSIE\\s([^\\s|;]+)/i", $agent, $regs)) {
        $browser = "Internet Explorer";
        $browser_ver = $regs[1];
    } else if (preg_match("/FireFox\\/([^\\s]+)/i", $agent, $regs)) {
        $browser = "FireFox";
        $browser_ver = $regs[1];
    } else if (preg_match("/Maxthon/i", $agent, $regs)) {
        $browser = "(Internet Explorer " . $browser_ver . ") Maxthon";
        $browser_ver = "";
    } else if (preg_match("/Opera[\\s|\\/]([^\\s]+)/i", $agent, $regs)) {
        $browser = "Opera";
        $browser_ver = $regs[1];
    } else if (preg_match("/OmniWeb\\/(v*)([^\\s|;]+)/i", $agent, $regs)) {
        $browser = "OmniWeb";
        $browser_ver = $regs[2];
    } else if (preg_match("/Netscape([\\d]*)\\/([^\\s]+)/i", $agent, $regs)) {
        $browser = "Netscape";
        $browser_ver = $regs[2];
    } else if (preg_match("/safari\\/([^\\s]+)/i", $agent, $regs)) {
        $browser = "Safari";
        $browser_ver = $regs[1];
    } else if (preg_match("/NetCaptor\\s([^\\s|;]+)/i", $agent, $regs)) {
        $browser = "(Internet Explorer " . $browser_ver . ") NetCaptor";
        $browser_ver = $regs[1];
    } else if (preg_match("/Lynx\\/([^\\s]+)/i", $agent, $regs)) {
        $browser = "Lynx";
        $browser_ver = $regs[1];
    }
    if ($browser != "") {
        return $browser . " " . $browser_ver;
    } else {
        return "Unknow browser";
    }
}
/*
php5.3中不再支持eregi()函数，而使用preg_match()函数替代。
解决的方法是：将eregi()函数替换成preg_match() 函数。
if(eregi('^test',$file))
可以替换为
if(preg_match('/^test/i',$file))
*/
function get_os() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return "Unknown";
    }
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $os = false;
    if (preg_match("/win/i", $agent) && preg_match("/nt 5.1/i", $agent)) {
        $os = "Windows XP";
    } else if (preg_match("/win 9x/i", $agent) && strpos($agent, "4.90")) {
        $os = "Windows ME";
    } else if (preg_match("/win/i", $agent) && strpos($agent,"98")) {
        $os = "Windows 98";
    } else if (preg_match("/win/i", $agent) && strpos($agent, "95")) {
        $os = "Windows 95";
    } else if (preg_match("/win/i", $agent) && preg_match("/nt 5/i", $agent)) {
        $os = "Windows 2000";
    } else if (preg_match("/win/i", $agent) && preg_match("/nt/i", $agent)) {
        $os = "Windows NT";
    } else if (preg_match("/win/i", $agent) && ereg("32", $agent)) {
        $os = "Windows 32";
    } else if (preg_match("/linux/i", $agent)) {
        $os = "Linux";
    } else if (preg_match("/unix/i", $agent)) {
        $os = "Unix";
    } else if (preg_match("/sun/i", $agent) && preg_match("/os/i", $agent)) {
        $os = "SunOS";
    } else if (preg_match("/ibm/i", $agent) && preg_match("/os/i", $agent)) {
        $os = "IBM OS/2";
    } else if (preg_match("/Mac/i", $agent) && preg_match("/PC/i", $agent)) {
        $os = "Macintosh";
    } else if (preg_match("/PowerPC/i", $agent)) {
        $os = "PowerPC";
    } else if (preg_match("/AIX/i", $agent)) {
        $os = "AIX";
    } else if (preg_match("/HPUX/i", $agent)) {
        $os = "HPUX";
    } else if (preg_match("/NetBSD/i", $agent)) {
        $os = "NetBSD";
    } else if (preg_match("/BSD/i", $agent)) {
        $os = "BSD";
    } else if (ereg("OSF1", $agent)) {
        $os = "OSF1";
    } else if (ereg("IRIX", $agent)) {
        $os = "IRIX";
    } else if (preg_match("/FreeBSD/i", $agent)) {
        $os = "FreeBSD";
    } else if (preg_match("/teleport/i", $agent)) {
        $os = "teleport";
    } else if (preg_match("/flashget/i", $agent)) {
        $os = "flashget";
    } else if (preg_match("/webzip/i", $agent)) {
        $os = "webzip";
    } else if (preg_match("/offline/i", $agent)) {
        $os = "offline";
    } else {
        $os = "Unknown";
    }
    return $os;
}?>
