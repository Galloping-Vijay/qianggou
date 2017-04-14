<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2017/4/14
 * Time: 10:37
 */

namespace app\admin\model;


class AdminLog extends BaseModel
{
    public function Admin()
    {
        return $this->belongsTo('Admin','admin_id');
    }
}