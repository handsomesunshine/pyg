<?php
   namespace  app\adminapi\logic;
   class AuthLogic{
       public static function check(){
         $controller=request()->controller();
         $action=request()->action();
//         if($controller=='Index'&&$action=='index'){
//             return true;
//         }
         $user_id=input('user_id');
         $role=\app\common\model\Admin::find($user_id);
         $role_id=$role['role_id'];
         if($role_id==1){
             return true;
         }
         $role_list=\app\common\model\Role::find($role_id);
         $role_auth_ids=explode(',',$role_list['role_auth_ids']);
         $auth=\app\common\model\Auth::where('auth_c',$controller)->where('auth_a',$action)->find();
         $auth_id=$auth['id'];
         if(in_array($auth_id,$role_auth_ids)){
             return true;
         }
         return false;
       }
   }