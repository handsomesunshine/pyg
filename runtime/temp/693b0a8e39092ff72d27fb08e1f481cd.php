<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"G:\wamp64\www\WWW\pyg\public/../application/home\view\login\mail.html";i:1564631445;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
    <title>个人注册</title>

    <link rel="stylesheet" type="text/css" href="/static/home/css/all.css"/>
    <link rel="stylesheet" type="text/css" href="/static/home/css/pages-register.css"/>

    <script type="text/javascript" src="/static/home/js/all.js"></script>
    <script type="text/javascript" src="/static/home/js/pages/register.js"></script>
</head>

<body>
<div class="register py-container ">
    <!--head-->
    <div class="logoArea">
        <a href="" class="logo"></a>
    </div>
    <!--register-->
    <div class="registerArea">
        <h3>注册新用户<span class="go">我有账号，去<a href="login.html" target="_blank">登陆</a></span></h3>
        <div class="info">
            <form action="<?php echo url('phone'); ?>" method="post" id="reg_form" class="sui-form form-horizontal">
                <div class="control-group">
                    <label class="control-label">邮箱：</label>
                    <div class="controls">
                        <input type="text" id="phone" name="phone" placeholder="请输入你的邮箱"
                               class="input-xfat input-xlarge">
                        <span class="error"></span>
                    </div>
                </div>
                <div class="control-group">
                    <label for="inputPassword" class="control-label">验证码：</label>
                    <div class="controls">
                        <input type="text" id="code" name="code" placeholder="验证码" class="input-xfat input-xlarge"
                               style="width:120px">
                        <button type="button" class="btn-xlarge" id="dyMobileButton">发送验证码</button>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="control-group">
                    <label for="inputPassword" class="control-label">登录密码：</label>
                    <div class="controls">
                        <input type="password" id="password" name="password" placeholder="设置登录密码"
                               class="input-xfat input-xlarge">
                        <span class="error"></span>
                    </div>
                </div>
                <div class="control-group">
                    <label for="inputPassword" class="control-label">确认密码：</label>
                    <div class="controls">
                        <input type="password" id="repassword" name="repassword" placeholder="再次确认密码"
                               class="input-xfat input-xlarge">
                        <span class="error"></span>
                    </div>
                </div>
                <div class="control-group">
                    <label for="inputPassword" class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <div class="controls">
                        <input name="m1" type="checkbox" value="2" checked=""><span>同意协议并注册《品优购用户协议》</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls btn-reg">
                        <a id="reg_btn" class="sui-btn btn-block btn-xlarge btn-danger reg-btn"
                           href="javascript:;">完成注册</a>
                    </div>
                </div>
            </form>
            <div class="clearfix"></div>
        </div>
    </div>
    <!--foot-->
    <div class="py-container copyright">
        <ul>
            <li>关于我们</li>
            <li>联系我们</li>
            <li>联系客服</li>
            <li>商家入驻</li>
            <li>营销中心</li>
            <li>手机品优购</li>
            <li>销售联盟</li>
            <li>品优购社区</li>
        </ul>
        <div class="address">地址：北京市昌平区建材城西路金燕龙办公楼一层 邮编：100096 电话：400-618-4000 传真：010-82935100</div>
        <div class="beian">京ICP备08001421号京公网安备110108007702
        </div>
    </div>
</div>
</body>
<script>
    $(function () {
        $('#reg_btn').click(function () {
            $('#reg_form').submit();
        });
        $('#dyMobileButton').click(function () {
            var phone = $('#phone').val();
            if (phone == '') {
                $('#phone').next().html('邮箱不能为空');
                return;
            } else if (!/^\d*@/.test(phone)) {
                $('#phone').next().html('邮箱格式不正确');
                return;
            } else {
                $('#phone').next().html('');
            }
            var time=6;
            var timer=setInterval(function(){
                time--;
                if(time>0){
                    $('#dyMobileButton').html('发送验证码'+time+'秒');
                    $('#dyMobileButton').prop('disabled',true);
                }
                else{
                    $('#dyMobileButton').html('发送验证码');
                    $('#dyMobileButton').prop('disabled',false);
                    clearInterval(timer);
                }
            },1000);
            $.ajax({
                'url':"<?php echo url('home/login/sendcodemail'); ?>",
                'type':'post',
                'data':"mail="+phone,
                'dataType':'json',
                'success':function(res){
                    alert(res.msg);return;
                }
            })
        });
    });
</script>
</html>