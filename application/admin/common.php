<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/3/31
 * Time: 17:33
 */
use think\Db;
use App\Admin\Model\Access;
/**
 * 管理员操作记录
 * @param $log_info
 */
function  adminLog($log_info){
    $add['log_time'] = time();
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    $add['log_url'] = request()->baseUrl() ;
    Db::name('admin_log')->insert($add);
}

function getAdminInfo($admin_id){
    return Db::name('admin')->where('admin_id',$admin_id)->find();
}

/**
 * 导出excel
 * @param $strTable
 * @param $filename
 */
function downloadExcel($strTable,$filename)
{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
    header('Expires:0');
    header('Pragma:public');
    echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 格式化字节大小
 * @param $size
 * @param string $delimiter
 * @return string
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取所有菜单
 * @return string
 */
function getAllMenu(){
    $menu = Db::name('Access');
    //获取最大组值
    $max_group = $menu->max('menu_group');

    //获取各个分组
    $menu_list=array();
    for ($i=1;$i<=$max_group;$i++){
        $menu_group = $menu->where('menu_group',$i)->select();
        $childMenus=array();
        foreach ($menu_group as $v1){
            if($v1['id']==$i){
                unset($v1);
            }else{//获取子节点
                $childMenusOne=[
                    "id"=>$v1['id'],"name"=>$v1['name'],"parentId"=>$v1['pid'],"url"=>"__ADMIN__/".$v1['module'].'/'.$v1['action'], "icon"=>"","order"=>"1","isHeader"=>$v1['isHeader'],"childMenus"=>""
                ];
                array_push($childMenus,$childMenusOne);
            }
        }
        //获取父节点 id就是$i;
        $pid_menu=$menu->find($i);
        $one=[
            "id"=>$pid_menu['id'],"name"=>$pid_menu['name'],"parentId"=>$pid_menu['pid'],"url"=>"","icon"=>"","order"=>"1","isHeader"=>$pid_menu['isHeader'],"childMenus"=>$childMenus
        ];

        array_push($menu_list,$one);
    }
    return json_encode($menu_list,JSON_UNESCAPED_UNICODE);
}

/**
 * 过滤管理员菜单
 * @return mixed
 */
function getMenuArr(){

    $menu = Db::name('Access');
    //获取用户权限集
    $act_list = session('act_list');
    $list=explode(',', $act_list);
    //echo $act_list;

    //获取各个分组
    $menu_list=array();
    //查询用户拥有权限的二级菜单
    $role = $menu->where("id in ($act_list)")->cache(true)->select();
    $p_id =[];
    foreach($role as $val){
        if(!in_array($val['pid'],$p_id)){
            array_push($p_id,$val['pid']);
        }
    }
    foreach($p_id as $p){
        $menu_group = $menu->where('menu_group','in',$p)->select();
        $childMenus=array();
        foreach ($menu_group as $v1){
            if($v1['id']==$p){
                unset($v1);
            }else{//获取子节点
                if(in_array($v1['id'],$list)){
                    $childMenusOne=[
                        "id"=>$v1['id'],"name"=>$v1['name'],"parentId"=>$v1['pid'],"url"=>"__ADMIN__/".$v1['module'].'/'.$v1['action'], "icon"=>"","order"=>"1","isHeader"=>$v1['isHeader'],"childMenus"=>""
                    ];
                    array_push($childMenus,$childMenusOne);
                }
            }
        }
        //获取父节点 id就是$i;
        $pid_menu=$menu->find($p);
        $one=[
            "id"=>$pid_menu['id'],"name"=>$pid_menu['name'],"parentId"=>$pid_menu['pid'],"url"=>"","icon"=>"","order"=>"1","isHeader"=>$pid_menu['isHeader'],"childMenus"=>$childMenus
        ];

        array_push($menu_list,$one);
    }
    return json_encode($menu_list,JSON_UNESCAPED_UNICODE);
}

function respose($res){
    exit(json_encode($res));
}