<?php


namespace app\adminapi\logic;


use think\exception\Handle;
use think\exception\HttpException;
use think\Log;
use think\Controller;

class Error extends Handle
{
    public function render(\Exception $e)
    {

        if ($e instanceof HttpException) {

            $statusCode = $e->getStatusCode();

        }

//        //TODO::开发者对异常的操作
//        Log::init([
//            'type'=>'file',
//            'path'=>'./Log/'
//        ]);
//        Log::error($e->getMessage());
//        Log::save();
//        return parent::render($e);


    }


}