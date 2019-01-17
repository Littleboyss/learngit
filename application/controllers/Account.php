<?php
use \Firebase\JWT\JWT;
class AccountController extends ApiController
{
    protected static $_jwtKey = 'this is jwt token key';

    public function loginAction()
    {
        $username = $this->_request->getPost('username');
        $password = $this->_request->getPost('password');
        //验证账号密码
        if ($username == '' || $password == '') {
            $this->error('用户名或密码为空');
        }
        $loginUser = (new UserModel())->login($username, md5($password));
        if ($loginUser) {
            $payload = [
                "iss" => "http://wujigang.cn", // jwt的签发者
                "iat" => time(), // 签发时间
                "exp" => time() + (new Ext_Date)::DAY_SECONDS * 30, // 30天后过期
            ];
            $payload['loginUser'] = $loginUser;
            $jwt                  = JWT::encode($payload, self::$_jwtKey);
            $data                 = [
                'token' => $jwt,
            ];

            $this->success('登录成功', $data);
        } else {
            //登录失败
            $this->error($loginRes['message']);
        }
    }

    // 获取用户信息
    public function infoAction()
    {
        $loginUser            = $this->_loginUser;
        $loginUser['role_id'] = 0;
        $loginUser['perms']   = [];
        // 首先根据用户ID获取用户角色
        $roleLinkModel = new UserRoleLinkModel();
        $roleList      = $roleLinkModel->findAll([
            'user_id' => $loginUser['id'],
        ]);
        if ($roleList) {
            ['name' => $loginUser['role_name']] = (new RoleModel())->load($roleList[0]['role_id'], 'name');
            $roleIds                            = array_column($roleList, 'role_id');
            $rolePermissionModel                = new RoleModel();
            $menuList                           = $rolePermissionModel->findAll([
                'role_id' => ['in', "(" . implode(',', $roleIds) . ")"],
            ]);
            if ($menuList) {
                $menuIds        = array_column($menuList, 'menu_id');
                $AuthModel      = new AuthModel();
                $permissionMenu = $AuthModel->findAll([
                    'id' => ['IN', "(" . implode(',', $menuIds) . ")"],
                    'type' => 1
                ]);
                if ($permissionMenu) {
                    $loginUser['perms'] = array_column($permissionMenu, 'url');
                }
            }
        }
        $this->success('获取用户数据成功', $loginUser);
    }

    public function logoutAction()
    {
        return $this->success('退出成功');
    }

}
