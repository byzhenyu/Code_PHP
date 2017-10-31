<?php
namespace Admin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 用户证件管理
 * @author yuanyulin QQ:755687023 liniukeji.com
 *
 */
class MemberCertificatesModel extends Model{
    
    /**
     * 判断用户学历信息是否保存成功
     * @param array    $data  //需要修改的信息
     * @param int      $id    //member表中的id
     */
    public function memberCertificatesStatus($data, $id=0) {
//        var_dump($data);
        $rules = array(
            array('type', 'require', '关系类型不能为空', 1, 'regex', 3),
            
            array('name', 'require', '姓名不能为空', 1, 'regex', 3),
            
            array('education', 'require', '学历不能为空', 1, 'regex', 3),
            
            array('call_name', 'require', '称呼不能为空', 1, 'regex', 3),
        );
        //删除原来的信息
        $this->where('member_id='. $id)->delete();
        //将新的信息写入表中
        foreach ($data as $key => $value) {
            $value['member_id'] = $id;
//            var_dump($value);
//            if ($this->validate($rules)->create($value) == !FALSE) {
            if ($this->create($value) == !FALSE) {
                $this->add();
            } else {
                return V(0, $this->getError());
            }
        }
        return V(1, '保存成功');
    }   
}
