<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/12
 * Time: 17:37
 */

namespace app\admin\controller;


use app\admin\model\Access;

class System extends Base
{
    //网站设置
    public function index(){

    }
    //推荐位
    public function recommended_area(){

    }
    //权限列表
    public function role_list(){
        $role =new Access();
        $role_list = $role->paginate(10);
        $page =$role_list->render();

        $this->assign('page',$page);
        $this->assign('list',$role_list);
        return $this->fetch();
    }

}