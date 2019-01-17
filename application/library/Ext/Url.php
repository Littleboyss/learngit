<?php
class Ext_Url
{
    /**
     * 通过原图片地址以及尺寸返回对应尺寸的图片地址
     * @param string $imgFile 图片地址
     * @param string $size 需要设置尺寸[70_70,204_204,400_400]
     * @return string
     */
    public static function imageUrlBySize($imgFile, $size)
    {
        if (!$imgFile) {
            $nopicSize = '';
            if ($size == '204_204' || $size == '400_400') {
                $nopicSize = '_204';
            }
            if ($size == '70_70') {
                $nopicSize = '_50';
            }
            return LEASE_DOMAIN . '/resource/common/image/noPic' . $nopicSize . '.png';
        }

        // 图片以传参方式传入高宽
        if ($size) {
            $sizeMap = explode('_', $size);
            if (count($sizeMap) == 2) {
                return PHOTO_PATH . $imgFile . '?w=' . $sizeMap[0] . '&h=' . $sizeMap[0] . '';
            }
        }
        // 如果没有高宽参数，则展示原图

        return PHOTO_PATH . $imgFile;
    }
}