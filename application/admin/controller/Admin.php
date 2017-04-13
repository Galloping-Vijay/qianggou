<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/11
 * Time: 11:14
 */

namespace app\admin\controller;


use app\admin\model\Access;
use app\admin\model\AdminRole;
use think\Db;
use think\Verify;
use think\paginator;
use think\session;
use app\admin\model\Admin as AdminModel;

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
     * 管理员列表
     * @return mixed
     */
    public function admin_list(){
        $admin= new AdminModel();
        $admin_list = $admin->paginate(10);
        $page = $admin_list->render();

        $this->assign('admin_list',$admin_list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    /**
     * 管理员详情
     * @return mixed
     */
    public function admin_info(){
        $admin = new AdminModel();
        $role = new AdminRole();
        $admin_id = input('admin_id/d',0);
        if($admin_id){
            $info =$admin->where('admin_id',$admin_id)->find();
        }else{
            $info['admin_id']="";
            $info['user_name']="";
            $info['password']="";
        }
        $act = empty($admin_id) ? 'add' : 'edit';
        $admin_role = $role->select();

        $this->assign('act',$act);
        $this->assign('info',$info);
       $this->assign('admin_role',$admin_role);
        return $this->fetch();
    }

    /**
     * 管理员增删改操作
     * */
    public function adminHandle(){
        $admin = new AdminModel();
        $data = input('post.');
        if(empty($data['password'])){
            unset($data['password']);
        }else{
            $data['password'] = encrypt($data['password']);
        }
        if($data['act'] == 'add'){//新增管理员
            unset($data['admin_id']);
            unset($data['act']);

            $data['add_time'] = time();
            if($admin->where("user_name", $data['user_name'])->count()){
                $this->error("此用户名已被注册，请更换",url('Admin/Admin/admin_info'));
            }else{
                $r = $admin->save($data);
            }
        }elseif ($data['act'] == 'edit'){//修改管理员信息
            unset($data['act']);
            $r = $admin->where('admin_id',$data['admin_id'])->update($data);

        }elseif($data['act'] == 'del' && $data['admin_id']>1){//删除管理员
            $r = $admin->where('admin_id', $data['admin_id'])->delete();
            exit(json_encode(array('msg'=>'删除成功!','status'=>1)));
        }
        if($r){
            $this->success("操作成功",url('Admin/Admin/admin_list'));
        }else{
            $this->error("操作失败",url('Admin/Admin/admin_info'));
        }
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

    /**
     * 角色列表
     * @return mixed
     */
    public function role(){
        $role = new AdminRole();
        $role_list = $role->paginate(10);
        $page = $role_list->render();

        $this->assign('list',$role_list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    /**
     * 角色详情(添加修改)
     * @return mixed
     */
    public function role_info(){
        $admin_role = new AdminRole();
        $access = new Access();

        $role_id = input('role_id/d');
        $detail = array();
        if($role_id){
            //获取角色权限id
            $detail = $admin_role->where("role_id",$role_id)->find()->toArray();
            $detail['act_list'] = explode(',', $detail['act_list']);
            $this->assign('detail',$detail);
        }
        //权限菜单
        $right = $access->where('pid','>',0)->order('id')->select();

        $modules=[];
        foreach ($right as $val){
            if(!empty($detail)){
                $val['enable'] = in_array($val['id'], $detail['act_list']);
            }
            $modules[$val['menu_group']][] = $val;
        }
        //权限组
        $group = $access->where('pid',0)->column('name','id');
        //dump($group);
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

    /**
     * 删除角色
     */
    public function role_del(){
        $AdminModel = new AdminModel();
        $role = new AdminRole();
        $role_id = input('post.role_id/d');
        $admin = $AdminModel->where('role_id',$role_id)->find();
        if($admin){
            exit(json_encode(0));
        }else{
            $d = $role->where("role_id", $role_id)->delete();
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


}