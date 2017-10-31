<?php
namespace Admin\Controller;

/**
 * 后台控制器
 * @author songwei <QQ:837686983>
 */
class PositionController extends AdminCommonController {

    public function index(){
     
        $positionModel = M('Position');
        $where['status'] = 0;
        $info = $positionModel->where($where)->field(true)->order('sort,id')->select();
        $position_list = D('Common/Tree')->toFormatTree($info);
        $this->assign('list',$position_list);     
        $this->display();    
    }

    public function searchByPinyin(){
        getDataByKeywords('Position');
    }

    public function edit($id = 0){

        $id = I('id', 0, 'intval');
        //IS_POST为真是添加/修改操作，为假表示是展示分配数据操作
        if (IS_POST) { 
            $positionModel = D('Position');
            if ($positionModel->create() !== false) {
                if ($id > 0) {
                    if($positionModel->where('id='. $id)->save()===false){
                        $this->ajaxReturn(V(0,'上级职务选择不合法'));
                    }
                } else {
                    $positionModel->add();
                }
                $this->ajaxReturn(V(1,'保存成功!'));
            } else {
                $this->ajaxReturn(V(0, $positionModel->getError()));
            }
        } else { 

            $info = array();
            /* 获取数据 */
            $position = M('Position');
            $info = $position->field(true)->find($id);

            $position_list = D('Position')->getPositionTree();

            //查询出上级的数据
            $parent_info = $position->where('id='.$info['parent_id'])->field(true)->find();
            
            $this->assign('position', $position_list);
            $this->assign('info', $info);
            $this->assign('parent_info', $parent_info);   
            $this->display();
        }
    }
    public function recycle(){
        $id = I('id', 0);
        if ($id == 0) {
            $this->ajaxReturn(V(0, '删除失败, 未知id'));
        }
        //判断是否存在下级
        $where['status'] = 0;
        $where['position_id'] = $id;
        $count = M('Member')->where($where)->count();
        if ($count > 0) {
            $this->ajaxReturn(V(0, '无法删除, 该职务存在关联用户。'));
        }
        unset($where);
        $where['status'] = 0;
        $where['parent_id'] = $id;
        $count = M('Position')->where($where)->count();
        if ($count > 0) {
            $this->ajaxReturn(V(0, '无法删除, 该职务存在下级职务。'));
        }
        $this->_recycle('position');
    }

    

    /**
     * 职务树数据获取(用于在弹窗选择人员时获取左侧的职务树展示)
     * @author liuyang 594353482@qq.com
     */
    public function getPostTreeData(){
        $positionModel = M('Position');
        $where['status'] = 0;
        $info = $positionModel->where($where)->field('id,parent_id,name')->order('sort,id')->select();
        $Tree = new \Common\Tools\BuildTreeArray($info,'id','parent_id','0');
        $data = $Tree->getTreeArray();
        $this->ajaxReturn($data);
    }

}
