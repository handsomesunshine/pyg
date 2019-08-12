<?php

namespace app\adminapi\controller;

use \app\common\model\Auth as AuthModel;
use think\Request;

class Auth extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $parms=input();   //接收提交数据
        $where=[];
        if(!empty($parms['keyword'])){
            $where['auth_name']=['like',"%{$parms['keyword']}%"];
        }
        $list=\app\common\model\Auth::field('id,auth_name,pid,pid_path,auth_c,auth_a,is_nav,level')->where($where)->select();
        $list=(new \think\Collection($list))->toArray();
        if(!empty($parms['type'])&&$parms['type']==true){
            //父子树状列表
            $data=get_tree_list($list);
        }else{
            //
            $data=get_cate_list($list);
        }
        $this->ok($data);
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $parpm=input();
        if(empty($parpm['pid'])){
            $parpm['pid']=0;
        }
        if(empty($parpm['is_nav'])){
            $parpm['is_nav']=$parpm['radio'];
        }
        $valist=$this->validate($parpm,[
            'auth_name'=>'require',
            'pid'=>'require',
            'is_nav'=>'require'
        ]);
        if($valist!==true){
             $this->fail($valist,401);
        }
        if($parpm['pid']==0){
             $parpm['pid_path']=0;
             $parpm['level']=0;
             $parpm['auth_c']='';
             $parpm['auth_a']='';
        }else{
             $info=AuthModel::find($parpm['pid']);
             if(empty($info)){
                 $this->fail('数据异常');
             }
             $parpm['level']=$info['level']+1;
             $parpm['pid_path']=$info['pid_path'].'_'.$info['id'];
        }
        $newInfo=AuthModel::create($parpm,true);
        $data=AuthModel::find($newInfo['id']);
        $this->ok($data);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $list= AuthModel::field('id,auth_name,pid,pid_path,auth_c,auth_a,is_nav,level')->find($id);
        $this->ok($list);
    }
    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $parpm=input();
        if(empty($parpm['pid'])){
             $parpm['pid']=0;
        }
        if(empty($parpm['is_nav'])){
             $parpm['is_nav']=$parpm['radio'];
        }
        $valist=$this->validate($parpm,[
            'auth_name'=>'require',
            'pid'=>'require',
            'is_nav'=>'require'
        ]);
        if($valist!==true){
             $this->fail($valist,401);
        }
        $auto=AuthModel::find($parpm['pid']);
        if(empty($auto)){
           $this->fail('数据异常');
        }
        if($parpm['pid']==0){
            $parpm['level']=0;
            $parpm['pid_path']=0;
            $parpm['auth_c']='';
            $parpm['auth_a']='';
        }else{
            $parpm['level']=$auto['level']+1;
            $parpm['pid_path']=$auto['pid_path'].'_'.$auto['id'];
        }
        AuthModel::update($parpm,['id'=>$id],true);
        $data=AuthModel::find($id);
        $this->ok($data);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $list=AuthModel::where('pid',$id)->count();
        if($list>0){
            $this->fail('有子权限,无法删除');
        }
        AuthModel::destroy($id);
        $this->ok();
    }

    /**
     *菜单权限列表
     *
     */
    public function nav(){
        $parpms=input('user_id');
        $role=\app\common\model\Admin::find($parpms);
        $role_id=$role['role_id'];
        if($role_id==1){
             $data=\app\common\model\Auth::where('is_nav',1)->select();
        }else{
            $auto=\app\common\model\Role::where('id',$role_id)->find();
            $auto_ids=$auto['role_auth_ids'];
            $data=AuthModel::where('is_nav',1)->where('id','in',$auto_ids)->select();
        }
        $list=(new \think\Collection($data))->toArray();
        $info=get_tree_list($list);
        $this->ok($info);
    }
}
