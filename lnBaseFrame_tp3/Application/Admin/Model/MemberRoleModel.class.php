<?php
namespace Admin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 用户角色管理
 * @author yuanyulin QQ:755687023 liniukeji.com
 *
 */
class MemberRoleModel extends Model{
    
    /**
     * 判断用户角色是否保存成功
     * @param array    $data  //需要修改的信息
     * @param int      $id    //member表中的id
     */
    public function memberRoleStatus($data, $id=0) {
        //删除原来的信息
        $this->where('member_id='. $id)->delete();
        //将新的信息写入表中
        $data = explode(',', $data);
        foreach ($data as $key => $value) {
            $role_data['member_id'] = $id;
            $role_data['role_id'] = $value;
            if ($this->create($role_data) == !FALSE) {
                $this->add();
            } else {
                return V(0, $this->getError());
            }
        }
        return V(1, '保存成功');
    }   
}
