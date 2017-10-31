<?php
namespace Home\Controller;

/**
 * 首页控制器
 * create by zhaojiping <QQ:17620286>
 */
class IndexController extends \Common\Controller\CommonWebController {

    public function index(){
        // 获取主导航条
        $mainNav = D('Basic/Nav')->getWebNavList();
        $this->minaNav = $mainNav;
        $this->display();
    }

    // 首页文章列表
    public function articleList(){
    	$type = I('type', 12, 'intval');    //type 首页不同的文章列表 1表示政策解读 2表示销售技巧 3法律法规
    	$current_id = I('current_id');
    	$main_nav_id = I('main_nav_id');
		if ($type == 1) {
    		$where['category_id'] = 13;
    	} else {
    		if ($type == 2) {
	    		$where['category_id'] = 14;
	    	} else {
	    		if ($type == 3) {
		    		$where['category_id'] = 15;
		    	}
	    	}
    	}
        if ($type == 1) {
            $where['category_id'] = 13;
        } elseif ($type == 2) {
            $where['category_id'] = 14;
        } elseif ($type == 3) {
            $where['category_id'] = 15;
        } elseif ($type == 4) {
            $where['category_id'] = 12;
        } elseif ($type == 5) {
            $where['category_id'] = 26;
        }
    	$field = 'id, name, url, add_time';
    	$data = D('Article/Article')->ArticleList($where, $field);

    	$this->assign('current_id', $current_id);
    	$this->assign('main_nav_id', $main_nav_id);
    	$this->assign('data', $data['data']);
    	$this->assign('page', $data['page']['page']);
    	$this->display('articleList');
    }

    // 文章详情页面
    public function articleDetail(){
    	$id = I('article_id', 0, 'intval');
    	if ($id == 0) {
    		echo "非法文章id";
    		exit;
    	}
    	$where['id'] = $id;
    	$field = 'id, name, introduce, content, click_count, add_time';
    	$info = D('Article/Article')->ArticleInfo($where, $field);

    	$this->assign('info', $info);
    	$this->display('articleDetail');
    }

}
