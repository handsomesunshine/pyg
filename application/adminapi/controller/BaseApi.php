<?php
    namespace app\adminapi\controller;
    use think\Controller;
    use think\Log;
    use think\Request;
    use tools\jwt\Token;

    class BaseApi extends Controller{
        protected $no_login=['login/login','login/captcha'];
        public function __construct(Request $request)
        {
            echo 1;die;
            parent::__construct($request);
                 //处理跨域预检请求
                //允许的源域名
                header("Access-Control-Allow-Origin: *");
                //允许的请求头信息
                header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
                //允许的请求类型
               header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
                   try {
                       $path=strtolower($this->request->controller()).'/'.$this->request->action();
                       if (!in_array($path, $this->no_login)) {
//                           $user_id = Token::getUserId();
                            $user_id=1;
                           if (empty($user_id)) {
                               $this->fail('未登录或Token无效', 403);
                           }
                           $this->request->get(['user_id' => $user_id]);
                           $this->request->post(['user_id' => $user_id]);
                           $info=\app\adminapi\logic\AuthLogic::check();
                           if(!$info){
                                $this->fail('没有权限',401);
                           }
                       }
                   }catch (\Exception $e){
                         $this->fail('服务异常，请检查token令牌', 403);
                   }




        }
         //通用响应
        public function response($code=200,$msg='success',$data=[]){
              $res=[
                  'code'=>$code,
                  'msg'=>$msg,
                  'data'=>$data
              ];
              json($res)->send();die;
//              echo json_encode($res, JSON_UNESCAPED_UNICODE);die;
        }
        //返回错误信息提示
        protected function fail($msg='fail',$code='400'){
            return $this->response($code,$msg);
        }
        //返回成功信息提示
        protected function ok($data=[],$code='200',$msg='success'){
             return $this->response($code,$msg,$data);
        }
    }
