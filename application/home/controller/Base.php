<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 9:40
 */

namespace app\home\controller;

use think\Controller;


class Base extends Controller
{

    public function ajaxReturn($data){
        exit(json_encode($data));
    }
}