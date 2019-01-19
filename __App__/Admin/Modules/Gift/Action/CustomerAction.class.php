<?php
/**
 * @author chengy 2017.3.27
 */

class CustomerAction extends AdminAction{
    // 反馈列表
    public function index(){
        $ShopCustomerCare = M('ShopCustomerCare');
        import('ORG.Util.Page');
        $status = 2;
        if (I('status')) {
            $status = I('status');
            if ($status == 4) {
                $where = '1 = 1';
            }else{
                $where ="status = $status";
            }
        }else{
            $where ="status = $status";
        }
        $count = $ShopCustomerCare->where($where)->count();
        $page = new Page($count, 10);
        $data = $ShopCustomerCare->field('t1.*,t2.username')->join('as t1 left join '.c('DB_PREFIX').'user_user as t2 on t1.user_id=t2.id')->limit($page->firstRow . ',' . $page->listRows)->where($where)->select();
        $show = $page->show();
        $this->assign('data',$data);
        $this->assign('status',$status);
        $this->assign('show',$show);
        $this->display();
    }
    // 处理
    public function dealwith(){
        if (IS_POST) {
            $data['id'] = $_POST['id'];
            $data['request'] = $_POST['request'];
            if (!empty($data['request'])) {
                $data['admin_name'] = $_SESSION['admin']['nickname'];
                $data['status'] = 1;
                $data['deltime'] = time();
                M('ShopCustomerCare')->save($data);
                $notice['notice']=$data['request'];
                $notice['user_id']=$_POST['uid'];
                $notice['username']=$_POST['username'];
                $notice['parentname']='SYSTEM';
                $notice['classname']='系统消息';
                $notice['addtime']=time();
                // 修改订单状态
                $res = M('UserNotice')->add($notice);
                if ($res) {
                    $this->success ('修改成功',U('index',array('id'=>$data['id'])));
                }else{
                    $this->error ('修改失败',U('dealwith',array('id'=>$data['id'])));
                }
            }else{
                $this->error ('修改失败,参数异常',U('dealwith',array('id'=>$data['id'])));
            }

        }else{
            $order_id = isset ( $_GET ['id'] ) ? ( int ) $_GET ['id'] : 0;
            $map['t1.id'] = $order_id;
            $res = M('ShopCustomerCare')->field('t1.*,t2.username')->join('as t1 left join '.c('DB_PREFIX').'user_user as t2 on t1.user_id=t2.id')->where($map)->find();
            $this->assign ('data', $res );
            $this->display();   
        }
    }
	
}