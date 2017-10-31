<?php
namespace Common\Model;
use Think\Model;
/**
 *  文章列表模型
 *  @author yuanyulin QQ:755687023
 *
 */
class ArticleModel extends Model{
    protected $insertFields = array('name','introduce','content','add_time','category_id','display','photo_path','url','sort');
    protected $updateFields = array('id','name','introduce','content','add_time','category_id','display','photo_path','url','sort');
    protected $selectFields = array('id','name','category_id','add_time','click_count','display');
    
    protected $_validate = array(
        array('name','require','文章标题名称不能为空', self::MUST_VALIDATE),
        array('name', '1,20', '文章标题名称不能超过20个字符', self::MUST_VALIDATE, 'length', 3),
        array('category_id','require','文章分类必须选择', self::MUST_VALIDATE),
        array('introduce','require','文章简介内容不能为空', self::MUST_VALIDATE),
        array('introduce', '1,200', '文章简介内容不能超过200', self::MUST_VALIDATE, 'length', 3),
        array('content','require','文章内容不能为空', self::MUST_VALIDATE),
    );

    // 获得文章分类详情
    public function search(){        
        $name = I('get.name', '','trim');     
        $map['status'] = 0;
        if($name) {
            $map['name'] = array('like',"%$name%");
        }
        $category_id = I('category_id', '');
        if($category_id) {
            $map['category_id'] = array('eq',$category_id);
        }
        $data = $this->getArticleByPage($map, $field=null, $order='add_time desc');
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }

    protected function checkName($data){
        $id = I('get.id', 0, 'intval');
        $where['name'] = $data;
        $where['status']= array('eq',0);
        if ($id > 0) {
            $where['id'] = array('neq', $id );
        }
        $count = $this->where($where)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }
    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
    */
    public function getArticleByPage($where, $field=null, $order='sort, id'){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);
        
        $data = $this->field($field)->where($where)->limit($page['limit'])->order($order)->select();

        $articleCategoryData = D('ArticleCategory')->field('id,name')->order('sort')->select();

        foreach ($data as $key => $value) {
            foreach ($articleCategoryData as $k => $v) {
                if($value['category_id'] == $v['id']){
                    $data[$key]['category_name'] = $v['name'];
                    break;
                }   
            }
        }
        return array(
            'data' => $data,
            'page' => $page['page']
        );   
    }
}
