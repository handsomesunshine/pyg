<?php

namespace app\adminapi\controller;

use app\common\model\Goods as GoodsModel;
use app\common\model\Type as TypeModel;
use think\Request;


class Goods extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $parpm = input();
        $where = [];
        if (isset($parpm['keyword']) && !empty($parpm['keyword'])) {
            $keyword = $parpm['keyword'];
            $where['goods_name'] = ['like', "%$keyword%"];
        }
        $info = GoodsModel::with('category,brand,type')->where($where)->paginate(10);
        $this->ok($info);
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $parpm = input();
        $valist = $this->validate($parpm, [
            'goods_name|商品名' => 'require',
            'goods_price|商品价格' => 'require',
//              'goods_logo|商品logo'=>'require',
//              'goods_images|相册图片'=>'require|array',
            'item|商品规格' => 'require|array',
            'attr|商品属性' => 'require|array'
        ]);
        if ($valist !== true) {
            $this->fail($valist);
        }
        \think\Db::startTrans();
        try {

            if (is_file('.' . $parpm['goods_logo'])) {
                $good_logo = dirname($parpm['goods_logo']) . DS . 'thumb' . basename($parpm['goods_logo']);
                \think\Image::open('.' . $parpm['goods_logo'])->thumb(210, 240)->save('.' . $good_logo);
                $parpm['goods_logo'] = $good_logo;
            }
            $parpm['goods_attr'] = json_encode($parpm['attr'], JSON_UNESCAPED_UNICODE);
            $goods = GoodsModel::create($parpm, true);
            $goods_images = [];
            foreach ($parpm['goods_images'] as $image) {
                if (is_file('.' . $image)) {
                    $image_big = dirname($image) . DS . 'big_' . basename($image);
                    $image_sml = dirname($image) . DS . 'sml_' . basename($image);
                    $thmp = \think\Image::open('.' . $image);
                    $thmp->thumb(800, 800)->save('.' . $image_big);
                    $thmp->thumb(400, 400)->save('.' . $image_sml);
                    $row = [
                        'goods_id' => $goods['id'],
                        'pics_big' => $image_big,
                        'pics_sma' => $image_sml
                    ];
                    $goods_images[] = $row;
                }
            }
            $images = new \app\common\model\GoodsImages();
            $images->saveAll($goods_images);
            $goods_item = [];
            foreach ($parpm['item'] as $value) {
                $value['goods_id'] = $goods['id'];
                $goods_item[] = $value;
            }
            $item = new \app\common\model\SpecGoods();
            $item->allowField(true)->saveAll($goods_item);
            \think\Db::commit();
            $list = GoodsModel::with('category,brand,type')->find($goods['id']);
            $this->ok($list);
        } catch (\Exception $e) {
            \think\Db::rollback();
            $ee = $e->getMessage();
            $this->fail($ee);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $info = GoodsModel::with('category_row,brand_row,goods_images,spec_goods')->find($id);
        $info['category'] = $info['category_row'];
        $info['brand'] = $info['brand_row'];
        unset($info['category_row']);
        unset($info['brand_row']);
        $type = TypeModel::with('specs,attrs,specs.spec_values')->find($info['type_id']);
        $info['type'] = $type;
        $this->ok($info);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $info = GoodsModel::with('category_row,category_row.brands,goods_images,spec_goods')->find($id);
        $info['category']=$info['category_row'];
        unset($info['category_row']);
        $type=TypeModel::with('specs,attrs,specs.spec_values')->find($info['type_id']);
        $info['type']=$type;
        $cate_one=\app\common\model\Category::where('pid',0)->select();
        $pid_path=explode('_',$info['category']['pid_path']);
        $cate_two=\app\common\model\Category::where('pid',$pid_path[1])->select();
        $cate_tree=\app\common\model\Category::where('pid',$pid_path[2])->select();
        $typemodel=\app\common\model\Type::select();
        $data=[
            'goods'=>$info,
            'category'=>[
                'cate_one'=>$cate_one,
                'cate_two'=>$cate_two,
                'cate_tree'=>$cate_tree
            ],
            'type'=>$typemodel
        ];
        $this->ok($data);

    }



    public function update(Request $request, $id)
    {
        $parpm=input();
        $valist=$this->validate($parpm,[
            'goods_name|商品名称'=>'require|max:2,10',
            'goods_price|商品价格'=>'require|number',
            'goods_images|商品相册'=>'require|array',
            'goods_logo|商品图片'=>'require',
            'item|商品规格'=>'require|array',
            'attr|商品属性'=>'require|array'
        ]);
        if($valist!==true){
             $this->fail($valist);
        }
        \think\Db::startTrans();
        try{
            if(isset($parpm['goods_logo'])&&is_file('.'.$parpm['goods_logo'])){
                $goods_logo=dirname($parpm['goods_logo']).DS.'thumb_'.basename($parpm['goods_logo']);
                \think\Image::open('.'.$parpm['goods_logo'])->thumb(210,240)->save('.'.$goods_logo);
                $parpm['goods_logo']=$goods_logo;
            }
            $parpm['goods_attr']=json_encode($parpm['attr'],JSON_UNESCAPED_UNICODE);
            GoodsModel::update($parpm,['id'=>$id],true);
            if(isset($parpm['goods_images'])){
                 $goods_images=[];
                 foreach ($parpm['goods_images'] as $image){
                    if(is_file('.'.$image)){
                        dump('5165465');
                        $image_big=dirname($image).DS.'big_'.basename($image);
                        $image_sma=dirname($image).DS.'sma_'.basename($image);
                        $obj=\think\Image::open('.'.$image);
                        $obj->thumb(800,800)->save('.'.$image_big);
                        $obj->thumb(400,400)->save('.'.$image_sma);
                        $row=[
                            'goods_id'=>$id,
                            'pics_big'=>$image_big,
                            'pics_sma'=>$image_sma
                        ];
                        $goods_images[]=$row;
                    }
                }
                $goods_image=new \app\common\model\GoodsImages();
                $goods_image->saveAll($goods_images);
                \app\common\model\SpecGoods::destroy(['goods_id'=>$id]);
                $spec_goods=[];
                foreach($parpm['item'] as $v){
                     $v['goods_id']=$id;
                     $spec_goods[]=$v;
                }
                $spec=new \app\common\model\SpecGoods();
                $spec->saveAll($spec_goods);
                \think\Db::commit();
                $info=GoodsModel::with('category,brand,type')->find($id);
                $this->ok($info);
            }
        }catch (\Exception $e){
            \think\Db::rollback();
            $ee=$e->getMessage();
            $this->fail($ee);
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */

    public function delete($id)
    {
        $info=GoodsModel::find($id);
        if(empty($info)){
            $this->fail('数据异常');
        }
        if($info['is_on_sale']==1){
            $this->fail('无法删除上架商品');
        }
        GoodsModel::destroy($id);
        $this->ok('删除成功');
    }
    public function delpics($id){
        $list=\app\common\model\GoodsImages::find($id);
        if(empty($list)){
            $this->fail('数据异常');
        }
        $list->delete();
        unlink('.'.$list['pics_big']);
        unlink('.'.$list['pics_sma']);
        $this->ok('删除成功');
    }
}
