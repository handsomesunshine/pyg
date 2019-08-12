<?php

namespace app\common\model;

use think\Model;

class Order extends Model
{
    public function ordergoods(){
        return $this->hasMany('OrderGoods','goods_id','id');
    }
    public function user(){
        return $this->belongsTo('User','user_id','id');
    }
}
