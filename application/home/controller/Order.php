<?php

namespace app\home\controller;

use app\common\model\SpecGoods;
use think\Controller;
use think\Exception;
use think\Request;
use app\common\model\Address as AddressModel;

class Order extends Base
{
    /**
     * 显示结算页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if (!session('?user_info')) {
            session('blank_url', 'home/cart/index');
            $this->redirect('home/login/login');
        }
        $order = AddressModel::where('user_id', session('user_info.id'))->select();
        $list = \app\home\logic\OrderLogic::getCartDataWithGoods();
        return view('create', ['order' => $order, 'cart_data' => $list['cart_data'], 'total_number' => $list['total_number'], 'total_price' => $list['total_price']]);
    }

    /**
     * 支付页
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $parms = input('');
        $valist = $this->validate($parms, [
            'address_id' => 'require|integer|gt:0'
        ]);
        if ($valist !== true) {
            $this->error($valist);
        }
        $address = \app\common\model\Address::where('id', $parms['address_id'])->find();
        if (!$address) {
            $this->error('请选择正确的收货地址');
        }
        $goods = \app\home\logic\OrderLogic::getCartDataWithGoods();
        $order_sn = time() . mt_rand(100000, 999999);
        $user_id = session('user_info.id');
        $order_data = [
            'order_sn' => $order_sn,
            'user_id' => $user_id,
            'consignee' => $address['consignee'],
            'address' => $address['area'] . $address['address'],
            'phone' => $address['phone'],
            'goods_price' => $goods['total_price'],
            'shipping_price' => 0,
            'coupon_price' => 0,
            'order_amount' => $goods['total_price'],
            'total_amount' => $goods['total_price']
        ];
        \think\Db::startTrans();
        try {
            foreach ($goods['cart_data'] as $v) {
                if ($v['number'] > $v['goods_number']) {
                    throw  new \Exception('购买的商品数量不足', 400);
                }
            }

            $order = \app\common\model\Order::create($order_data);
            $order_goods_data = [];
            foreach ($goods['cart_data'] as $v) {
                $row = [
                    'order_id' => $order['id'],
                    'goods_id' => $v['goods_id'],
                    'spec_goods_id' => $v['spec_goods_id'],
                    'number' => $v['number'],
                    'goods_name' => $v['goods_name'],
                    'goods_logo' => $v['goods_logo'],
                    'goods_price' => $v['goods_price'],
                    'spec_value_names' => $v['value_names'],
                ];
                $order_goods_data[] = $row;
            }
            $order_goods = new \app\common\model\OrderGoods();
            $order_goods->saveAll($order_goods_data);
            $spec_goods = [];
            $goods_data = [];
            foreach ($goods['cart_data'] as $v) {
                if ($v['spec_goods_id']) {
                    $row = [
                        'id' => $v['spec_goods_id'],
                        'store_count' => $v['store_count'] - $v['number'],
                        'store_frozen' => $v['store_frozen'] + $v['number'],
                    ];
                    $spec_goods[] = $row;
                } else {
                    $row = [
                        'id' => $v['goods_id'],
                        'goods_number' => $v['goods_number'] - $v['number'],
                        'frozen_number' => $v['frozen_number'] + $v['number'],
                    ];
                    $goods_data[] = $row;
                }
            }
            $spec_goods_model = new \app\common\model\SpecGoods();
            $spec_goods_model->saveAll($spec_goods);
            $goods_data_model = new \app\common\model\Goods();
            $goods_data_model->saveAll($goods_data);
            \app\common\model\Cart::where('user_id',session('user_info.id'))->where('is_selected',1)->delete();
            \think\Db::commit();
            //生成二维码
            //二维码图片中的链接
            $url = url('/home/order/qrpay', ['id' => $order_data['order_sn'], 'debug' => true], true, 'http://pyg.tbyue.com');
            //生成支付二维码
            $qrCode = new \Endroid\QrCode\QrCode($url);
            //二维码保存路径
            $qr_path = '/uploads/qrcode/' . uniqid(mt_rand(100000, 999999), true) . '.png';
            //将二维码图片信息保存到文件中
            $qrCode->writeFile('.' . $qr_path);
            $this->assign('qr_path', $qr_path);
            $pay_type = config('pay_type');
            return view('pay', ['order_sn' => $order_sn, 'pay_type' => $pay_type, 'total_price' => $goods['total_price']]);
        } catch (\Exception $e) {
            \think\Db::rollback();
            $ee = $e->getMessage();
            $code = $e->getCode();
            if ($code == 400) {
                $this->error($ee);
            } else {
                $this->error('创建订单失败，请重试!');
            }
        }
    }

    //跳转到支付页
    public function pay()
    {
        $parms = input();
        $user_id = session('user_info.id');
        $order = \app\common\model\Order::where('order_sn', $parms['order_sn'])->where('user_id', $user_id)->find();
        if (!$order) {
            $this->error('订单不存在');
        }
        $con = config('pay_type.' . $parms['pay_code']);
        $order->pay_code = $parms['pay_code'];
        $order->pay_name = $con['pay_name'];
        $order->save();
        switch ($parms['pay_code']) {
            case 'wechat':
                break;
            case 'union':
                break;
            case 'alipay':
            default:
                echo "<form id='alipayment' action='/plugins/alipay/pagepay/pagepay.php' method='post' style='display: none'>
    <input id='WIDout_trade_no' name='WIDout_trade_no' value='{$order['order_sn']}'/>
    <input id='WIDsubject' name='WIDsubject' value='品优购订单'/>
    <input id='WIDtotal_amount' name='WIDtotal_amount' value='{$order['order_amount']}'/>
    <input id='WIDbody' name='WIDbody' value='测试订单'/>
</form><script>document.getElementById('alipayment').submit();</script>";
                break;
        }
    }

    //跳转到支付成功页面
    public function callback()
    {
        $parms = input();
        require_once('./plugins/alipay/config.php');
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaysevice = new \AlipayTradeService($config);
        $result = $alipaysevice->check($parms);
        if ($result) {
            $order_sn = $parms['out_trade_no'];
            $order = \app\common\model\Order::where('order_sn', $order_sn)->find();
            return view('paysuccess', ['pay_name' => '支付宝', 'order_amount' => $parms['total_amount'], 'order' => $order]);
        } else {
            return view('payfail', ['msg' => '支付失败']);
        }
    }

    //支付宝异步通知地址
    public function notify()
    {
        $parms = input();
        trace('支付宝异步通知-home/order/notify:' . json_encode($parms), 'debug');
        require_once('./plugins/alipay/config.php');
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaysevice = new \AlipayTradeService($config);
        $result = $alipaysevice->check($parms);
        if (!$result) {
            //验证签名失败
            trace('支付宝异步通知_home/order/notify:验证失败', 'error');
            echo 'fail';
            die;
        }
        //验证成功
        $order_sn = $parms['out_trade_no'];
        $trade_status = $parms['trade_status'];
        if ($trade_status == 'TRADE_FINISHED') {
            echo 'success';
            die;
        }
        $order = \app\common\model\Order::where('order_sn', $order_sn)->find();
        if (!$order) {
            trace('支付宝异步通知-home/order/notify:订单不存在', 'error');
            echo 'fail';
            die;
        }
        if ($order['order_amount'] != $parms['total_amount']) {
            trace('支付宝异步通知-home/order/notify:支付金额不对', 'error');
            echo 'fail';
            die;
        }
        if ($order['order_status']) {
            $order->order_status = 1;
            $order->pay_time = time();
            $order->save();
            $json = json_encode($parms);
            \app\common\model\Paylog::create(['order_sn' => $order_sn, 'json' => $json]);
            echo 'success';
            die;
        }
        echo 'success';
        die;
    }

    //二维码url地址路径
    public function qrpay()
    {
        $agent = \request()->server('HTTP_USER_AGENT');
        //判断扫码支付方式
        if (strpos($agent, 'MicroMessenger') !== false) {
            //微信扫码
            $pay_code = 'wx_pub_qr';
        } elseif (strpos($agent, 'AlipayClient') !== false) {
            $pay_code = 'alipay_qr';
        } else {
            //默认为支付宝扫码支付
            $pay_code = 'alipay_qr';
        }
        //接收订单id参数
        $order_sn = input('id');
        //创建支付
        $this->pingpp($order_sn, $pay_code);
    }

    //发起支付请求
    public function pingpp($order_sn, $pay_code)
    {
        //查询支付
        $order = \app\common\model\Order::where('order_sn', $order_sn)->find();
        //ping++聚合支付
        \Pingpp\Pingpp::setApiKey(config('pingpp.api_key'));
        \Pingpp\Pingpp::setPrivateKeyPath(config('pingpp.private_key_path'));
        \Pingpp\Pingpp::setAppId(config('pingpp.app_id'));
        $parms = [
            'order_no' => $order['order_sn'],
            'app' => ['id' => config('pingpp.app_id')],
            'channel' => $pay_code,
            'amount' => $order['order_amount'],
            'client_ip' => '127.0.0.1',
            'currency' => 'cny',
            'subject' => 'Your Subject',//自定义标题
            'body' => 'Your Body',//自定义内容
            'extra' => [],
        ];
        if ($pay_code == 'wx_pub_qr') {
            $params['extra']['product_id'] = $order['id'];
        }
        //创建charge对象
        $ch = \Pingpp\Charge::create($params);
        $this->redirect($ch->credential->$pay_code);
        die;
    }

    //查询订单状态
    public function status()
    {
        //接收订单编号
        $order_sn = input('order_sn');
        $res = curl_require("http://pyg.tbyue.com/home/order/status/order_sn/{$order_sn}", false);
        echo $res;
        die;
    }

    //支付结果页面
    public function payresult()
    {
        $order_sn = input('order_sn');

        $order = \app\common\model\Order::where('order_sn', $order_sn)->find();
        if (empty($order)) {
            return view('payfail', ['msg' => '订单编号错误']);
        } else {
            return view('paysuccess', ['pay_name' => $order->pay_name, 'order_amount' => $order['order_amount'], 'order' => $order]);
        }
    }
    public function di(){

    }
}
