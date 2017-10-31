<?php
namespace Admin\Controller;

/**
 * 文章管理控制器
 * @author yuanyulin <755687023@qq.com>
 */
class ArticleController extends AdminCommonController {
    //文章管理列表
    public function index() {  
        $category_id = I('category_id', '');      
        $data = D("Article")->search();
        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }
        $ArticleCategory=D('ArticleCategory');
        $where['status'] = 0;
        $info=$ArticleCategory->field(true)->where($where)->order('sort, id')->select(); 
        //获取树形结构
        $typeData = D('Common/Tree')->toFormatTree($info);
        $this->assign('typeData',$typeData);
        $this->assign('category_id',$category_id);
        $this->display();
    }
    //文章管理的添加与编辑
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = D('Article');
            if($data->create() !== false){
                if ($id > 0) {                   
                    $v = $data->where('id='. $id)->save();
                } else {
                    $data->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, $data->getError()));
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Article')->field(true)->find($id);
            if(false === $info){
                $this->error('分类信息错误');
            }

            $ArticleCategoryTree = D('ArticleCategory')->getArticleCategoryTree($id);

            $this->assign('ArticleCategoryTree', $ArticleCategoryTree);
            $this->assign('info', $info);
            
            $this->display();
        }
    }
    // 放入回收站
    public function recycle(){
        $this->_recycle('Article');  //调用父类的方法
    }

    // 删除图片
    public function delFile(){
        $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }
    
}