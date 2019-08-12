<?php

namespace app\home\logic;
class Auto
{
    public static function ali($result)
    {
//          dump($result->alipay_user_info_share_response);
          $res=$result->alipay_user_info_share_response;
          $row=\app\common\model\User::where('openid',$res->user_id)->find();
          if($row){
               $row->nickname=$res->nickname??'这个人有点懒';
               $row->figure_url=$res->avatar??'';
               $row->save();
          }else{
              $row=[
                  'nickname'=>'测试',
                  'open_type'=>'alipay',
                  'openid'=>$res->user_id,
                  ];
              \app\common\model\User::create($row);
          }
    }
}