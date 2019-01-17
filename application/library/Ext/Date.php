<?php
/**
 * 时间日期处理工具
 */
class Ext_Date
{
    const DAY_SECONDS = 60 * 60 *24;

    const MONTH_DAYS = 30;
    
    /**
     * 根据时间计算天数
     */
    public static function getDays($startDate, $endDate)
    {
        $endDate = strtotime(date('Y-m-d 23:59:59', $endDate));
        $startDate = strtotime(date('Y-m-d 00:00:00', $startDate));
        // 不足一天按一天计算
        return round(($endDate - $startDate) / self::DAY_SECONDS);
    }

    /**
     * 根据天数计算月数
     *
     * @param [type] $days
     * @return void
     */
    public function getMonthCount($days)
    {
        if (!$days) {
            return 0;
        }
        $intMonth = bcdiv($days, self::MONTH_DAYS, 0);
        $floatMonth = bcdiv($days, self::MONTH_DAYS, 1);
        return $floatMonth > $intMonth ? $floatMonth : $intMonth;
    }

    /**
     * 格式化日期
     *
     * @param string $timestrap
     * @return string
     */
    public static function formatDay($timestrap, $format = 'Y-m-d')
    {
        if ($timestrap) {
            return date($format, $timestrap);
        }
        return '';
    }

    /**
     * 格式化时间
     */
    public static function formatTime($timestrap, $format = 'Y-m-d H:i:s')
    {
        if ($timestrap) {
            return date($format, $timestrap);
        }
        return '';
    }

    /**
     * 格式化日期范围内的数据
     *
     * @param [type] $startDate
     * @param [type] $endDate
     * @return void
     */
    public static function formatRangeDay($startDate, $endDate)
    {
        return self::formatDay($startDate, 'Y.m.d').'~'.self::formatDay($endDate, 'Y.m.d');
    }
}