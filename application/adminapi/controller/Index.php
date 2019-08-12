<?php
namespace app\adminapi\controller;


class Index extends BaseApi
{
    public function index()
    {
        $one = [
            ['id' => 1, 'auth_name' => '权限管理', 'pid'=>0],
            ['id' => 2, 'auth_name' => '商品管理', 'pid'=>0],
            ['id' => 3, 'auth_name' => '订单管理', 'pid'=>0],
        ];

        $two = [
            ['id' => 4, 'auth_name' => '管理员列表', 'pid'=>1],
            ['id' => 5, 'auth_name' => '管理员新增', 'pid'=>1],
            ['id' => 6, 'auth_name' => '角色管理', 'pid'=>1],
            ['id' => 7, 'auth_name' => '权限管理', 'pid'=>1],
            ['id' => 8, 'auth_name' => '商品列表', 'pid'=>2],
            ['id' => 9, 'auth_name' => '商品新增', 'pid'=>2],
            ['id' => 10, 'auth_name' => '订单列表', 'pid'=>3],
            ['id' => 11, 'auth_name' => '订单新增', 'pid'=>3],
        ];
        $res=[];
        foreach ($one as $k=>$value){
            $value['son']=[];
            foreach ($two as $i=>$v){
               if($value['id']==$v['pid']){
                   $value['son'][]=$v;
               }
            }
            $res[]=$value;
        }
        dump($res);
    }
}
