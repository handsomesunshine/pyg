<?php

namespace app\home\controller;

use app\common\model\User as UserModel;
use think\Controller;

class Login extends Base
{
    //显示登录页面
    public function login()
    {
        $this->view->engine->layout(false);
        return view();
    }

    //用户登录
    public function dologin()
    {
        $parpm = input();
        $valist = $this->validate($parpm, [
            'username|用户名' => 'require',
            'password|密码' => 'require|length:6,20'
        ]);
        if ($valist !== true) {
            $this->error($valist);
        }
        $parpm['password'] = password($parpm['password']);
        $info = UserModel::where(function ($query) use ($parpm) {
            $query->where('username', $parpm['username'])->whereOr('email', $parpm['username']);
        })->where('password', $parpm['password'])->find();
        if ($info) {
            session('user_info', $info->toArray());
            \app\home\logic\CartLogic::cookieToDb();
            $blank_url = session('blank_url') ?: 'home/index/index';
            $this->redirect($blank_url);
        } else {
            $this->error('用户名或密码错误');
        }
    }

    //显示注册页面
    public function register()
    {
        $this->view->engine->layout(false);
        return view();
    }

    //用户注册
    public function phone()
    {
        $parpm = input();
        $valist = $this->validate($parpm, [
            'phone|手机号' => 'require|regex:1[3-9]\d{9}|unique:user,phone',
            'code|验证码' => 'require|length:4',
            'password|密码' => 'require|length:6,20|confirm:repassword'
        ]);
        if ($valist !== true) {
            $this->error($valist);
        }
        $code = cache('register_code_' . $parpm['phone']);
        if ($code != $parpm['code']) {
            $this->error('验证码错误');
        }

        $parpm['password'] = password($parpm['password']);
        $parpm['username'] = $parpm['phone'];
        $parpm['nickname'] = nickname($parpm['phone']);
        UserModel::create($parpm, true);
        $this->redirect('home/login/login');
    }

    //发送验证码
    public function sendcode()
    {
        $parpm = input();
        $valist = $this->validate($parpm, [
            'phone' => 'require|regex:1[3-9]\d{9}'
        ]);
        if ($valist !== true) {
            $res = [
                'code' => 400,
                'msg' => $valist
            ];
            return Json($res);
            die;
        }
        $timer = cache('register_time_' . $parpm['phone']);
        if (time() - $timer < 60) {
            $rs = [
                'code' => '500',
                'msg' => '发送太过频繁'
            ];
            echo json_encode($rs);
            die;
        }
        $co = mt_rand(1000, 9999);
        $code = '%23code%23%3d' . $co;
        //发送短信验证
//          $resule=sendmsg($parpm['phone'],$code);
        $resule = true;
        //发送邮件验证
//         $resule=\app\home\logic\QQmail::qq($parpm['phone'],$co);
        if ($resule === true) {
            cache('register_code_' . $parpm['phone'], $co, 180);
            cache('register_time_' . $parpm['phone'], time(), 180);
            $re = [
                'code' => 200,
                'msg' => '短信发送成功',
                'data' => "$code"
            ];
            echo json_encode($re);
            die;
        } else {
            $re = [
                'code' => 400,
                'msg' => $resule
            ];
            echo json_encode($re);
            die;
        }
    }

    //qq登录
    public function qqcallback()
    {
        require_once("./plugins/qq/API/qqConnectAPI.php");
        $qc = new \QC();
        $access_token = $qc->qq_callback();
        $openid = $qc->get_openid();
        $qc = new \QC($access_token, $openid);
        $info = $qc->get_user_info();
        $user = UserModel::where('open_type', 'qq')->where('openid', $openid)->find();
        if ($user) {
            $user->nickname = $info['nickname'];
            $user->save();
        } else {
            UserModel::create(['open_type' => 'qq', 'openid' => $openid, 'nickname' => $info['nickname']]);
        }
        $list = UserModel::where('open_type', 'qq')->where('openid', $openid)->find();
        session('user_info', $list->toArray());
        $blank_url = session('blank_url') ?: 'home/index/index';
        \app\home\logic\CartLogic::cookieToDb();
        $this->redirect($blank_url);
    }

    //退出登录
    public function logout()
    {
        session(null);
        $this->redirect('home/login/login');
    }

    //短信测试
    public function text()
    {
        $phone = 18732332474;
        $code = '%23code%23%3d7481';
        $res = sendmsg($phone, $code);
        dump($res);
    }

