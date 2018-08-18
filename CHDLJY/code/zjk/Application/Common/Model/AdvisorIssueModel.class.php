<?php
namespace Common\Model;

use Think\Model;

class AdvisorIssueModel extends Model
{
	protected $tableName = 'advisor_invite';
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT));


    public function getIssueList($advisor_id,$status = 1){

    	$map['status'] = $status;
    	$map['advisor_id'] = $advisor_id; 
    	$issueIdList = $this->where($map)->select();
    	$issue_content = array();
    	foreach ($issueIdList as $key => $value) {
    		//1.查找项目信息
    		$issue_temp = query_issue_infomation($value['issue_id']);
    		$issue_content[$key]['id'] = $issue_temp['id'];
    		$issue_content[$key]['title'] = $issue_temp['title'];
    		$issue_content[$key]['cover_id'] = $issue_temp['cover_id'];
    		$issue_content[$key]['uid'] = $issue_temp['uid'];
    		$issue_content[$key]['tc_name'] = $issue_temp['tc_name'];
  
    	}
    	return $issue_content;
    }
    //项目存在且status = 1 即项目已经有1名指导老师
    public function checkIssueExist($advisor_id,$issue_id){

        // $map['status'] = 1;
        $map['issue_id'] = $issue_id;
        $map['advisor_id'] = $advisor_id;

        $check_info = $this->where($map)->find();

        if($check_info['status'] === '0'){
            $result['status'] = true;

        }elseif($check_info['status'] === '1'){
            $result['status'] = false;
            $result['msg'] = '该项目已经有1名指导老师';
        }else{
            $result['status'] = false;
            $result['msg'] = '该项目尚未邀请指导';
        }

        return $result;
    }

    public function saveInviteInfo($advisor_id,$issue_id){

        $map['status'] = 0;
        $map['issue_id'] = $issue_id;
        $map['advisor_id'] = $advisor_id;

        if($this->where($map)->setField('status','1')){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    //检查项目详情页“同意指导”button是否亮起 return bool or false
    public function check_advisor_auth($issue_id){
        $user_auth = session('user_auth');
        //当前登录用户ID $user_auth['uid'];
        // $map['status'] = 0;
        $map['advisor_id'] = $user_auth['uid'];
        $map['issue_id'] = $issue_id;

        $check_info = $this->where($map)->find();
        //dump($this->getLastSql());
        if($check_info){
            if($check_info['status'] == 0){
                //未指导，正确亮起同意button
                $result = 1;
            }else{
                //已指导，亮起,可显示取消button
                $result = 2;
            }
        }else{
            $result = 0;
        }
        return $result;

    }
}