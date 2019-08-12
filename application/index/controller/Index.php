<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        $v=password('123456');
        dump($v);
        return '这是Index页面';
    }
}