    //支付宝回调页面
    public function zhi()
    {
        $params = input();
//        require_once './plugins/auth/AopSdk.php';
        require_once './plugins/auth/AopSdk.php';
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        $aop->appId = '2016100200645172';
        $aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAtqQB25mG0pn1n8IqAe13+0Q5B5mv+HyNkKQGiT4YPXWvjRRwl1cKcsV42d0LrcWt/N/jzuHxgm/aEvAhumhxWCEJcdkHfFZuvGhy2zvK595nwtD7efb9IA8DfEYXry9S3Ke8xK9UNeGHgC8gVxLB6t6l4SRKoQvtNaGKmHJimhYcdkoKo69WD12HYXghzUM6/XK0A380ipG+1Evl5Eo5pZoi+KDXE5mcJlQJWtSTkYbj7ImCBRU4XoDhjRL87DybRPRmhjoBMtsWvfg0kvvLJ7A2ZHYYhZuzJ6435FsEA+LzQPfzIVKjTeq4DBA3EsQW19aiN9hy+ahlYf9x2oENBwIDAQABAoIBADMebmKvctvpOaVQa/+EHqvWKXwmGOmcuua78hRhq9kv4kXDbcv+Ea+T88JOqyElDhCT/af+92DBu7DHQzlwWCEJhiI2U3EKpA3Z0iPodV82kaYmZex6I9jgOuKCn8hpn3ChBiWqyRAXopxPFGcqmdoKHTWJudWfh/IV17vZcB2eDyw4PYp+euhuBgYJHVNCTutX5moBt1nB5bp5EquKgGM/5E30OABGxYBPap7qbMnMGmGuQaGGalyrZ/7tdIZITgtelwIjmMzQRhokvvxw796BgVx60N6pmU82l+aQeG5KXQ01DT/CrQKTVBnnhpeIfbnSVlG08bVm11d5nJHo9/kCgYEA7HiYOpu211dcUXWKwYOxKUPNclSFYuGjslIPFuW/XVQN4/mi8M5jAO4uCG0eigF549SN1byWCFgxFnqcej93mGMJyTz3FKktmuHvK3cGhl5TROR9Qmregr3QYOVixXjf4eyez9k5V0LLEzA3Sa7VUmSMEchT4iRZNT22Dvl/oRUCgYEAxblYTObTALkGRTsV2COAws6qjKTbEX1B2Xq7WR/+RB1nPYFAnHLeEiAsNxMD/L9rfu5tJan7FDyMjLJlRKSUYg4NIw+V7D0uqhAbuKJ71K7+kqc6gnxXujTcqnkm2CnA6QB4MrhLXIW1+LgDSqjKs2Q0Im5+z1o/yK3wJjCTpKsCgYApf3xPhLYQkk8sKHGCRkcX08NBFh4WXTyp9YaaYRU1HqQVZuC2W7G6HxJK/kNGur2WQt3lLWQy8K8kn73IIk0tm+vCugUuRgYv0Qh02z105Sa9x09IEZMc5IymBHtR5kV701eHaDqM2rbiliqNrrXW8Lg4Aqzd2b0h/8NsW1KPhQKBgQCQ59r80RXGq9MancjOlIZjPs7jMFaUpLXDyxzOnpHcHahx/O37Onvh0M5WtorviuIfLmGzfrXSCOYAnyV0fyF4E1AxZ9S6Q8SVQiTu1bXnEzDjDcflpXctslweW5fKSB9nI77zfPlOH3hThZhz+OxFOKS0r0IkeMHSMxeTBXrO7QKBgQDSCYHJ0JrojFpNdIRm3+quZXfysHTfg2BBKQw2fCUGVd6hTELfXh8Rde4i+paa53XghdOXwIsVlm5+6Ati0FVPdoRpsJCI4K8zfZBSdhjr216EF87m3L+DTMhfkk83sSqcKtgrgGOTbxMbc1LWWDZWu5QC6ge2QFEyy8tnyxTOUw==';
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6XsZFGWEoAoIBMXHy4ZRF5Y8Y9ViVKFWmrzN/F/cpe23ZQCbxZb03ahIOZ1IYgI7Qupep0N69OYM3n2wyix5XoNXdLeQzU49FN2+0HlkZGanpNlwa+Ms7mTv+1TaiC3xdFD1raQLOYaWG5baXJHGkft3TjTzm+Ypdjt1w7e2ughbvWXeKO9wxvQMpKjTCUoB0ZPDLF6FsXzSyAJL1fN/k+zFlR6Ylf9hKOMOvLqMx2/W5rQJkSecTRGKl6M1Kzy0EDjifVqYIxSc+yKMahFr0t5Ds0sa+zR1k3MlE2c3Xuyt/jvFKrSA5KebiolkHgfRmrklDHrau+59h0LZFFaNIwIDAQAB';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'UTF-8';
        $aop->format = 'json';
//        dump($params);die;
        $request = new \AlipaySystemOauthTokenRequest ();
        $request->setGrantType("authorization_code");
        $request->setCode($params['auth_code']);
//        $request->setRefreshToken("201208134b203fe6c11548bcabd8da5bb087a83b");
        $result = $aop->execute($request);
//        dump($result);die;
        $result = (new \think\Collection($result))->toArray();
//        dump($result['alipay_system_oauth_token_response']->access_token);die;
        $access_token = $result['alipay_system_oauth_token_response']->access_token ?? '';
        if (!$access_token) {
            echo json_encode(['msg' => 'code无效'], JSON_UNESCAPED_UNICODE);
        }
        $request = new \AlipayUserInfoShareRequest ();
        $result = $aop->execute($request, $access_token);
        \app\home\logic\Auto::ali($result);
        $list = \app\common\model\User::where('open_type', 'alipay')->where('openid', $result->alipay_user_info_share_response->user_id)->find();
        session('user_info', $list->toArray());
        $blank_url = session('blank_url') ?: 'home/index/index';
        \app\home\logic\CartLogic::cookieToDb();
        $this->redirect($blank_url);
    }

