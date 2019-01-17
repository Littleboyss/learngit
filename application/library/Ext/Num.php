<?php
/**
 * 数字处理工具类
 */
class Ext_Num
{
    /**
     * 数字转换为汉字
     * @param Int $num
     * @return string | bool
     */
    public static function toHans(Int $num, $ww = 0)
    {
        if (strlen($num) >= 14) {
            return false;
        }
        $num = intval($num);
        $c1 = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
        $c2 = ['', '十', '百', '千', '万', '十万', '百万', '千万', '亿', '十亿'];

		// 345
        $w = strlen(floor($num / 10));   // strlen 34 => 2

		// ww是上次的位数，w是当前位数，如果上次位数是3（千），此次位数是1（十），数字不是连续的，就追加一个零
        $zero = '';
        if ($ww !== 0 && ($ww - $w) > 1) {
            $zero = '零';
        }

        if (isset($c1[$num])) {
            return $zero . $c1[$num];
        }

		// 位数名字
        $str = $c2[$w];		 			// c2 2 => 百

		// 最高位的数字
        $top = intval(substr($num, 0, 1));	// 345 => 3, 543 => 5
        $remain = $num - $top * pow(10, $w);		// 345 - 3 * pow(10, 2) => 45

		// 23   =  零|'' .     二	    . 十	   .  三
        $retval = $zero . $c1[$top] . $str . self::num_to_zh($remain, $ww = $w);

        if (FALSE !== ($pos = strrpos($retval, '万'))) {
            $count = substr_count($retval, '万') - 1;
            $retval = preg_replace('/万/', '', $retval, $count);
        }
        if (FALSE !== ($pos = strrpos($retval, '亿'))) {
            $count = substr_count($retval, '亿') - 1;
            $retval = preg_replace('/亿/', '', $retval, $count);
        }

        return $retval; // 数字 + 位 + 回调
    }

    /**
     * 金额转换为大写金额
     * @param $num
     * @return string
     */
    public static function toRmb($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) >= 14) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字&ldquo;零&rdquo;
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个&ldquo;零&rdquo;字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上&ldquo;整&rdquo;
        if (empty($c)) {
            return "零元整";
        } else {
            return $c . "整";
        }
    }
}
