<?php

namespace app\adminapi\controller;

use think\Controller;
use app\common\model\Role as RoleModel;
use think\Request;

class Role extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
         $list=RoleModel::where('id','>',1)->select();
         foreach ($list as $k=>$v){
//             dump($v['role_auth_ids']);
               $auth=\app\common\model\Auth::where('id','in',$v['role_auth_ids'])->select();
               $auth_list=(new \think\Collection($auth))->toArray();
               $tree=get_tree_list($auth_list);
               $list[$k]['role_auths']=$tree;
    }
         unset($v);
         $this->ok($list);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $parpm=input();
        $valist=$this->validate($parpm,[
            'role_name'=>'require',
            'auth_ids'=>'require'
        ]);
        if($valist!==true){
              $this->fail($valist,401);
        }
        $parpm['role_auth_ids']=$parpm['auth_ids'];
        $list=RoleModel::create($parpm,true);
        $this->ok($list);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
          $info=RoleModel::find($id);
          $this->ok($info);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $parpm=input();
        $valist=$this->validate($parpm,[
            'role_name'=>'require',
            'auth_ids'=>'require'
        ]);
        if($valist!==true){
            $this->fail($valist,401);
        }
        $parpm['role_auth_ids']=$parpm['auth_ids'];
        RoleModel::update($parpm,['id'=>$id],true);
        $list=RoleModel::find($id);
        $this->ok($list);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $info=RoleModel::find($id);
        if(!$info){
            $this->fail('数据异常',401);
        }
        RoleModel::destroy($id);
        $this->ok();
    }
}
