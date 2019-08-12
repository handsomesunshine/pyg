<?php

namespace app\home\controller;

use think\Collection;

class Myorder extends Base
{
    public function index()
    {
        $user_id = session('user_info.id');
        if (!isset($user_id)) {
            session('blank_url', 'home/myorder/index');
            $this->redirect('home/login/login');
        }
        $list = \app\common\model\Order::where('user_id', $user_id)->select();
        $total = count($list);

        $list = (new Collection($list))->toArray();
        $order_goods_id = [];
        foreach ($list as $v) {
            $order_goods_id[] = $v['id'];
        }
        $order_goods = \app\common\model\OrderGoods::where('order_id', 'in', $order_goods_id)->select();
        $goods = [];
        foreach ($order_goods as $v) {
            $goods[$v['order_id']][] = $v->toArray();
        }
        unset($v);
        $status_zero = [];
        $status_two = [];
        $status_three = [];
        foreach ($list as &$v) {
            $v['son'] = $goods[$v['id']];
            if ($v['order_status'] == '0') {
                $status_zero[] = $v;
            }
            if ($v['order_status'] == '2') {
                $status_two[] = $v;
            }
            if ($v['order_status'] == '3') {
                $status_three[] = $v;
            }
        };
        $total_zero = count($status_zero);
        $total_two = count($status_two);
        $total_three = count($status_three);
        unset($v);
        return view('index', ['order' => $list, 'total' => $total, 'status_zero' => $status_zero, 'total_zero' => $total_zero, 'status_two' => $status_two, 'total_two' => $total_two, 'total_three' => $total_three, 'status_three' => $status_three]);
    }
}