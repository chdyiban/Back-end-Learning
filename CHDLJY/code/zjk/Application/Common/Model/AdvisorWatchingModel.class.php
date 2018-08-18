<?php
namespace Common\Model;

use Think\Model;

class AdvisorWatchingModel extends Model
{
	protected $tableName = 'advisor_watching';

    public function getWatchingIssueList($advisor_id){

    	$map['status'] = 1;
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

    /**
    * 检查项目详情页“关注该项目”button是否亮起 
    * 1.is_advisor 当前用户为指导老师，则可关注项目
    * 2.关注表里存在,且status=1,则代表已经关注
    * 3.关注表里存在，且status=0,则代表曾经取消关注，可以再次关注,status更新为1即可
    */
    public function check_advisor_watching_auth($issue_id){
        $user_auth = session('user_auth');
        //当前登录用户ID $user_auth['uid'];
        // $map['status'] = 0;

        if(!is_advisor($user_auth['uid'])){
            return 0;
        }

        $map['advisor_id'] = $user_auth['uid'];
        $map['issue_id'] = $issue_id;

        $check_info = $this->where($map)->find();

        if($check_info['status'] == 1){
            //已关注，亮起,可显示取消button
            $result = 2;
        }else{
            //可以点关注
            $result = 1;
        }
        return $result;
    }

    public function watching($issue_id){

        $map['advisor_id'] = is_login();
        $map['issue_id'] = $issue_id;

        $check_info = $this->where($map)->find();

        if(!$check_info){
            $data['advisor_id'] = is_login();
            $data['issue_id'] = $issue_id;
            $data['status'] = '1';
            if($this->add($data)){
                return true;
            }
        }elseif($check_info['status'] == '0'){
            if($this->where($map)->setField('status','1')){
                return true;
            }
        }else{
            return false;
        }
    }

    public function cancelWatching($issue_id){
        $map['status'] = '1';
        $map['advisor_id'] = is_login();
        $map['issue_id'] = $issue_id;

        $check_info = $this->where($map)->find();
        if($check_info && $this->where($map)->setField('status','0')){
            return true;
        }else{
            return false;
        }
    }
}