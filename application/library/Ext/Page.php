<?php
/**
 * 分页工具
 */
class Ext_Page
{
    /**
     * 获取请求地址，并保护请求参数，去除分页参数
     *
     * @param Yaf_Dispatcher $dispatcher
     * @return void
     */
    private static function getPageUrl(Yaf_Dispatcher $dispatcher)
    {
        $uri = $dispatcher->getRequest()->getRequestUri();
        $query = $_GET;
        $query = array_filter($query, 'strlen');
        if (isset($query['page'])) {
            unset($query['page']);
        }
        return $uri . '?' . http_build_query($query);
    }

    public static function render($count, $page, $page_size = 10, $opt = '')
    {
        $page_count = ceil($count / $page_size);  //计算得出总页数
        $init = 1;
        $page_len = 7;
        $max_p = $page_count;
        $pages = $page_count;

        if ($pages != 0) {
            //判断当前页码
            $page = (empty($page) || $page < 0) ? 1 : $page;
    
            $url = self::getPageUrl(Yaf_Dispatcher::getInstance());
    
            //获取当前页url
            if (substr($url, -1) != '&') {
                $url .= '&';
            }
    
            //分页功能代码
            $page_len = ($page_len % 2) ? $page_len : $page_len + 1;  //页码个数
            $pageoffset = ($page_len - 1) / 2;  //页码个数左右偏移量
    
            $navs = '<div class="page_container"><div class="page_container_nav">';
            if ($page != 1) {
                $navs .= '<a href="' . $url . 'page=' . ($page - 1) . '" class="page_active"><i class="fa fa-angle-left"></i></a>'; //上一页
            } else {
                $navs .= '<a href="javascript:;" class="page_negative"><i class="fa fa-angle-left"></i></a>';
            }
            if ($pages > $page_len) {
                //如果当前页小于等于左偏移
                if ($page <= $pageoffset) {
                    $init = 1;
                    $max_p = $page_len;
                } else  //如果当前页大于左偏移
                {
                    //如果当前页码右偏移超出最大分页数
                    if ($page + $pageoffset >= $pages + 1) {
                        $init = $pages - $page_len + 1;
                    } else {
                        //左右偏移都存在时的计算
                        $init = $page - $pageoffset;
                        $max_p = $page + $pageoffset;
                    }
                }
            }
            for ($i = $init; $i <= $max_p; $i++) {
                if ($i == $page) {
                    $navs .= '<a href="javascript:;" class="page_cur">' . $i . '</a>';
                } else {
                    $navs .= '<a href="' . $url . 'page=' . $i . '" class="page_num">' . $i . '</a>';
                }
            }
            if ($page != $pages) {
                $navs .= '<a href="' . $url . 'page=' . ($page + 1) . '" class="page_active"><i class="fa fa-angle-right"></i></a>';//下一页
            } else {
                $navs .= '<a href="javascript:;" class="page_negative"><i class="fa fa-angle-right"></i></a>';
            }
            $navs .= '<span class="page_skip">到第<input type="text" class="page_count" value="1">页</span>
                    <a href="javascript:;" class="page_confirm" url="' . $url . 'page=">确定</a></div></div>';
            return $navs;
        }
        return '';
    }        
}
