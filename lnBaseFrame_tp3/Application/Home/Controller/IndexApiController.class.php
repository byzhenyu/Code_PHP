<?php
namespace Home\Controller;

/**
 * APP首页控制器
 * create by zhaojiping <QQ:17620286>
 */
class IndexApiController extends \Common\Controller\CommonApiController {

    // 首页文章列表
    public function articleList(){
        $type = I('type', 0, 'intval');    //type 首页不同的文章列表 1表示政策解读 2表示销售技巧 3法律法规
        $keywords = I('keywords', '', 'htmlspecialchars');
        if ($keywords) {
            $where['name'] = array('like', '%'.$keywords.'%');
        }
        if ($type == 1) {
            $where['category_id'] = 13;
        } elseif ($type == 2) {
            $where['category_id'] = 14;
        } elseif ($type == 3) {
            $where['category_id'] = 15;
        } elseif ($type == 4) {
            $where['category_id'] = 12;
        } elseif ($type == 5){
            $where['category_id'] = 26;
        }
        $field = 'id, name, introduce, url, click_count, photo_path';
        $data = D('Article/Article')->ArticleList($where, $field);
        $data = $data['data'];
        $this->apiReturn(1, '文章列表', $data);
    }

    // 文章详情页面
    public function articleDetail(){
        $id = I('article_id', 0, 'intval');
        // if ($id == 0) {
        //     $this->apiReturn(0, '非法文章id');
        // }
        $where['id'] = $id;
        $field = 'id, name, introduce, content, click_count,add_time';
        $info = D('Article/Article')->ArticleInfo($where, $field);

        $this->assign('info', $info);
        $this->display('articleDetail');
    }

    // braner轮播图点击详情页面
    public function defineDetail(){
        $id = I('id', 0, 'intval');
        // if ($id == 0) {
        //     $this->apiReturn(0, '非法文章id');
        // }
        $where['id'] = $id;
        $field = 'id, title as name, content';
        $info = M('Banner')->field($field)->where($where)->find($id);

        $this->assign('info', $info);
        $this->display('defineDetail');
    }

}
