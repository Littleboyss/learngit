<?php

use \Firebase\JWT\JWT;

class ApiController extends \Libs\Controller\Base
{
    protected static $_jwtKey = 'this is jwt token key';
    
    // 当前登录用户信息
    protected $_loginUser = null;

    public function init()
    {
        parent::init();
        $jwt = $this->_request->getServer('HTTP_AUTHORIZATION'); // 服务器设置,获取header头信息需要加http前缀，并大写
        if ($jwt) {
            try {
                $jwtInfo = JWT::decode($jwt, self::$_jwtKey, ['HS256']);
                if ($jwtInfo) {
                    $this->_loginUser = (array) $jwtInfo->loginUser;
                }
            } catch (\Throwable $th) {
                return $this->error($th->getMessage());
            }
        }
    }

    protected function getPostJson()
    {
        return json_decode($this->_request->getRaw(), true);
    }
}
