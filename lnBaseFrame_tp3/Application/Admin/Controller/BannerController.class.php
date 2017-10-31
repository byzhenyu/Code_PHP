<?php
namespace Admin\Controller;

/**
 * 后台轮播图控制器
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 */
class BannerController extends AdminCommonController {

    // 手机启动页管理
    public function mobileStartPicture(){
        $where['type'] = 0; // type轮播图类型  0表示手机APP启动页图片列表  1表示手机APP轮播图图片列表  2手机登录页背景图片  
        $result = $this->_bannerList($where);

        $this->assign('type', 0);
        $this->display('index');
    }

    // 手机轮播图管理
    public function mobileBannerPicture(){
        $where['type'] = 1; // type轮播图类型  0表示手机APP启动页图片列表  1表示手机APP轮播图图片列表  2手机登录页背景图片  
        $result = $this->_bannerList($where);

        $this->assign('type', 1);
        $this->display('index');
    }

    // 手机登录背景图管理
    public function mobileLoginPicture(){
        $where['type'] = 2; // type轮播图类型  0表示手机APP启动页图片列表  1表示手机APP轮播图图片列表  2手机登录页背景图片  
        $result = $this->_bannerList($where);

        $this->assign('type', 2);
        $this->display('index');
    }

    // 根据条件查询banner图列表
    private function _bannerList($where){
        $where['status'] = 0;
        $Banner = D('Banner');
        $list = $Banner->bannerList($where);

        $this->assign('list', $list);
    }

    // 轮播图添加、修改
    public function edit(){
        $type = I('type');
        $id = I('id', 0, 'intval');     // banner表的主键id
        $Banner = D('Banner');
        if (IS_POST) {
            if ($_POST['open_type'] == 1) {
                unset($_POST['article_id'], $_POST['content']);
            }
            if ($_POST['open_type'] == 2) {
                unset($_POST['url'], $_POST['content']);
            }
            if ($_POST['open_type'] == 3) {
                unset($_POST['url'], $_POST['article_id']);
            }

            if ($id == 0) {
                $data = $Banner->create(I('post.'), 1);
                if($data){
                    $Banner->add();
                    $this->ajaxReturn(V(1, '保存成功'));
                } else {
                    $this->ajaxReturn(V(0, $Banner->getError()));
                }
            } else{
                $data = $Banner->create(I('post.'), 2);
                if($data){
                    $Banner->save();
                    $this->ajaxReturn(V(1, '修改成功'));
                } else {
                    $this->ajaxReturn(V(0, $Banner->getError()));
                }
            }
        }
        $info = $Banner->detailInfo($id);
        if ($info['open_type'] == 2) {
            $articleInfo = M('Article')->where(array('id' => $info['article_id']))->field('name')->find();
            $info['articleName'] = $articleInfo['name'];
        }
        
        $this->assign('type', $type);
        $this->assign('info', $info);       
        $this->display();
    }

    public function recycle(){
        $this->_recycle('Banner');
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
        $this->_changeDisabled('Banner');  //调用父类的方法
    }

    // ajax设置为默认启动页
    public function ajaxChangeIsDefault(){
        if (IS_AJAX) {
            $id = I('id', 0, 'intval');
            $type = I('type', -1, 'intval');
            if ($type == -1) {
                $this->ajaxReturn(V(0, '修改失败, 需要type'));
            }
            $where['type'] = $type;
            $map['id'] = $id;
            M('Banner')->where($where)->setField('is_default', 1);
            M('Banner')->where($map)->setField('is_default', 0);
            $this->ajaxReturn(V(1, '修改成功'));
        }
    }
}
