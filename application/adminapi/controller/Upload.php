<?php

namespace app\adminapi\controller;

use think\Controller;

class Upload extends BaseApi
{
    //单图上传
    public function logo(){
         $type=input('type');
         if(empty($type)){
             $this->fail('缺少参数');
         }
         $file=request()->file('logo');
         if(empty($file)){
             $this->fail('必须上传文件');
         }
         $info=$file->validate(['size'=>10*21231*15678,'ext'=>'jpg,png,gif'])->move(ROOT_PATH.'public'.DS.'uploads'.DS.$type);
         if($info){
             $logo=DS.'uploads'.DS.$type.DS.$info->getSaveName();
             $this->ok($logo);
         } else{
             $msg=$file->getError();
             $this->fail($msg);
         }
    }
    //多图上传
    public function images(){
        //接收type参数，图片分组,默认分组
        $type=input('type','goods');
        //获取上传的文件（数组）
        $files=request()->file('images');
        //遍历数组逐个上传文件
        $data=['success'=>[],'error'=>[]];
        foreach($files as $file){
            $dir=ROOT_PATH.'public'.DS.'uploads'.DS.$type;
            if(!is_dir($dir)){
                 mkdir($dir);
            }
        $info=$file->validate(['size'=>10*1024*1024,'ext'=>'jpg,jpeg,png,gif'])->move($dir);
        if($info){
           $path=DS.'uploads'.DS.$type.DS.$info->getSaveName();
           $data['success'][]=$path;
        }else{
            $data['error'][]=[
                'name'=>$file->getInfo('name'),
                'msg'=>$file->getError()
            ];
        }
        }
        $this->ok($data);
    }
}
