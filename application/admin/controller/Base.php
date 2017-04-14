<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 17:34
 */

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class Base extends Controller
{
    function __construct(Request $request = null)
    {
        Session::start();
        parent::__construct($request);
    }

    /*
     * 初始化操作
     */

    public function _initialize()
    {
        $this->request->isAjax() ? define('IS_AJAX',true) : define('IS_AJAX',false);  //
        ($this->request->method() == 'GET') ? define('IS_GET',true) : define('IS_GET',false);  //
        ($this->request->method() == 'POST') ? define('IS_POST',true) : define('IS_POST',false);  //

        define('MODULE_NAME',$this->request->module());  // 当前模块名称是
        define('CONTROLLER_NAME',$this->request->controller()); // 当前控制器名称
        define('ACTION_NAME',$this->request->action()); // 当前操作名称是
        define('PREFIX',config('database.prefix')); // 数据库表前缀

       $this->assign('action',ACTION_NAME);
        //过滤不需要登陆的行为
        if(in_array(ACTION_NAME,array('login','logout','vertify')) || in_array(CONTROLLER_NAME,array('Ueditor','Uploadify'))){
            //return;
        }else{
            if(session('admin_id') > 0 ){
                $this->check_priv();//检查管理员菜单操作权限
            }else{
                $this->error('请先登陆',url('Admin/Admin/login'),1);
            }
        }
        $this->public_assign();
    }

    /**
     * 保存公告变量到 smarty中 比如 导航
     */
    public function public_assign()
    {
        $tpshop_config = array();
        $tp_config = Db::name('config')->cache(true)->select();
        foreach($tp_config as $k => $v)
        {
            $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
        }
        $this->assign('tpshop_config', $tpshop_config);
    }

    /**
     * 检测权限
     * @return bool
     */
    public function check_priv()
    {
        $ctl = CONTROLLER_NAME;
        $act = ACTION_NAME;
        $act_list = session('act_list');
        //无需验证的操作
        $uneed_check = array('login','logout','vertifyHandle','vertify','imageUp','upload','login_task');
        if($ctl == 'Index' || $act_list == 'all'){
            //后台首页控制器无需验证,超级管理员无需验证
            return true;
        }elseif(strpos($act,'ajax') || in_array($act,$uneed_check)){
            //所有ajax请求不需要验证权限
            return true;
        }else{
            $right = Db::name('access')->where("id", "in", $act_list)/*->cache(true)*/->column('action','module');
            //检查是否拥有此操作权限
            if(!array_intersect_assoc([$ctl=>$act], $right)){
                $this->error("您没有操作权限,请联系超级管理员分配权限",url('admin/index/home'));
            }
        }
    }

    public function ajaxReturn($data,$type = 'json'){
        exit(json_encode($data));
    }
}