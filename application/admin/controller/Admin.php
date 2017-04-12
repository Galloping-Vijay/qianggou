<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/11
 * Time: 11:14
 */

namespace app\admin\controller;


use think\Db;
use think\Verify;
use think\paginator;
use think\session;

class Admin extends Base
{
    public function index(){
        $list = array();
        $keywords = input('keywords/s');
        if(empty($keywords)){
            $res = Db::name('admin')->select();
        }else{
            $res = DB::name('admin')->where('user_name','like','%'.$keywords.'%')->order('admin_id')->select();
        }
        $role = Db::name('admin_role')->column('role_id,role_name');
        if($res && $role){
            foreach ($res as $val){
                $val['role'] =  $role[$val['role_id']];
                $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
                $list[] = $val;
            }
        }
        $this->assign('list',$list);
        //return $this->fetch();
    }

    /**
     * 修改管理员密码
     * @return \think\mixed
     */
    public function modify_pwd(){
        $admin_id = input('admin_id/d',0);
        $oldPwd = input('old_pw/s');
        $newPwd = input('new_pw/s');
        $new2Pwd = input('new_pw2/s');

        if($admin_id){
            $info = Db::name('admin')->where("admin_id", $admin_id)->find();
            $info['password'] =  "";
            $this->assign('info',$info);
        }

        if(IS_POST){
            //修改密码
            $enOldPwd = encrypt($oldPwd);
            $enNewPwd = encrypt($newPwd);
            $admin = Db::name('admin')->where('admin_id' , $admin_id)->find();
            if(!$admin || $admin['password'] != $enOldPwd){
                $json = array(
                    'status'=>-1,
                    'msg'=>'旧密码不正确'
                );
            }else if($newPwd != $new2Pwd){
                $json = array(
                    'status'=>-1,
                    'msg'=>'两次密码不一致'
                );
            }else{
                $row = Db::name('admin')->where('admin_id' , $admin_id)->insert(array('password' => $enNewPwd));
                if($row){
                    $json = array(
                        'status'=>1,
                        'msg'=>'修改成功'
                    );
                }else{
                    $json = array(
                        'status'=>-1,
                        'msg'=>'修改失败'
                    );
                }
            }
            exit(json_encode($json));
        }
        return $this->fetch();
    }
    public function admin_info(){
        $admin_id = input('get.admin_id/d',0);
        if($admin_id){
            $info = Db::name('admin')->where("admin_id", $admin_id)->find();
            $info['password'] =  "";
            $this->assign('info',$info);
        }
        $act = empty($admin_id) ? 'add' : 'edit';
        $this->assign('act',$act);
        $role = Db::name('admin_role')->where('1=1')->select();
        $this->assign('role',$role);
        return $this->fetch();
    }

    public function adminHandle(){
        $data = input('post.');
        if(empty($data['password'])){
            unset($data['password']);
        }else{
            $data['password'] = encrypt($data['password']);
        }
        if($data['act'] == 'add'){
            unset($data['admin_id']);
            $data['add_time'] = time();
            if(Db::name('admin')->where("user_name", $data['user_name'])->count()){
                $this->error("此用户名已被注册，请更换",U('Admin/Admin/admin_info'));
            }else{
                $r = Db::name('admin')->insert($data);
            }
        }

        if($data['act'] == 'edit'){
            $r = Db::name('admin')->where('admin_id', $data['admin_id'])->insert($data);
        }

        if($data['act'] == 'del' && $data['admin_id']>1){
            $r = db::name('admin')->where('admin_id', $data['admin_id'])->delete();
            exit(json_encode(1));
        }

        if($r){
            $this->success("操作成功",url('Admin/Admin/index'));
        }else{
            $this->error("操作失败",url('Admin/Admin/index'));
        }
    }

