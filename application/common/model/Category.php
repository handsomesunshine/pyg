<?php

namespace app\common\model;

use think\Model;

class Category extends Model
{
    public function brands(){
        return $this->hasMany('Brand','cate_id','id');
    }
}
