<?php
class Ext_String
{
    // 字符串输出，确保字符串最大长度
    public static function renderStr($str, $maxLen)
    {
        if (mb_strlen($str, 'UTF-8') > $maxLen) {
            return '<span title="' . $str . '">...' . mb_substr($str, -$maxLen, $maxLen, 'UTF-8') . '</span>';
        }
        return $str;
    }
}