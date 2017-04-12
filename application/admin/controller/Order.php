<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/12
 * Time: 9:40
 */

namespace app\admin\controller;


class Order extends Base
{
    public function index(){
        echo '订单列表';
    }
    public function export_order(){
        echo '订单导出';
    }
    public function send_order(){
        echo '订单发货';
    }

}