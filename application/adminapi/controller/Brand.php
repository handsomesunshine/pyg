<?php

namespace app\adminapi\controller;

use app\common\model\Brand as BrandModel;
use think\Request;

class Brand extends BaseApi
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
        if (isset($parpm['cate_id']) && !empty($parpm['cate_id'])) {
            $where['cate_id'] = $parpm['cate_id'];
            $list = BrandModel::where($where)->field('id,name')->select();
        } else {
            if (isset($parpm['keyword']) && !empty($parpm['keyword'])) {
                $keyword = $parpm['keyword'];
                $where['name'] = ['like', "%$keyword%"];
            }
            $list = BrandModel::alias('b')->join('category c', 'b.cate_id=c.id', 'left')->field('b.*,c.cate_name')->where($where)->select();
        }
        $this->ok($list);
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
        $vatlist = $this->validate($parpm, [
            'name' => 'require',
            'cate_id' => 'require',
            'is_hot' => 'require',
            'sort' => 'require',
        ]);
        if ($vatlist !== true) {
            $this->fail($vatlist);
        }
        if (isset($parpm['logo']) && !empty($parpm['logo']) && is_file('.' . $parpm['logo'])) {
            \think\Image::open('.' . $parpm['logo'])->thumb(200, 100)->save('.' . $parpm['logo']);
        }
        $list = BrandModel::create($parpm, true);
        $info = BrandModel::find($list['id']);
        $this->ok($info);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $list = BrandModel::find($id);
        $this->ok($list);
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $parpm = input();
        $valist = $this->validate($parpm, [
            'name' => 'require',
            'cate_id' => 'require',
            'is_hot' => 'require',
            'sort' => 'require',
        ]);
        if ($valist !== true) {
            $this->fail($valist);
        }
        if (isset($parpm['logo']) && !empty($parpm['logo']) && is_file('.' . $parpm['logo'])) {
            \think\Image::open('.' . $parpm['logo'])->thumb('200', '100')->save('.' . $parpm['logo']);
        }
        if (isset($parpm['desc']) || isset($parpm['logo']) || isset($parpm['url'])) {

        }
        BrandModel::update($parpm, ['id' => $id], true);
        $list = BrandModel::find($id);
        $this->ok($list);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $total = \app\common\model\Goods::where('brand_id', $id)->count();
        if ($total > 0) {
            $this->fail('品牌下有商品，不能删除');
        }
        BrandModel::destroy($id);
        $this->ok();
    }
}
