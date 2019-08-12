<?php

namespace app\common\model;

use think\Model;

class Cart extends Model
{
    public function goods(){
        return $this->belongsTo('Goods','goods_id','id')->bind('goods_name,goods_logo,goods_price,goods_number,cost_price,frozen_number');
    }
    public function specGoods(){
        return $this->belongsTo('SpecGoods','spec_goods_id','id')->bind(['value_ids','value_names','price','cost_price2'=>'cost_price','store_count','store_frozen']);
    }
}
