	// 收货地址三级联动，随便复制到一个接口控制器中
    public function getRegionList() {
        $parent_id = I('parent_id', 0, 'intval'); // 手机端打开的第一个界面，查询省级区域parent_id不用传

        $list = M('Region')->where(array('parent_id' => $parent_id))->field('id, name, parent_id')->select();

        $this->ajaxReturn(V(1, '三级联动', $list));
    }