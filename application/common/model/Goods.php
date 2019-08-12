<?php

namespace app\common\model;

use think\Model;

class Goods extends Model
{
    public function category(){
        return $this->belongsTo('Category','cate_id','id')->bind('cate_name');
    }
    public function categoryRow(){
        return $this->belongsTo('Category','cate_id','id');
    }
    public function brand(){
        return $this->belongsTo('Brand','brand_id','id')->bind(['brand_name'=>'name']);
    }
    public function brandRow(){
        return $this->belongsTo('Brand','brand_id','id');
    }
    public function type(){
        return $this->belongsTo('Type','type_id','id')->bind('type_name');
    }
    public function typeRow(){
        return $this->belongsTo('Type','type_id','id');
    }
    public function goodsImages(){
        return $this->hasMany('goods_images','goods_id','id');
    }
    public function specGoods(){
        return $this->hasMany('SpecGoods','goods_id','id');
    }
    public static function getGoodsWritSpec($goods_id,$spec_goods_id){
       if($spec_goods_id){
           $where=[
               'S.id'=>$spec_goods_id
           ];
       }else{
           $where=['G.id'=>$goods_id];
       }
       $goods=self::alias('g')
           ->join(config('database.prefix').'spec_goods S','g.id=s.goods_id','left')
           ->field('G.*,S.value_ids,S.value_names,S.price,S.cost_price as cost_price_spec,S.store_count')
           ->where($where)
           ->find();
       if($goods['price']>0){
           $goods['goods_price']=$goods['price'];
       }
       if($goods['cost_price']>0){
           $goods['cost_price']=$goods['cost_price_spec'];
       }
       return $goods;
    }
}