    //支付宝路由拼接
    public function dev()
    {
        $http = 'https://openauth.alipaydev.com/oauth2/publicAppAuthorize.htm?';
        $app_id = '2016100200645172';
        $scope = 'auth_user';
        $redirect_uri = urlencode('http://www.pyg.com/home/login/zhi');
        $state = '0';
        $url = $http . 'app_id=' . $app_id . '&scope=' . $scope . '&redirect_uri=' . $redirect_uri . '&state=' . $state;
        $this->redirect($url);
    }

    //微博路由拼接
    public function wei()
    {

        include_once('./plugins/weibo/config.php');
        include_once('./plugins/weibo/saetv2.ex.class.php');

        $o = new \SaeTOAuthV2(WB_AKEY, WB_SKEY);

        $code_url = $o->getAuthorizeURL(WB_CALLBACK_URL);
        $this->redirect($code_url);
    }

    //微博回调地址
    public function accept()
    {
        $params = input('code');
//        dump($params);
        include_once('./plugins/weibo/config.php');
        include_once('./plugins/weibo/saetv2.ex.class.php');
        $o = new \SaeTOAuthV2(WB_AKEY, WB_SKEY);
        $ass_token = $o->getAccessToken('code', ['code' => $params, 'redirect_uri' => WB_CALLBACK_URL]);
        $o = new \SaeTClientV2(WB_AKEY, WB_SKEY, $ass_token['access_token']);
        $user_info = $o->show_user_by_id($ass_token['uid']);
//        dump($user_info);
//        die;
        $user = UserModel::where('openid', $user_info['id'])->find();
        if ($user) {
            $row = [
                'nickname' => $user_info['screen_name'],
                'figure_url' => $user_info['profile_image_url'],
            ];
            UserModel::update($row, ['id' => $user['id']]);
        } else {
            $row = [
                'nickname' => $user_info['screen_name'],
                'figure_url' => $user_info['profile_image_url'],
                'open_type' => 'weibo',
                'openid' => $user_info['id'],
                'username' => $user_info['name'],
            ];
            UserModel::create($row);
        }
        $list = UserModel::where('open_type', 'weibo')->where('openid', $user_info['id'])->find();
        session('user_info', $list->toArray());
        $blank_url = session('blank_url') ?: 'home/index/index';
        \app\home\logic\CartLogic::cookieToDb();
        $this->redirect($blank_url);
    }
    //邮箱注册显示页面
    public function mail(){
        $this->view->engine->layout(false);
       return view();
    }
    //发送邮箱验证
    public function sendcodemail(){
        $parpm=input();
        $co=mt_rand(10000,99999);
        $resule=\app\home\logic\QQmail::qq($parpm['mail'],$co);
        if ($resule === true) {
            cache('register_code_' . $parpm['mail'], $co, 180);
            cache('register_time_' . $parpm['mail'], time(), 180);
            $re = [
                'code' => 200,
                'msg' => '邮箱发送成功',
                'data' => "$co"
            ];
            echo json_encode($re);
            die;
        } else {
            $re = [
                'code' => 400,
                'msg' => $resule
            ];
            echo json_encode($re);
            die;
        }
    }
}