    /*
     * 管理员登陆
     */
    public function login(){

        if(session('?admin_id') && session('admin_id')>0){
            $this->error("您已登录",Url('admin/index/index'));
        }

        if(IS_POST){
            $verify = new Verify();
            if (!$verify->check(input('vertify'), "admin_login")) {
                exit(json_encode(array('status'=>0,'msg'=>'验证码错误')));
            }

            $condition['user_name'] = input('username/s');
            $condition['password'] = input('password/s');
            if(!empty($condition['user_name']) && !empty($condition['password'])){
                //加密
                $condition['password'] = encrypt($condition['password']);
                $admin_info = Db::name('admin')->join(PREFIX.'admin_role', PREFIX.'admin.role_id='.PREFIX.'admin_role.role_id','INNER')->where($condition)->find();
                if(is_array($admin_info)){
                    session('admin_id',$admin_info['admin_id']);
                    session('act_list',$admin_info['act_list']);
                    Db::name('admin')->where("admin_id = ".$admin_info['admin_id'])->update(array('last_login'=>time()));
                    session('last_login_time',$admin_info['last_login']);

                    adminLog('后台登录');
                    $url = session('from_url') ? session('from_url') : url('Admin/Index/index');
                    exit(json_encode(array('status'=>1,'url'=>$url)));
                }else{
                    exit(json_encode(array('status'=>0,'msg'=>'账号密码不正确')));
                }
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'请填写账号密码')));
            }
        }

        return $this->fetch();
    }
    /**
     * 退出登陆
     */
    public function logout(){
        session_unset();
        session_destroy();
        session::clear();
        $this->success("退出成功",url('Admin/Admin/login'));
    }

    /**
     * 验证码获取
     */
    public function vertify()
    {
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => false,
            'reset' => false
        );
        $Verify = new Verify($config);
        $Verify->entry("admin_login");
    }
    //角色管理
    public function role(){
        /*$list = Db::name('admin_role')->order('role_id desc')->select();
        $this->assign('list',$list);
        return $this->fetch();*/
    }

    public function role_info(){
        $role_id = input('get.role_id/d');
        $detail = array();
        if($role_id){
            $detail = Db::name('admin_role')->where("role_id",$role_id)->find();
            $detail['act_list'] = explode(',', $detail['act_list']);
            $this->assign('detail',$detail);
        }
        $right = Db::name('system_menu')->order('id')->select();
        foreach ($right as $val){
            if(!empty($detail)){
                $val['enable'] = in_array($val['id'], $detail['act_list']);
            }
            $modules[$val['group']][] = $val;
        }
        //权限组
        $group = array(
            'order'=>'订单管理','goods'=>'商品管理','促销管理'=>'商品中心','article'=>'文章管理',
            'operation'=>'运营管理','system'=>'系统功能','access'=>'权限管理'
        );
        $this->assign('group',$group);
        $this->assign('modules',$modules);
        return $this->fetch();
    }

    public function roleSave(){
        $data = input('post.');
        $res = $data['data'];
        $res['act_list'] = is_array($data['right']) ? implode(',', $data['right']) : '';
        if(empty($data['role_id'])){
            $r = Db::name('admin_role')->insert($res);
        }else{
            $r = Db::name('admin_role')->where('role_id', $data['role_id'])->insert($res);
        }
        if($r){
            adminLog('管理角色');
            $this->success("操作成功!",url('Admin/Admin/role_info',array('role_id'=>$data['role_id'])));
        }else{
            $this->success("操作失败!",url('Admin/Admin/role'));
        }
    }

    public function roleDel(){
        $role_id = input('post.role_id/d');
        $admin = Db::name('admin')->where('role_id',$role_id)->find();
        if($admin){
            exit(json_encode("请先清空所属该角色的管理员"));
        }else{
            $d = Db::name('admin_role')->where("role_id", $role_id)->delete();
            if($d){
                exit(json_encode(1));
            }else{
                exit(json_encode("删除失败"));
            }
        }
    }
    //管理员日志
    public function log(){
        /*$p = input('p/d',1);
        $logs = DB::name('admin_log')->alias('l')->join('__ADMIN__ a','a.admin_id =l.admin_id')->order('log_time DESC')->page($p.',20')->select();
        $this->assign('list',$logs);
        $count = DB::name('admin_log')->where('1=1')->count();
        $Page = DB::name('admin_log')->where('1=1')->paginate(20,$count);
        $show = $Page->render();
        $this->assign('pager',$Page);
        $this->assign('page',$show);
        return $this->fetch();*/
    }


    /**
     * 供应商列表
     */
    public function supplier()
    {
        $supplier_count = DB::name('suppliers')->count();
        $page = DB::name('suppliers')->where('1=1')->paginate(20,$supplier_count);
        $show = $page->render();
        $supplier_list = DB::name('suppliers')
            ->alias('s')
            ->field('s.*,a.admin_id,a.user_name')
            ->join('__ADMIN__ a','a.suppliers_id = s.suppliers_id','LEFT')
           /* ->limit($page->$currentPage, $page->listRows)*/
            ->select();
        $this->assign('list', $supplier_list);
        $this->assign('page', $show);
        return $this->fetch();
    }

    /**
     * 供应商资料
     */
    public function supplier_info()
    {
        $suppliers_id = input('get.suppliers_id/d', 0);
        if ($suppliers_id) {
            $info = DB::name('suppliers')
                ->alias('s')
                ->field('s.*,a.admin_id,a.user_name')
                ->join('__ADMIN__ a','a.suppliers_id = s.suppliers_id','LEFT')
                ->where(array('s.suppliers_id' => $suppliers_id))
                ->find();
            $this->assign('info', $info);
        }
        $act = empty($suppliers_id) ? 'add' : 'edit';
        $this->assign('act', $act);
        $admin = Db::name('admin')->field('admin_id,user_name')->where('1=1')->select();
        $this->assign('admin', $admin);
        return $this->fetch();
    }

    /**
     * 供应商增删改
     */
    public function supplierHandle()
    {
        $data = input('post.');
        $suppliers_model = Db::name('suppliers');
        //增
        if ($data['act'] == 'add') {
            unset($data['suppliers_id']);
            $count = $suppliers_model->where("suppliers_name", $data['suppliers_name'])->count();
            if ($count) {
                $this->error("此供应商名称已被注册，请更换", url('Admin/Admin/supplier_info'));
            } else {
                $r = $suppliers_model->insertGetId($data);
                if (!empty($data['admin_id'])) {
                    $admin_data['suppliers_id'] = $r;
                    Db::name('admin')->where(array('suppliers_id' => $admin_data['suppliers_id']))->insert(array('suppliers_id' => 0));
                    Db::name('admin')->where(array('admin_id' => $data['admin_id']))->insert($admin_data);
                }
            }
        }
        //改
        if ($data['act'] == 'edit' && $data['suppliers_id'] > 0) {
            $r = $suppliers_model->where('suppliers_id',$data['suppliers_id'])->insert($data);
            if (!empty($data['admin_id'])) {
                $admin_data['suppliers_id'] = $data['suppliers_id'];
                Db::name('admin')->where(array('suppliers_id' => $admin_data['suppliers_id']))->insert(array('suppliers_id' => 0));
                Db::name('admin')->where(array('admin_id' => $data['admin_id']))->insert($admin_data);
            }
        }
        //删
        if ($data['act'] == 'del' && $data['suppliers_id'] > 0) {
            $r = $suppliers_model->where('suppliers_id', $data['suppliers_id'])->delete();
            Db::name('admin')->where(array('suppliers_id' => $data['suppliers_id']))->insert(array('suppliers_id' => 0));
        }

        if ($r !== false) {
            $this->success("操作成功", url('Admin/Admin/supplier'));
        } else {
            $this->error("操作失败", url('Admin/Admin/supplier'));
        }
    }

    //管理员列表
    public function admin_list(){

    }

}