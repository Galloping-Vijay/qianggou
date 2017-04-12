<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 17:35
 */

namespace app\admin\controller;



use app\admin\model\Access;
use app\admin\model\Order;

class Index extends Base
{
    public function index(){
        $act_list = session('cat_list');
        //$menu_list =getMenuList($act_list);
        //$this->assign('menu_list',$menu_list);
        $admin_info = getAdminInfo(session('admin_id'));
        //$order =new Order();
        //$order_amount = $order->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();
        //$this->assign('order_amount',$order_amount);
        $this->assign('admin_info',$admin_info);
        $this->assign('menu',getAllMenu());
        return $this->fetch();
    }

    public function home(){
        //echo getAllMenu();
        echo '控制面板';
    }
}