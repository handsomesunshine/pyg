<?php

namespace app\home\controller;

use think\Collection;
use think\Controller;
use think\Request;

class Base extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $list=\app\common\model\Category::select();
        $info=(new Collection($list))->toArray();
        $category=get_tree_list($info);
        $user_id=session('user_info.id');
        if(empty($user_id)){
            $data=cookie('cart')? :[];
            $number=count($data);
        }else{
            $list=\app\common\model\Cart::where('user_id',$user_id)->select();
            $number=count($list);
        }
        $this->assign(['category'=>$category,'number'=>$number]);
    }
}
