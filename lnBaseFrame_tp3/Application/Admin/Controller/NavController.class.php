<?php
namespace Admin\Controller;

/**
 * 后台轮播图控制器
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 */
class NavController extends AdminCommonController {

    // 手机APP首页导航
    public function mobileNav(){
        $where['type'] = 0;       // type类型  0表示手机APP首页导航  1表示web导航
        $result = $this->_navList($where);

        $this->assign('type', 0);
        $this->display('index');
    }

    // web导航
    public function webNav(){
        $where['type'] = 1;       // type类型  0表示手机APP首页导航  1表示web导航
        $result = $this->_navList($where);
        
        $this->assign('type', 1);
        $this->display('index');
    }

    // 查询列表
    private function _navList($where){
        $where['status'] = 0;
        $Nav = D('Nav');
        $list = $Nav->navList($where, $field, $order);
        $list = D('Common/Tree')->toFormatTree($list);

        $this->assign('list', $list);
    }

    // 轮播图添加、修改
    public function edit(){
        $type = I('type', -1);
        if ($type == -1) {
            $this->ajaxReturn(V(0, '编辑失败，缺少参数type'));
        }
        $id = I('id', 0, 'intval');     // Nav表的主键id
        $Nav = D('Nav');
        if (IS_POST) {
            if ($_POST['type'] == 1) {
                unset($_POST['img']);
            }
            if ($id == 0) {
                $data = $Nav->create(I('post.'), 1);
                if($data){
                    $Nav->add();
                    $this->ajaxReturn(V(1, '保存成功'));
                } else {
                    $this->ajaxReturn(V(0, $Nav->getError()));
                }
            } else{
                $data = $Nav->create(I('post.'), 2);
                if($data){
                    $Nav->save();
                    $this->ajaxReturn(V(1, '修改成功'));
                } else {
                    $this->ajaxReturn(V(0, $Nav->getError()));
                }
            }
            
        }
        $info = $Nav->detailInfo($id);
        if ($type == 1) {    // 查询web端导航多级目录
            $navCategoryTree = M('Nav')
                                ->where(array('status' => 0, 'type' => 1, 'disabled' => 0, 'parent_id' => 0))
                                ->field('id, title as name, parent_id')
                                ->select();

            $this->assign('navCategoryTree', $navCategoryTree);
        }
        //增加时间戳，处理缓存的问题
        $info['img'] = $info['img'].'?'.rand();
        $this->assign('type', $type);
        $this->assign('info', $info);       
        $this->display();
    }

    public function recycle(){
        $this->_recycle('Nav');
    }

    // 删除图片
    public function delFile(){
        $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }

    // 改变可用状态
    public function changeDisabled(){
        $this->_changeDisabled('Nav');  //调用父类的方法
    }
}
