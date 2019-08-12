<?php

namespace app\home\logic;

use app\common\model\Cart as CartModel;
use think\Collection;

class CartLogic
{
    //添加购物车
    public static function addcart($goods_id, $spec_goods_id, $number, $is_selected = 1)
    {
        $user_id = session('user_info.id');
        if (session('?user_info')) {
            $where = [
                'user_id' => $user_id,
                'goods_id' => $goods_id,
                'spec_goods_id' => $spec_goods_id
            ];
            $info = CartModel::where($where)->find();
            if ($info) {
                $info->number += $number;
                $info->is_selected += $is_selected;
                $info->save();
            } else {
                $where['number'] = $number;
                $where['is_selected'] = $is_selected;
                CartModel::create($where);
            }
        } else {
            $data = cookie('cart') ?: [];
            $key = $goods_id . '_' . $spec_goods_id;
            if (isset($data[$key])) {
                $data[$key]['number'] += $number;
                $data[$key]['is_selected'] = $is_selected;
            } else {
                $data[$key] = [
                    'id' => $key,
                    'user_id' => $user_id,
                    'goods_id' => $goods_id,
                    'spec_goods_id' => $spec_goods_id,
                    'number' => $number,
                    'is_selected' => $is_selected,
                ];
            }
            cookie('cart', $data, 86400 * 7);
        }
    }

    //查询购物车
    public static function getAllCart()
    {
        if (session('?user_info')) {
            $user_id = session('user_info.id');
            $data = CartModel::where('user_id', $user_id)->field('id,user_id,goods_id,number,spec_goods_id,is_selected')->select();
            $data = (new \think\Collection($data))->toArray();
        } else {
            $data = cookie('cart') ?: [];
            $data = array_values($data);
        }
        return $data;
    }
    //登录后cookie数据转移到数据库
    public static function cookieToDb()
    {
        $data=cookie('cart')?:[];
        foreach ($data as $v){
              self::addcart($v['goods_id'],$v['spec_goods_id'],$v['number']);
        }
        cookie('cart',null);
    }
    //修改购买数量
    public static function changeNum($id,$number){
         if(session('?user_info')){
              $user_id=session('user_info.id');
              CartModel::update(['number'=>$number],['id'=>$id,'user_id'=>$user_id]);
         }else{
             $data=cookie('cart')?:[];
             $data[$id]['number']=$number;
             cookie('cart',$data,86400*7);
         }
    }
    //删除购物车的数据
    public static function delCart($id){
          if(session('?user_info')){
               $user_id=session('user_info.id');
               CartModel::where(['id'=>$id,'user_id'=>$user_id])->delete();
          }else{
              $data=cookie('cart')?:[];
              unset($data[$id]);
              cookie('cart',$data,86400*7);
          }
    }
    //修改商品状态
    public static function changestatus($id,$is_selected){
         if(session('?user_info')){
              $user_id=session('user_info.id');
              $where['user_id']=$user_id;
              if($id!='all'){
                  $where['id']=$id;
              }
              CartModel::where($where)->update(['is_selected'=>$is_selected]);
         }else{
             $data=cookie('cart')?:[];
             if($id=='all'){
                  foreach ($data as &$v){
                       $v['is_selected']=$is_selected;
                  }
                  unset($v);
             }else{
                 $data[$id]['is_selected']=$is_selected;
             }
             cookie('cart',$data,86400*7);
         }
    }
}