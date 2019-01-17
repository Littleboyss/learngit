<?php

/**
 * 用户EAPI
 * Class Eapi_User
 */
class Eapi_User extends \Libs\Eapi\Base
{
    /**
     * 远程登录
     * @param string $inUserName
     * @param string $inUserPass
     * @return mixed
     * admin 13666666666 222222
     * user  13666668888 222222
     */
    public function login($inUserName, $inUserPass)
    {
        $ret = $this->_eapiModel->remoteLogin($inUserName, $inUserPass, '');
        $ret['message'] = $ret['code'].'--'.$ret['message'];
        return $ret;
    }


    /** 根据user_id批量获取账号信息可以单独获取某个字段
     * @param array $userIdRs user_id
     * @param string $fileName 字段名
     * @return array
     */
    public function getUserInFoForBatch($userIdRs, $fileName = '')
    {
        $ret = $this->_eapiModel->getUserInfoBatchByUserId($userIdRs, $fileName);
        $ret['message'] = $ret['code'].'--'.$ret['message'];
        return $ret;
    }

    /**
     * 根据ID获取用户信息
     * @param string $inUserId
     * @return array
     */
    public function getAllUserInfoById($inUserId)
    {
        $ret = $this->_eapiModel->getAllUserInfoById($inUserId);
//        $ret['message'] = $ret['code'].'--'.$ret['message'];
        return $ret;
    }

    /**
     * 根据ID获取承租方用户信息
     * @param string $lesseeId
     * @return array
     */
    public function getUserInfoByProjectId($lesseeId)
    {
        $ret = $this->_eapiModel->getUserInfoByProjectId($lesseeId, 'lease');
        $ret['message'] = $ret['code'].'--'.$ret['message'];
        return $ret;
    }
    /**
     * 根据手机号获取用户信息
     * @param string $mobile
     * @return array
     */
    public function getUserByMobile($mobile)
    {
        $ret = $this->_eapiModel->getUserByMobile($mobile);
        $ret['message'] = $ret['code'].'--'.$ret['message'];
        return $ret;
    }

    // 获取公司的用户列表
    public function getCompanyUserList($page, $num, $companyId, $keyword = '')
    {
        $ret = $this->_eapiModel->getCompany($page, $num, $companyId, '3', $keyword);
        $ret['message'] = $ret['code'] . '--' . $ret['message'];
        return $ret;
    }

    public function getCompanyByCompanyId($companyId){
        $ret = $this->_eapiModel->getCompanyInfo($companyId);
        $ret['message'] = $ret['code'] . '--' . $ret['message'];
        return $ret;
    }
}
