<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        echo md5('shop'.'root12345');
    }

}
