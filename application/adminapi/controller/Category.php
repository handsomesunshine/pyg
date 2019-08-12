<?php

namespace app\adminapi\controller;

use app\common\model\Category as CategoryModel;
use think\Request;

class Category extends BaseApi
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
        if(isset($parpm['pid'])){
            $where['pid']=$parpm['pid'];
        }
        $list=CategoryModel::where($where)->select();
        $list=(new \think\Collection($list))->toArray();
        if(!isset($parpm['type'])||$parpm['type']!='list'){
            $list=get_cate_list($list);
        }
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
           'cate_name'=>'require',
           'pid'=>'require',
            'is_show'=>'require',
           'is_hot'=>'require',
           'sort'=>'require'
        ]);
        if($valist!==true){
            $this->fail($valist);
        }
        if($parpm['pid']==0){
             $parpm['pid_path']=0;
             $parpm['pid_path_name']='';
             $parpm['level']=0;
        }else{
             $rs=CategoryModel::where('id',$parpm['pid'])->find();
             if(empty($rs)){
                $this->fail('数据异常',401);
             }
             $parpm['pid_path']=$rs['pid_path'].'_'.$rs['id'];
             $parpm['pid_path_name']=$rs['pid_path_name'].'_'.$rs['cate_name'];
             $parpm['level']=$rs['level']+1;
        }
        $parpm['image_url']=isset($parpm['logo'])?$parpm['logo']:'';
        $list=CategoryModel::create($parpm,true);
        $info=CategoryModel::find($list['id']);
        $this->ok($info);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $list=CategoryModel::find($id);
        $this->ok($list);
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
            'cate_name'=>'require',
            'pid'=>'require',
            'is_show'=>'require',
            'is_hot'=>'require',
            'sort'=>'require'
        ]);
        if($valist!==true){
            $this->fail($valist);
        }
        if($parpm['pid']==0){
            $parpm['pid_path']=0;
            $parpm['pid_path_name']='';
            $parpm['level']=0;
        }else{
            $rs=CategoryModel::where('id',$parpm['pid'])->find();
            if(empty($rs)){
                $this->fail('数据异常',401);
            }
            $parpm['pid_path']=$rs['pid_path'].'_'.$rs['id'];
            $parpm['pid_path_name']=$rs['pid_path_name'].'_'.$rs['cate_name'];
            $parpm['level']=$rs['level']+1;
        }
        if(!isset($parpm['logo'])&&!empty($parpm['logo'])){
            $parpm['image_url']=$parpm['logo'];
        }
        CategoryModel::update($parpm,['id'=>$id],true);
        $info=CategoryModel::find($id);
        $this->ok($info);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $total=CategoryModel::where('pid',$id)->count();
        if($total>0){
           $this->fail('分类下有子权限，不能删除');
        }
        CategoryModel::destroy($id);
        $this->ok();
    }
}
