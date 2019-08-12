<?php

namespace app\adminapi\controller;

use app\common\model\Admin as AdminModel;
use think\Request;

class Admin extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $parpm=input();
        $where=[];
        if(!empty($parpm['keyword'])){
             $where['username']=['like',"%{$parpm['keyword']}%"];
        }
        $data=AdminModel::alias('a')
            ->join('role r','a.role_id=r.id','left')
            ->field('a.id,username,email,nickname,last_login_time,status,role_name')
            ->where($where)
            ->paginate(2);
        $this->ok($data);
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
              'username'=>'require',
              'email'=>'require',
              'role_id'=>'require',
          ]);
          if($valist!==true){
               $this->fail($valist,401);
          }
          if(empty($parpm['password'])){
                $parpm['password']=123456;
          }
          $parpm['password']=password($parpm['password']);
          $list=AdminModel::create($parpm,true);
          $data=AdminModel::find($list['id']);
          $this->ok($data);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $info=AdminModel::find($id);
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
        if($id==1){
            $this->fail('超级管理员，无权修改');
        }
        if(!empty($parpm['type'])&&$parpm['type']=='reset_pwd'){
             $pwd['password']=password('123456');
             AdminModel::update(['password'=>$pwd],['id'=>$id],true);
        }else{
            unset($parpm['username']);
            AdminModel::update($parpm,['id'=>$id],true);
        }
        $list=AdminModel::find($id);
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
        if($id==1){
            $this->fail('不能删除超级管理员');
        }
        if($id==input('user_id')){
             $this->fail('不能删除自己');
        }
        $list=AdminModel::find($id);
        if(!$list){
            $this->fail('数据异常',401);
        }
        AdminModel::destroy($id);
        $this->ok();
    }
}
