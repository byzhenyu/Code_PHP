<?php
/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description    区域控制器
 * @Author         谢有辉 QQ：565356915
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2017.12.21完成
 * @CreateBy       PhpStorm
 */

namespace Admin\Controller;

class RegionController extends CommonController {
    // 省级列表
    public function list_region() {
        if (IS_AJAX) {
            $keyword = I('keyword', '', 'trim');

            $map['parent_id'] = 0;
            if ($keyword) {
                $map['name'] = array('like', '%' . $keyword . '%');
            }

            $Region = M('region', 'ln_shop_');
            $count = $Region->where($map)->count();
            $page = get_page($count);
            $list = $Region->where($map)->order(array('sorting desc', 'id'))->limit($page['limit'])->select();

            $this->assign('keyword', $keyword);
            $this->assign('page', $page['page']);
            $this->assign('list', $list);
            $this->display('ajax_list_region');
        } else {
            $this->display('list_region');
        }

    }

    // 下级区域列表
    public function level_region_list() {
        $parent_id = I('parent_id', 0, 'intval');
        if ($parent_id > 0) {
            S('parent_id', $parent_id);
        }

        if (IS_AJAX) {
            $parent_id = S('parent_id');

            if (!$parent_id) {
                $this->ajaxReturn(V(0, '请选择需要管理的地区'));
            }

            $keyword = I('keyword', '', 'trim');
            if ($keyword) {
                $map['name'] = array('like', '%' . $keyword . '%');
            }

            $Region = M('region', 'ln_shop_');
            $map['parent_id'] = $parent_id;
            $count = $Region->where($map)->count();
            $page = get_page($count);
            $list = $Region->where($map)->order(array('sorting desc', 'id desc'))->limit($page['limit'])->select();

            $this->assign('keyword', $keyword);
            $this->assign('list', $list);
            $this->display('ajax_level_region_list');
        } else {
            $parent['parent_id'] = $parent_id;
            $result = M('region', 'ln_shop_')->where(array('id' => $parent_id))->find();
            if ($result) {
                $parent['level'] = $result['level'];
            }

            $this->assign('parent', $parent);
            $this->display('level_region_list');
        }

    }

    // 批量添加区域数据
    public function add() {
        $name = I('name');
        $level = I('level', 0, 'intval');
        $parent_id = I('parent_id');

        if (empty($name)) {
            $this->ajaxReturn(V(0, '请填写地区名称'));
        }
        $name = explode('、', $name);
        if (count($name) != count(array_unique($name))) {
            $this->ajaxReturn(V(0, '所要添加的区域中名称有重复'));
        }

        if ($parent_id > 0) {
            S('parent_id', $parent_id);
        }
        if ($level > 0) {
            S('level', $level);
        }
        if (!$parent_id) {
            $parent_id = S('parent_id');
            $level = S('level');
        }

        if ($parent_id == -1) {
            $parent_id = 0;
        }

        $Region = M('region', 'ln_shop_');
        $where = array('parent_id' => $parent_id, 'name' => array('in', $name));
        $res = $Region->where($where)->find();
        if (empty($res)) {
            $dataList = array(
                'parent_id' => $parent_id,
                'level' => $level + 1,
            );

            foreach ($name as $key => $value) {
                $dataList['name'] = $value;
                $Region->add($dataList);
            }
            $this->ajaxReturn(V(1, '添加成功'));
        } else {
            $this->ajaxReturn(V(0, '区域名称有重复'));
        }

    }

    // 修改区域信息
    public function edit() {
        $id = I('id');

        if (empty($id)) {
            $this->ajaxReturn(V(0, '请选择需要操作的数据'));
        }

        $Region = M('region', 'ln_shop_');
        $region_info = $Region->find($id);

        if (IS_POST) {
            $name = I('name');
            $sorting = I('sorting', 0, 'intval');

            if (!$name) {
                $this->ajaxReturn(V(0, '请填写区域名称'));
            }

            // 单独修改区域名称
            $map = array('parent_id' => $region_info['parent_id'], 'name' => $name, 'id' => array('neq', $id));
            $count = $Region->where($map)->count();
            if ($count > 0) {
                $this->ajaxReturn(V(0, '该等级下已有该区域，请换个名称'));
            }

            $data = array('sorting' => $sorting, 'name' => $name);
            $Region->where(array('id' => $id))->data($data)->save();

            $this->ajaxReturn(V(1, '修改成功'));

        }

        $this->assign('list', $region_info);
        $this->display('edit_region');
    }

    // 删除和清空区域
    public function delRegion() {
        $parent_id = I('parent_id'); //用于清空当前等级的区域数据
        $id = I('id'); // 单条删除

        $Region = M('region', 'ln_shop_');

        if ($parent_id > 0) {
            // 批量删除：查询是否有数据
            $where['parent_id'] = $parent_id;
            $count = $Region->where($where)->count();
            if ($count == 0) {
                $this->ajaxReturn(V(0, '没有数据，无需清空'));
            }

            // 清空该区域下当前等级的数据
            $result = $Region->where($where)->delete();
            if (!$result) {
                $this->ajaxReturn(V(0, '操作失败'));
            }

            $this->ajaxReturn(V(1, '操作成功'));
        }

        // 检查当前是否有下级
        $count = $Region->where(array('parent_id' => $id))->count();
        if ($count > 0) {
            $this->ajaxReturn(V(0, '该区域有下级区域，禁止删除'));
        }
        $Region->where("id=$id")->delete();

        $this->ajaxReturn(V(1, '操作成功'));
    }

}