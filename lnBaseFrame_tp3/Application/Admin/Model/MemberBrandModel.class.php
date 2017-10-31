<?php
namespace Admin\Model;
use Think\Model;
/**
 * 用户负责品牌管理
 * @author liuyang QQ:594353482
 *
 */
class MemberBrandModel extends Model{
    
   /**
     * 更新用户负责药品品牌信息
     */
    public function updateMemberBrand($member_id, $brand_ids){
        $where['member_id']=$member_id;
        M('MemberBrand')->where($where)->delete();
        $data['member_id'] = $member_id;
        $brand_ids = explode(',', $brand_ids);
        foreach ($brand_ids as $key => $value) {
            $data['brand_id'] = $value;
            M('MemberBrand')->add($data);
        }
    }

}
