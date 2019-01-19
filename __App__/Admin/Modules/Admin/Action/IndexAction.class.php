<?php

/**
 * 首页
 * @author 周应华
 */

class IndexAction extends AdminAction
{
	/**
	 * 后台框架页
	 */
	public function index()
	{
		$this->assign('mainMenus', get_main_menus());

		$adminModel = D('AdminAdmin');
		$admin = session('admin');
		$admin = $adminModel->where(array('username' => $admin['username']))->find();
		$this->assign('admin', $admin);

		// 权限
		$rights = explode(',', $admin['rights']);
		$modules = array();
		$modulesConf = C('MODULES');
		if (in_array('Admin', $rights)) {
			$modules = $modulesConf;
		} else {
			foreach ($rights as $v) {
				if (isset($modulesConf[$v])) {
					$modules[$v] = $modulesConf[$v];
				}
			}
		}
		$this->assign('modules', $modules);
		$this->assign('moduleName', $modules[session('module')]);

		$this->display();
	}

	/**
	 * 首页
	 */
	public function main()
	{
		$adminModel = D('AdminAdmin');
		$admin = session('admin');
		$adminDetail = $adminModel->where(array('username' => $admin['username']))->find();
		$modules = C('MODULES');
		$adminInfo = array(
            'nickname' => $admin['nickname'],
            'logintime' => date('Y-m-d H:i:s'),
            'loginip' => $adminDetail['loginip'],
            'logintimes' => $adminDetail['logintimes']
		);
		foreach ($admin['rights'] as $v) {
			$adminInfo['rights'][] = $modules[$v];
		}
		$adminInfo['rights'] = implode('，', $adminInfo['rights']);
		$adminInfo['ip'] = get_client_ip();
		$this->assign('adminInfo', $adminInfo);

		$sysInfo = array();
		$sysInfo['mysqlVer'] = mysql_get_server_info();
		$this->assign('sysInfo', $sysInfo);

		$this->display();
	}

	public function phpinfo()
	{
		phpinfo();
	}

	/**
	 * 子菜单
	 * @param int $id
	 */
	public function submenus($id)
	{
		$id = (int) $id;
		$mainMenus = get_main_menus();
		$this->assign('submenus', $mainMenus[$id]['sub']);
		$this->display();
	}

	/**
	 * 利用js的setInterval函数保持session会话
	 */
	public function sessionLife()
	{
	}

	/**
	 * 切换后台模块
	 */
	public function changeModule()
	{
		$module = $this->_get('module');
		$admin = session('admin');
		if (in_array($module, $admin['rights']) || in_array('Admin', $admin['rights'])) {
			session('module', $module);
		}

		// 更新loginmodule
		$adminModel = D('AdminAdmin');
		$adminModel->where(array('username' => $admin['username']))->save(array(
            'loginmodule' => $module
		));

		$this->redirect('Index/index');
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $path
	 */
	public function upload(){
		$gtype = I('get.type');
		$pcid = I('get.pcid');
		$type = empty($gtype)?'':trim($gtype);
		$path='../Static/images/'.strtolower(session('module')).'/'.$type.'/'.date("Ym",time()).'/'.$pcid.'_';
		if(!is_dir($path)){
			mkdir($path,0777,true);
		}
		$url=C('IMG_DOMAIN').substr($path,9,strlen($path));
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();
		$upload->maxSize  = 16*1024*1024 ;
		$upload->allowExts  = array('jpg', 'gif','png','jpeg','bmp','zip','rar','mp3');
		$upload->saveRule=uniqid().mt_rand(1000, 9999);

		/*
		 *$width='400';
		 $height='200';
		 $upload->thumb=true;
		 $upload->thumbMaxWidth='400';
		 $upload->thumbMaxHeight ='200';
		 $upload->thumbPrefix="";
		 $upload->thumbSuffix='';
		 */
		$upload->savePath =  $path;// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
			echo  json_encode(array('error'=>1,'message'=>$this->error($upload->getErrorMsg())));
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			echo  json_encode(array('error'=>0,'url'=>$url.$info[0]['savename']));
		}
	}
	/**
	 * 
	 * 根据URL 抓取图片到本地
	 */
	
	public function getImgHost(){
		$imgurl=$this->_request("imgurl");
		$searchstr=strtolower(C('IMG_DOMAIN'));//本地域名比对
		if(strstr(strtolower($imgurl),$searchstr)==true){  //如果为真说是本地图片
			$this->ajaxReturn(array('error'=>0,'msg'=>$imgurl));
		}
		$ext=strrchr($imgurl, ".");
		if($ext!=".png"&&$ext!=".gif"&&$ext!=".jpg"&&$ext!=".jpeg"){
			$this->ajaxReturn(array('error'=>0,'msg'=>$imgurl));
		}
		$path='../../Img/'.strtolower(session('module')).'/'.date("Ym",time()).'/';
		if(!is_dir($path)){
			mkdir($path,0777,true);
		}
		$imgsource=file_get_contents($imgurl);
		$filename=uniqid().mt_rand(1000, 9999).$ext;
		$fp=fopen($path.$filename,"a");
		fwrite($fp, $imgsource);
		fclose($fp);
		$hosturl=C('IMG_DOMAIN').substr($path,9,strlen($path)).$filename;
		$this->ajaxReturn(array('error'=>0,'msg'=>$hosturl));
    }
    
    
}
