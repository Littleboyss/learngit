<?php
/**
 * @author chengy 2017.3.27
 */

class AwardAction extends AdminAction{
	//兑换码奖品列表
	public function redcode_list(){
		$redcode = M('AwardRedcodeBonus');
        import('ORG.Util.Page');
        $where = '1 = 1';
        $type = I('type');
        if ($type) {
            $where .=" and type = $type";
        }
        $count = $redcode->where($where)->count();
        $page = new Page($count, 10);
		$data = $redcode->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $show = $page->show();
        $this->assign('data',$data);
        $this->assign('show',$show);
		$this->assign('class_id',$_POST['class_id']);
		$this->display();
	}
    // 兑换码奖品修改
    public function redcode_edit(){
        $redcode = M('AwardRedcodeBonus');
        if (IS_POST) {
            $data = $redcode->create();
            $res = $redcode->save();
            if ($res) {
                // 同时修改激活码表
                $datas['type']=$data['type']; 
                $datas['prize']=$data['nums']; 
                M('AwardRedcode')->where('bonus_id = '.$data['id'])->save($datas);
                $this->success('修改成功',U('redcode_list'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = I('get.id');
            $data = $redcode->where('id = '.$id)->find();
            //dump($data);
            $this->assign('data',$data);
            $this->display();
        }
    }
    // 兑换码奖品添加
    public function redcode_add(){
        $redcode = M('AwardRedcodeBonus');
        if (IS_POST) {
            $data = $redcode->create();
            $data['addtime'] = time();
            $res = $redcode->add($data);
            if ($res) {
                $this->success('添加成功',U('redcode_list'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->display();
        }
    }
    // 兑换奖品删除
    public function redcode_del(){
        $id = (int)I('id');
        if (!empty($id) && $id > 0) {
            $res = M('AwardRedcodeBonus')->where('id = '.$id)->delete();
            $res1 = M('AwardRedcode')->where('bonus_id = '.$id)->delete();
            if ($res ) {
                $this->success('删除成功',U('redcode_list'));
            }else{
                $this->error('删除失败');
            }
        }
    }
    public function delcard(){
        $admin = session('admin');
        // print_r($admin);die;
        $id = I('get.id');
        if($admin['username'] != 'admin'){
            $this->error('你没有权限',U('view',array('id'=>$id)));
        }
        if(!is_numeric($id)){
            $this->error('错误');
        }
        $AwardRedcode = M('AwardRedcode');
        $Map['bonus_id'] = $id;
        $rs = $AwardRedcode->where($Map)->delete();
        if ($rs) {
            $res = M('AwardRedcodeBonus')->where('id ='.$id)->setField('has_nums',0);
        }
        $this->redirect(U('view',array('id'=>$id)));
    }
    
    // 兑换码查看
    public function view(){
        import('ORG.Util.Page');
        $type = I('request.type');
        $RechargeCard = M('AwardRedcode');
        $id = I('get.id');
        $Map['bonus_id'] = $id;
        $count = $RechargeCard->where($Map)->count();
        $page = new Page($count, 10);
        $show = $page->show();
        $rs = $RechargeCard->where($Map)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('gift_id',$id);
        $this->assign('rs',$rs);
        $this->assign('show',$show);
        $this->display();
    }
	// 转盘列表
	public function turnplate_list(){
		$Award = M('AwardTurnplate');
        $where = '1 =  1 ';
        if (!empty($_POST['class_id']) && in_array($_POST['class_id'], array(1,2,3))) {
            $where .= ' and t1.class_id ='.$_POST['class_id'];
        }
        $data = $Award->field('t1.*,t2.name,t2.type,t2.nums')->join('as t1 left join '.c('DB_PREFIX').'award_turnplate_bonus as t2 on t1.bonus_id = t2.id')->where($where)->select();
        $this->assign('data',$data);
        $this->assign('class_id',$_POST['class_id']);
		$this->display();
	}
    // 转盘信息修改
    public function turnplate_edit(){
        if (IS_POST) {
            $data['id'] = $_POST['id'];
            $data['chance'] = $_POST['chance'];
            $data['level'] = $_POST['level'];
            $data['class_id'] = $_POST['class_id'];
            $data['level'] = $_POST['level'];
            $res1 = M('AwardTurnplate')->save($data);
            $datas['id'] = $_POST['bonus_id'];
            $datas['name'] = $_POST['name'];
            $datas['type'] = $_POST['type'];
            $datas['nums'] = $_POST['nums'];
            if ($datas['type'] == 4 && !empty($_POST['goods_id'])) {
                $datas['goods_id'] = $_POST['goods_id'];
            }
            $res2 = M('AwardTurnplateBonus')->save($datas);
            if (!$res1  && !$res2) {
                $this->error('修改失败',U('turnplate_edit',array('id'=>$data['id'])));
            }else{
                $this->success('修改成功',U('turnplate_list'));
            }
        }else{
            $id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
            $Award = M('AwardTurnplate');
            $data = $Award->field('t1.*,t2.name,t2.type,t2.nums,t2.goods_id')->join('as t1 left join '.c('DB_PREFIX').'award_turnplate_bonus as t2 on t1.bonus_id = t2.id')->where('t1.id = '.$id)->find();
            $this->assign('data',$data);
            $this->display();
        }
    }
    // 转盘添加
    public function turnplate_add(){
        if (IS_POST) {
            $bonus = M('AwardTurnplateBonus');
            $award = M('AwardTurnplate');
            $datas = $bonus->create();
            $res1 = $bonus->add($datas);
            if ($res1) {
                $data = $award->create();
                $data['bonus_id'] = $res1;
                $res2 = $award->add($data);
            }
            if ($res1  && $res2) {
                $this->success('添加成功',U('turnplate_list'));
            }else{
                $this->error('添加失败',U('turnplate_add'));
            }
        }else{
            $this->display();
        }
    }
    public function turnplate_del(){
        $id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
        $bonus_id = isset ( $_GET ['bonus_id'] ) ? ( int ) $_GET ['bonus_id'] : 0;
        $Award = M('AwardTurnplate');
        $AwardBonus = M('AwardTurnplateBonus');
        $res1 = $AwardBonus->where('id = '.$bonus_id)->delete();
        $res2 = $Award->where('id = '.$id)->delete();
        if ($res1  && $res2) {
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
	// 批量生成兑换码并插入数据库 
    public function create_redcode(){
        $redcode = M('AwardRedcode');
        if (IS_POST) {
            $nums = I('post.nums'); // 要生成多少个兑换码
            $word = I('post.word'); // 以此字符开头
            $bonus_id = I('get.id');// 奖品ID
            $info = M('AwardRedcodeBonus')->where('id = '.$bonus_id)->find();
            $type = $info['type'];
            $prize = $info['nums'];
            $data['addtime'] = time();
            if($nums >= 1 && $nums < 50000){
                $data['nums'] = $nums;
            }
            if($word){
                $data['word'] = $word;
            }
            if(in_array($type ,array(1,2,3))){
                $data['type'] = $type;
            }
            if($prize>0){
                $data['prize'] = $prize;
            }
            if($bonus_id>0){
                $data['bonus_id'] = $bonus_id;
            }
            $j = 0;
            for($i=1 ; $i <= $nums ; $i++){
                $data['codes'] = substr($word . mt_rand(1,100000000).$i .mt_rand(1,100000000).mt_rand(1,100000000).mt_rand(1,100000000),0,32);
                $res = $redcode->add($data);
                if (!$res) {
                    $j++;
                }
            }
            M('AwardRedcodeBonus')->where('id = '.$bonus_id)->setInc('has_nums',$nums-$j);
            $this->success('生成完成，'.$j.'个失败',U('redcode_list')); // 返回生成的兑换码的ID数组
        }else{
            $id = I('id');
            $this->assign('id',$id);
            $this->display();
        }
    }
}