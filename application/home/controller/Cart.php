<?php
    namespace app\home\controller;

    class Cart extends Base
    {
        //添加购物车成功页
        public function addcart(){
            if(request()->isGet()){
                $this->redirect('home/index/index');
            }
            $parms=input();
            $valist=$this->validate($parms,[
                'goods_id'=>'require|integer|gt:0',
                'number'=>'require|integer|gt:0',
                'spec_goods_id'=>'integer|gt:0'
            ]);
            if ($valist!==true){
                 $this->error($valist);
            }
            \app\home\logic\CartLogic::addcart($parms['goods_id'],$parms['spec_goods_id'],$parms['number']);
            $goods=\app\common\model\Goods::getGoodsWritSpec($parms['goods_id'],$parms['spec_goods_id']);
            return view('addcart',['goods'=>$goods,'number'=>$parms['number']]);
        }
        //购物车页面
        public function index(){
            $list=\app\home\logic\CartLogic::getAllCart();
            foreach ($list as &$v){
                $v['goods']=\app\common\model\Goods::getGoodsWritSpec($v['goods_id'],$v['spec_goods_id']);
            }
            unset($v);
            return view('index',['goods'=>$list]);
        }
        //修改购买数量
        public function changenum(){
             $parms=input();
             $valist=$this->validate($parms,[
                 'id'=>'require',
                 'number'=>'require|integer|gt:0'
             ]);
             if($valist!==true){
                 $res=['code'=>400,'msg'=>'参数错误'];
                 echo json_encode($res);die;
             }
             \app\home\logic\CartLogic::changeNum($parms['id'],$parms['number']);
             $res=['code'=>200,'msg'=>'success'];
             echo json_encode($res);die;
        }
        //删除购物车商品
        public function delcart(){
            $parms=input();
            $valist=$this->validate($parms,[
                'id'=>'require',
            ]);
            if($valist!==true){
                 $res=['code'=>400,'msg'=>'参数错误'];
                 echo json_encode($res);die;
            }
            \app\home\logic\CartLogic::delCart($parms['id']);
            $res=['code'=>200,'msg'=>'success'];
            echo json_encode($res);die;
        }
        //修改商品状态
        public function changestatus(){
            $parms=input();
            $valist=$this->validate($parms,[
                'id'=>'require',
                'status'=>'require|in:0,1',
            ]);
            if($valist!==true){
                $res=['code'=>400,'msg'=>$valist];
                echo json_encode($res);die;
            }
            \app\home\logic\CartLogic::changestatus($parms['id'],$parms['status']);
            $res=['code'=>200,'msg'=>'success'];
            echo json_encode($res);die;
        }
    }