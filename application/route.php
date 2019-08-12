<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
Route::domain('adminapi.pyg.com',function (){
    Route::get('/','adminapi/index/index');
    //验证码图片
    Route::get('captcha/:id', "\\think\\captcha\\CaptchaController@index");//访问图片需要
    Route::get('captcha','adminapi/login/captcha');
    //登录
    Route::post('login','adminapi/login/login');
    //退出
    Route::get('logout','adminapi/login/logout');
    //权限路由
    Route::resource('auths','adminapi/auth',[],['id'=>'\d+']);
    //设置菜单权限
    Route::get('nav','adminapi/auth/nav');
    //角色路由
    Route::resource('roles','adminapi/role',[],['id'=>'\d+']);
    //管理员路由
    Route::resource('admins','adminapi/admin',[],['id'=>'\d+']);
    //商品分类路由
    Route::resource('categorys','adminapi/category',[],['id'=>'\d+']);
    //单文件上传
    Route::post('logo','adminapi/upload/logo');
    //多图上传
    Route::post('images','adminapi/upload/images');
    //商品品牌路由
    Route::resource('brands','adminapi/brand',[],['id'=>'\d+']);
    //商品模型路由
    Route::resource('types','adminapi/type',[],['id'=>'\d+']);
    //商品路由
    Route::resource('goods','adminapi/goods',[],['id'=>'\d+']);
    //删除图片相册路由
    Route::delete('delpics/[:id]','adminapi/goods/delpics',[],['id'=>'\d+']);
    //订单路由
    Route::resource('orders','adminapi/order',[],['id'=>'\d+']);
});
