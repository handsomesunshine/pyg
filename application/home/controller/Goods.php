<?php

namespace app\home\controller;

use app\common\model\Goods as GoodsModel;
use think\Controller;
use think\View;

class Goods extends Base
{
    //根据分类展示商品列表
    public function index($id=0)
    {
          //普通的查询
//          $list=GoodsModel::where('cate_id',$id)->order('update_time desc')->paginate(10);
//          $cate=\app\common\model\Category::where('id',$id)->find();
//          $brand=\app\common\model\Brand::where('cate_id',$id)->select();
        //return view('index',['list_info'=>$list,'cate_info'=>$cate,'brands'=>$brand]);
          //搜索引擎查询
        //接收参数
        $keywords = input('keywords');
        if(empty($keywords)){
            //获取指定分类下商品列表
            if(!preg_match('/^\d+$/', $id)){
                $this->error('参数错误');
            }
            //查询分类下的商品
            $list = \app\common\model\Goods::where('cate_id', $id)->order('id desc')->paginate(10);
            //查询分类名称
            $category_info = \app\common\model\Category::find($id);
            $cate_name = $category_info['cate_name'];
        }else{
            try{
                //从ES中搜索
                $list = \app\home\logic\GoodsLogic::search();
                $cate_name = $keywords;
            }catch (\Exception $e){
                $this->error('服务器异常');
            }
        }
        $brand=\app\common\model\Brand::where('cate_id',$id)->select();
        return view('index', ['list_info' => $list, 'cate_name' => $cate_name,'brands'=>$brand]);

    }
    //商品详情页
    public function detail($id){
          $goods=GoodsModel::with('goods_images,spec_goods')->where('id',$id)->find();
//          dump($goods);
//          die;
          if(!empty($goods['spec_goods'])){
               if($goods['spec_goods'][0]['price']>0){
                   $goods['goods_price']=$goods['spec_goods'][0]['price'];
               }
               if($goods['spec_goods'][0]['cost_price']>0){
                   $goods['cost_price']=$goods['spec_goods'][0]['cost_price'];
               }
               if($goods['spec_goods'][0]['store_count']>0){
                   $goods['store_count']=$goods['spec_goods'][0]['store_count'];
               }else{
                   $goods['store_count']=0;
               }
          }
          $goods['goods_attr']=json_decode($goods['goods_attr'],true);
          $valer_ids=array_unique(explode('_',implode('_',array_column($goods['spec_goods'],'value_ids'))));
          $spec=\app\common\model\SpecValue::with('specs')->where('id','in',$valer_ids)->select();
//          dump($spec);
//          die;
          $res=[];
          foreach ($spec as $val){
                $res[$val['spec_id']]=[
                       'spec_id'=>$val['spec_id'],
                       'spec_name'=>$val['spec_name'],
                       'spec_value'=>[]
                ];
          }
          foreach ($spec as $v){
               $res[$v['spec_id']]['spec_value'][]=$v;
          }
        $value_ids_map = [];
          foreach($goods['spec_goods'] as $v){
              $row=[
                  'id'=>$v['id'],
                  'price'=>$v['price'],
              ];
              $value_ids_map[$v['value_ids']]=$row;
          }
          $value_ids_map=json_encode($value_ids_map);
          return view('detail',['goods'=>$goods,'specs'=>$res,'value_ids_map'=>$value_ids_map]);
    }
}
