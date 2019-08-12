<?php
namespace app\home\controller;

class Index extends Base
{
    public function index()
    {

        $lives = \app\common\model\Live::order('id desc')->limit(6)->select();
        //渲染模板
        return view('index', ['lives'=>$lives]);
    }
//    public function finde(){
//        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
//        $params = [
//            'index' => 'test_index'
//        ];
//        $r = $es->indices()->create($params);
//        dump($r);die;
//    }
//    public function add(){
//        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
//        $params = [
//            'index' => 'test_index',
//            'type' => 'test_type',
//            'id' => 100,
//            'body' => ['id'=>100, 'title'=>'PHP从入门到精通', 'author' => '张三']
//        ];
//
//        $r = $es->index($params);
//        dump($r);die;
//    }
//    public function edit(){
//        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
//        $params = [
//            'index' => 'test_index',
//            'type' => 'test_type',
//            'id' => 100,
//            'body' => [
//                'doc' => ['id'=>100, 'title'=>'ES从入门到精通', 'author' => '李四']
//            ]
//        ];
//
//        $r = $es->update($params);
//        dump($r);die;
//    }
//    public function delete(){
//        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
//        $params = [
//            'index' => 'test_index',
//            'type' => 'test_type',
//            'id' => 100,
//        ];
//
//        $r = $es->delete($params);
//        dump($r);die;
//    }
}
