<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/13
 * Time: 10:31
 */

namespace app\admin\model;


class Admin extends BaseModel
{
    public function adminRole(){
        return $this->belongsTo('AdminRole','role_id');
    }
}