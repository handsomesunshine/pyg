<?php

namespace app\home\logic;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class QQmail{
       public static function qq($addr,$code){
           $mail = new PHPMailer(true);
           try{
               //邮件调试模式
               $mail->SMTPDebug = 1;
               //设置邮件使用SMTP
               $mail->isSMTP();
               // 设置邮件程序以使用SMTP
               $mail->Host = 'smtp.qq.com';
               $mail->isSMTP();
               // 设置邮件内容的编码
               $mail->CharSet='UTF-8';
               // 启用SMTP验证
               $mail->SMTPAuth = true;
               // SMTP username
               $mail->Username = '2325401844@qq.com';
               // SMTP password
               $mail->Password = 'znoitblkndlvechh';
               // 连接的TCP端口
//            $mail->Port = 465;
               //设置发件人昵称
               $mail->FromName='sunshine';
               //设置发件人
               $mail->setFrom('2325401844@qq.com');
               //  添加收件人1
               $mail->addAddress($addr);
               // 将电子邮件格式设置为HTML
               $mail->isHTML(true);

               $mail->Subject = '【品优购】';
               $mail->Body    = "注册验证码:".$code;
//            $mail->AltBody = '这是非HTML邮件客户端的纯文本';
               $mail->send();
               echo 'Message has been sent';

           }catch (Exception $e){
               echo 'Mailer Error: ' . $mail->ErrorInfo;
           }
       }
}