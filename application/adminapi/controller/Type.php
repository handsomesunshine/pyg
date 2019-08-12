<?php

namespace app\adminapi\controller;

use app\admin\model\Type as TypeModel;
use app\common\model\Spec;
use think\Request;

class Type extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $info = TypeModel::field('id,type_name')->select();
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
        $parpms = input();
        $vaildate = $this->validate($parpms, [
            'type_name|模型名称' => 'require|max:20',
            'spec|规格数组' => 'require|array',
            'attr|属性数组' => 'require|array'
        ]);
        if ($vaildate !== true) {
            $this->fail($vaildate);
        }
        \think\Db::startTrans();
        try {
            $type = TypeModel::create($parpms, true);
            foreach ($parpms['spec'] as $i => $spec) {
                if (trim($spec['name']) == '') {
                    unset($parpms[$i]);
                } else {
                    foreach ($spec['value'] as $k => $value) {
                        if (trim($value) == '') {
                            unset($parpms['spec'][$i]['value'][$k]);
                        }
                        if (empty($spec['value'])) {
                            unset($parpms['spec'][$i]);
                        }
                    }
                }
            }
            $specs = [];
            foreach ($parpms['spec'] as $spec) {
                $row = [
                    'type_id' => $type['id'],
                    'spec_name' => $spec['name'],
                    'sort' => $spec['sort']
                ];
                $specs[] = $row;
            }
            $spec_model = new \app\common\model\Spec();
            $spec_data = $spec_model->saveAll($specs);
            $spec_value = [];
            foreach ($parpms['spec'] as $i => $spec) {
                foreach ($spec['value'] as $k => $v) {
                    $list = [
                        'spec_id' => $spec_data[$i]['id'],
                        'spec_value' => $v,
                        'type_id' => $type['id']
                    ];
                    $spec_value[] = $list;
                }
            }
            $valueModel = new \app\common\model\SpecValue();
            $values = $valueModel->saveAll($spec_value);
            foreach ($parpms['attr'] as $k => $value) {
                if (trim($value['name']) == '') {
                    unset($parpms['attr'][$k]);
                } else {
                    foreach ($value['value'] as $i => $v) {
                        if (trim($v) == '') {
                            unset($parpms['attr'][$k]['value']['i']);
                        }
                    }
                }
            }
            $attr_arr = [];
            foreach ($parpms['attr'] as $k => $val) {
                $row = [
                    'attr_name' => $val['name'],
                    'type_id' => $type['id'],
                    'sort' => $val['sort'],
                    'attr_values' => implode(',',$val['value'])
                ];
                $attr_arr[] = $row;
            }
            $attr = new \app\common\model\Attribute();
            $attr->saveAll($attr_arr);
            \think\Db::commit();
            $info = TypeModel::find($type['id']);
            $this->ok($info);
        } catch (\Exception $e) {
            \think\Db::rollback();
//            $ee=$e->getMessage();
//            $this->fail($ee);
            $this->fail('添加失败');
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
        $list = TypeModel::with('specs,specs.spec_values,attrs')->find($id);
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
        $parpms = input();
        $valist = $this->validate($parpms, [
            'type_name|模型名称' => 'require|max:20',
            'spec|规格数组' => 'require|array',
            'attr|属性数组' => 'require|array'
        ]);
        if ($valist !== true) {
            $this->fail($valist);
        }
        \think\Db::startTrans();
        try {
            TypeModel::update(['type_name' => $parpms['type_name']], ['id' => $id], true);
            foreach ($parpms['spec'] as $i => $spec) {
                if (trim($spec['name']) == '') {
                    unset($parpms['spec'][$i]);
                } else {
                    foreach ($spec['value'] as $k => $value) {
                        if (trim($value) == '') {
                            unset($parpms['spec'][$i]['value'][$k]);
                        }
                    }
                    if (empty($spec['value'])) {
                        unset($parpms['spec'][$i]);
                    }
                }
            }

            $specs = [];
            foreach ($parpms['spec'] as $i => $spec) {
                $row = [
                    'type_id' => $id,
                    'spec_name' => $spec['name'],
                    'sort' => $spec['sort']
                ];
                $specs[] = $row;
            }
            \app\common\model\Spec::destroy(['type_id' => $id]);
            $spec_model = new \app\common\model\Spec();
            $spec_data = $spec_model->saveAll($specs);
            $spval = [];
            foreach ($parpms['spec'] as $i => $spec) {
                foreach ($spec['value'] as $k => $value) {
                    $row = [
                        'spec_id' => $spec_data[$i]['id'],
                        'spec_value' => $value,
                        'type_id' => $id
                    ];
                    $spval[] = $row;
                }
            }
            \app\common\model\SpecValue::destroy(['type_id' => $id]);
            $spmodel = new \app\common\model\SpecValue();
            $spmodel->saveAll($spval);
            foreach ($parpms['attr'] as $i => $attr) {

                if (trim($attr['name']) == '') {
                    unset($parpms['attr'][$i]);
                } else {
                    foreach ($attr['value'] as $k => $value) {
                        if (trim($value) == '') {
                            unset($parpms['attr'][$i]['value'][$k]);
                        }
                    }
                }
            }
            $attlist = [];
            foreach ($parpms['attr'] as $k => $attr) {
                $row = [
                    'attr_name' => $attr['name'],
                    'type_id' => $id,
                    'attr_values' => implode(',',$attr['value']),
                    'sort' => $attr['sort']
                ];
                $attlist[] = $row;
            }
            \app\common\model\Attribute::destroy(['type_id' => $id]);
            $attmodel = new \app\common\model\Attribute();
             $aa=$attmodel->saveAll($attlist);
            \think\Db::commit();
            $info = TypeModel::find($id);
            $this->ok($info);
        } catch (\Exception $e) {
            \think\Db::rollback();
            $ee = $e->getMessage();
            $this->fail($ee);
//            $this->fail('修改成功');
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
        $goods = \app\common\model\Goods::where('type_id', $id)->find();
        if ($goods) {
            $this->fail('正在使用中，不能删除');
        }
        \think\Db::startTrans();
        try {
            TypeModel::destroy($id);
            \app\common\model\Spec::destroy(['type_id'=>$id]);
            \app\common\model\SpecValue::destroy(['type_id'=>$id]);
            \app\common\model\Attribute::destroy(['type_id'=>$id]);
            $this->ok();
        } catch (\Exception $e) {
            \think\Db::rollback();
            $this->fail();
        }
    }
}
