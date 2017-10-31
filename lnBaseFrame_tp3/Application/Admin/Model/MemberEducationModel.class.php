<?php
namespace Admin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 用户学历管理
 * @author yuanyulin QQ:755687023 liniukeji.com
 *
 */
class MemberEducationModel extends Model{
    
    /**
     * 判断用户学历信息是否保存成功
     * @param array    $data  //需要修改的信息
     * @param int      $id    //member表中的id
     */
    public function memberEducationStatus($data, $id=0) {
//        var_dump($data);
        $rules = array(
            array('start_time', 'require', '开始时间不能为空', 1, 'regex', 3),
            
            array('end_time', 'require', '结束时间不能为空', 1, 'regex', 3),
            
            array('education', 'require', '学历不能为空', 1, 'regex', 3),
            array('education', '1,20', '学历描述文字过长，小于20字', 0, 'length', 3),
            
            array('school_or_company', 'require', '毕业学校或工作单位不能为空', 1, 'regex', 3),
            array('school_or_company', '1,30', '毕业学校或工作单位描述文字过长，小于30字', 0, 'length', 3), 
        );
        //删除原来的信息
        $this->where('member_id='. $id)->delete();
        //将新的信息写入表中
        foreach ($data as $key => $value) {
            $value['member_id'] = $id;
            $value['start_time'] = strtotime($value['start_time']);
            $value['end_time'] = strtotime($value['end_time']);
//            var_dump($value);
//            if ($this->validate($rules)->create($value) == !FALSE) {
            if ($this->create($value) == !FALSE) {
//                var_dump($this->create($value));die;
                $this->add();
            } else {
                return V(0, $memberModel->getError());
            }
        }
        return V(1, '保存成功');
    }   
}
