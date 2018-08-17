<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-27
 * Time: 下午1:54
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Ucenter\Controller;


use Think\Controller;

class IndexController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $uid = isset($_GET['uid']) ? op_t($_GET['uid']) : is_login();
        //调用API获取基本信息
        $this->userInfo($uid);
        $this->_fans_and_following($uid);

        $this->_tab_menu();

    }

    public function index($uid = null,$page=1)
    {

        $show_tab= get_kanban_config('UCENTER_KANBAN', 'enable','', 'USERCONFIG');
        $menu=$this->_tab_menu();
        foreach($show_tab as $v1) {
            foreach($menu as $v2) {
                if (array_search($v1,$v2)) {
                    $arr3[$v1] = $v2;
                }
            }
        }
        unset($v1);unset($v2);
        $appArr =$arr3;
        $current_action=current($appArr);
        $url_link=array(
            'info'=>'Ucenter/Index/information',
            'rank_title'=>'Ucenter/Index/rank',
            'follow'=>'Ucenter/Index/following',
        );
        if(!$current_action){
            $this->redirect('Ucenter/Index/information', array('uid' => $uid));
        }
        if (in_array($current_action['data-id'],array('info','rank_title','follow'))) {
            $this->redirect($url_link[$current_action['data-id']], array('uid' => $uid));
        }
        $type=key($appArr);
        if (!isset ($appArr [$type]))
        {
            $this->error(L('_ERROR_PARAM_').L('_EXCLAMATION_').L('_EXCLAMATION_'));
        }

        $this->assign('type', $type);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->assign('page',$page);

        //四处一词 seo
        $str = '{$user_info.name|text}';
        $str_app = '{$appArr.'.$type.'.title|text}';
        $this->setTitle($str . L('_INDEX_TITLE_'));
        $this->setKeywords($str . L('_PAGE_PERSON_') . $str_app);
        $this->setDescription($str . L('_DE_PERSON_') . $str_app . L('_PAGE_'));
        //四处一词 seo end
        $this->display();
    }



    private function userInfo($uid = null)
    {
        $user_info = query_user(array('avatar128', 'name', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'weibocount', 'rank_link', 'signature'), $uid);
        //获取用户封面id
        $map=getUserConfigMap('user_cover','',$uid);
        $map['role_id']=0;
        $model=D('Ucenter/UserConfig');
        $cover=$model->findData($map);
        $user_info['cover_id']=$cover['value'];
        $user_info['cover_path']=getThumbImageById($cover['value'],1140,230);
        $user_info['tags']=D('Ucenter/UserTagLink')->getUserTag($uid);
        $this->assign('user_info', $user_info);
        return $user_info;
    }

    public function information($uid = null)
    {
        //调用API获取基本信息
        //TODO tox 获取省市区数据
        $user = query_user(array('name', 'signature', 'email', 'mobile', 'rank_link', 'sex', 'pos_province', 'pos_city', 'pos_district', 'pos_community'), $uid);
        if ($user['pos_province'] != 0) {
            $user['pos_province'] = D('district')->where(array('id' => $user['pos_province']))->getField('name');
            $user['pos_city'] = D('district')->where(array('id' => $user['pos_city']))->getField('name');
            $user['pos_district'] = D('district')->where(array('id' => $user['pos_district']))->getField('name');
            $user['pos_community'] = D('district')->where(array('id' => $user['pos_community']))->getField('name');
        }

        //其他需要字段
        $user['extra']['about'] = D('field')->where(array('uid'=>$uid,'field_id'=>'15'))->getField('field_data');
        //显示页面
        $this->assign('user', $user);
        $this->getExpandInfo($uid);
        $this->getIssueInfo($uid);
        //当前登录用户的项目
        $this->getUserIssueInfo();

        //四处一词 seo
        $str = '{$user_info.name|text}';
        $this->setTitle($str . L('_INFO_TITLE_'));
        $this->setKeywords($str . L('_INFO_KEYWORDS_'));
        $this->setDescription($str . L('_INFO_DESC_')); 
        //四处一词 seo end

        $this->display();
    }

    public function inviteGuide(){
        //待解决，post是否需要过滤？
        $issue_id = $_POST['issue_id'];
        $advisor_id = $_GET['advisor_id'];

        $user_auth = session('user_auth');
        //$user_auth['uid']
        //当前登录用户ID
        $uid = $user_auth['uid'];
        //专家ID
        
        $title = '您有新的项目指导邀请，点击查看';
        
        $issue_owner = D('member')->where(array('uid'=>(int)$user_auth['uid']))->getField('name');
        $issue_name = $this->getIssueTitleById($issue_id);
        $url = U('ucenter/index/aggreeInvite',array('issue_id' => $issue_id ));

        $message = $issue_owner.'的项目 <strong>'.$issue_name['title'].'</strong> 邀请您指导!';

        //邀请关注的项目信息
        $aUrl = 'issue/index/issuecontentdetail';
        $url_args = array('id' => $issue_id);

        /**
        * sendMessage   发送消息，屏蔽自己
        * @param $to_uids 接收消息的用户们
        * @param string $title 消息标题
        * @param string $content 消息内容
        * @param string $url 消息指向的路径，U函数的第一个参数
        * @param array $url_args 消息链接的参数，U函数的第二个参数
        * @param int $from_uid 发送消息的用户
        * @param int $type 消息类型，0系统，1用户，2应用
        * @return bool
        * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
        */
        //sendMessage($to_uids, $title = '您有新的消息', $content = '', $url = '', $url_args = array(), $from_uid = -1, $type = 0)
        $result_advisor = D('Common/Message')->sendMessage($advisor_id, $title, $message, $aUrl, $url_args, $uid,1);
        if($result){
            $msg['status'] = 1;
            $msg['message'] = '已发送邀请，等待指导老师查看';
        }else{
            $msg['status'] = 0;
            $msg['message'] = '邀请成功';
        }
        $this->ajaxReturn($msg);
    }

    /**
    * 导师身份下，同意指导项目
    */
    public function aggreeInvite(){
        $issue_id = I('get.issue_id');
        if(!is_numeric($issue_id)){
            // $msg['status'] = 0;
            // $msg['message'] = '参数不合法';
            // $this->ajaxReturn($msg);
            $this->error('参数不合法',0);
        }

        //1.当前登录用户判断，是否为导师，若不是，返回错误,当前uid指专家
        $advisor_id = is_login();
        if(check_is_advisor($advisor_id) == false){
            // $msg['status'] = 0;
            // $msg['message'] = '非指导老师身份不能接受邀请';
            // $this->ajaxReturn($msg);
            $this->error('非指导老师身份不能接受邀请',0);
        }
        //2.项目判断，提示已有指导老师的，每个项目最多1名指导老师
        $result = D('Common/AdvisorIssue')->checkIssueExist($advisor_id,$issue_id);
        if($result['status'] === false){
            // $msg['status'] = 0;
            // $msg['message'] = $result['msg'];
            // $this->ajaxReturn($msg);
            $this->error($result['msg'],0);

        }

        //3.同意指导，status从0更改为1
        if(D('Common/AdvisorIssue')->saveInviteInfo($advisor_id,$issue_id)){
            // $msg['status'] = 1;
            $msg = '您已同意指导该项目';
            $this->success($msg,0);
        }else{
            // $msg['status'] = 0;
            // $msg['message'] = '系统错误，请稍后再试';
            $msg = '系统错误，请稍后再试';
            $this->error($msg,0);
        }
    }

    /**
    * 导师身份下，关注某项目
    */
    public function watchingIssue(){
        $issue_id = I('get.issue_id');
        $advisor_id = is_login();
        if(check_is_advisor($advisor_id) == false){
            $this->error('身份错误',0);
        }
        if(D('Common/AdvisorWatching')->watching($issue_id)){
            $this->success('成功关注项目',0);
        }else{
            $this->error('关注失败',0);
        }
    }
    /**
    * 导师身份下，取消关注某项目
    */
    public function cancelWatchingIssue(){
        $issue_id = I('get.issue_id');
        $advisor_id = is_login();
        if(check_is_advisor($advisor_id) == false){
            $this->error('身份错误',0);
        }
        if(D('Common/AdvisorWatching')->cancelWatching($issue_id)){
            $this->success('取消关注项目',0);
        }else{
            $this->error('关注失败',0);
        }
    }


    /**获取用户扩展信息
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function getExpandInfo($uid = null, $profile_group_id = null)
    {
        $profile_group_list = $this->_profile_group_list($uid);
        foreach ($profile_group_list as &$val) {
            $val['info_list'] = $this->_info_list($val['id'], $uid);
        }
        $this->assign('profile_group_list', $profile_group_list);
    }

    /**
    * 查询指导项目信息
    */
    private function getIssueInfo($uid = null){
        $issue_list = query_issue_by_advisor($uid);
        $this->assign('issue_count',count($issue_list));
        $this->assign('issue_list',$issue_list);

        $watching_issue_list = query_watching_issue_by_advisor($uid);
        $this->assign('watching_issue_count',count($watching_issue_list));
        $this->assign('watching_issue_list',$watching_issue_list);
    }

    /**
    * 找出当前登录用户拥有的项目信息
    */
    private function getUserIssueInfo(){
        $user_auth = session('user_auth');
        //$user_auth['uid']
        $map['uid'] = $user_auth['uid'];
        $user_issue = D('Issue_content')->where($map)->field('id,title,type')->select();
        $this->assign('user_issue_info',$user_issue);
    }

    /**
    * 根据项目ID找出项目信息,返回项目ID,标题及所属用户ID
    */
    private function getIssueTitleById($issue_id = null){
        if($issue_id != null){
            $map['id'] = $issue_id;
            $issue_info = D('Issue_content')->where($map)->field('title')->find();
        }
        return $issue_info;
    }

    /**扩展信息分组列表获取
     * @param null $uid
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _profile_group_list($uid = null)
    {
        $profile_group_list=array();
        $fields_list=$this->getRoleFieldIds($uid);
        if($fields_list){
            $fields_group_ids=D('FieldSetting')->where(array('id'=>array('in',$fields_list),'status' => '1'))->field('profile_group_id')->select();
            if($fields_group_ids){
                $fields_group_ids=array_unique(array_column($fields_group_ids,'profile_group_id'));
                $map['id']=array('in',$fields_group_ids);

                if (isset($uid) && $uid != is_login()) {
                    $map['visiable'] = 1;
                }
                $map['status'] = 1;
                $profile_group_list = D('field_group')->where($map)->order('sort asc')->select();
            }
        }
        return $profile_group_list;
    }

    private function getRoleFieldIds($uid=null){
        $role_id=get_role_id($uid);
        $fields_list=S('Role_Expend_Info_'.$role_id);
        if(!$fields_list){
            $map_role_config=getRoleConfigMap('expend_field',$role_id);
            $fields_list=D('RoleConfig')->where($map_role_config)->getField('value');
            if($fields_list){
                $fields_list=explode(',',$fields_list);
                S('Role_Expend_Info_'.$role_id,$fields_list,600);
            }
        }
        return $fields_list;
    }

    /**分组下的字段信息及相应内容
     * @param null $id
     * @param null $uid
     * @return null
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _info_list($id = null, $uid = null)
    {
        $fields_list=$this->getRoleFieldIds($uid);
        $info_list = null;

        if (isset($uid) && $uid != is_login()) {
            //查看别人的扩展信息
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'visiable' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = $uid;
        } else if (is_login()) {
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = is_login();

        } else {
            $this->error(L('_ERROR_PLEASE_LOGIN_').L('_EXCLAMATION_'));
        }
        foreach ($field_setting_list as &$val) {
            $map['field_id'] = $val['id'];
            $field = D('field')->where($map)->find();
            $val['field_content'] = $field;
            unset($map['field_id']);
            $info_list[$val['id']] = $this->_get_field_data($val);
            //当用户扩展资料为数组方式的处理@MingYangliu
            $vlaa = explode('|', $val['form_default_value']);
            $needle =':';//判断是否包含a这个字符
            $tmparray = explode($needle,$vlaa[0]);
            if(count($tmparray)>1){
                foreach ($vlaa as $kye=>$vlaas){
                    if(count($tmparray)>1){
                        $vlab[] = explode(':', $vlaas);
                        foreach ($vlab as $key=>$vlass){
                            $items[$vlass[0]] = $vlass[1];
                        }
                    }
                    continue;
                }
                $info_list[$val['id']]['field_data'] = $items[$info_list[$val['id']]['field_data']];
            }
            //当扩展资料为join时，读取数据并进行处理再显示到前端@MingYang
            if($val['child_form_type'] == "join"){
                $j = explode('|',$val['form_default_value']);
                $a = explode(' ',$info_list[$val['id']]['field_data']);
                $info_list[$val['id']]['field_data'] = get_userdata_join($a,$j[0],$j[1]);
            }
        }
        return $info_list;
    }

    public function _get_field_data($data = null)
    {
        $result = null;
        $result['field_name'] = $data['field_name'];
        $result['field_data'] = L('');
        switch ($data['form_type']) {
            case 'input':
            case 'radio':
            case 'textarea':
            case 'select':
                $result['field_data'] = isset($data['field_content']['field_data']) ? $data['field_content']['field_data'] : "";
                break;
            case 'checkbox':
                $result['field_data'] = isset($data['field_content']['field_data']) ? implode(' ', explode('|', $data['field_content']['field_data'])) : "";
                break;
            case 'time':
                $result['field_data'] = isset($data['field_content']['field_data']) ? date("Y-m-d", $data['field_content']['field_data']) : "";
                break;
        }
        $result['field_data'] = op_t($result['field_data']);
        return $result;
    }

    public function appList($uid = null, $page = 1, $tab = null)
    {
        $show_tab= get_kanban_config('UCENTER_KANBAN', 'enable','', 'USERCONFIG');
        $menu=$this->_tab_menu();
        foreach($show_tab as $v1) {
            foreach($menu as $v2) {
                if (array_search($v1,$v2)) {
                    $arr3[$v1] = $v2;
                }
            }
        }
        unset($v1);unset($v2);
        $appArr =$arr3;

        if (!$appArr) {
            $this->redirect('Usercenter/Index/information', array('uid' => $uid));
        }

        $type = op_t($_GET['type']);
        if (!isset ($appArr [$type])) {
            $this->error(L('_ERROR_PARAM_').L('_EXCLAMATION_').L('_EXCLAMATION_'));
        }
        $this->assign('type', $type);
        $this->assign('module',$appArr[$type]['data-id']);
        $this->assign('page',$page);
        $this->assign('tab',$tab);

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $str_app = '{$appArr.'.$type.'.title|op_t}';
        $this->setTitle($str . L('_DE_PERSON_') . $str_app . L('_PAGE_'));
        $this->setKeywords($str . L('_PAGE_PERSON_') . $str_app);
        $this->setDescription($str . L('_DE_PERSON_') . $str_app . L('_PAGE_'));
        //四处一词 seo end

        $this->display('index');
    }

    /**
     * 个人主页标签导航
     * @return void
     */
    public function _tab_menu()
    {
        $modules = D('Common/Module')->getAll();
        $apps = array();
        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/Widget/UcenterBlockWidget.class.php')) {
                    $apps[] = array('data-id' => $m['name'], 'title' => $m['alias'],'sort'=>$m['sort'],'key'=>strtolower($m['name']));
                }
            }
        }

        $show_tab= get_kanban_config('UCENTER_KANBAN', 'enable','', 'USERCONFIG');
        $apps[] = array('data-id' => 'info', 'sort'=>'0', 'title' =>'资料','key'=>'info');
        $apps[] = array('data-id' => 'rank_title', 'sort'=>'0', 'title' => L('_RANK_TITLE_'),'key'=>'rank_title');
        $apps[] = array('data-id' => 'follow', 'sort'=>'0','title' =>L('_FOLLOWERS_NO_SPACE_').'/粉丝','key'=>'follow');

        $apps = $this->sortApps($apps);
        $apps=array_combine(array_column($apps,'key'),$apps);
        foreach($show_tab as $v1) {
            foreach($apps as $v2) {
                if (array_search($v1,$v2)) {
                    $arr3[$v1] = $v2;
                }
            }
        }
        unset($v1);unset($v2);
        $this->assign('appArr', $arr3);
        return $apps;
    }


    public function _fans_and_following($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();
        //我的粉丝展示
        $map['follow_who'] = $uid;
        $fans_default = D('Follow')->where($map)->field('who_follow')->order('create_time desc')->limit(8)->select();
        $fans_totalCount = D('Follow')->where($map)->count();
        foreach ($fans_default as &$user) {
            $user['user'] = query_user(array('avatar64', 'uid', 'name', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['who_follow']);
        }
        unset($user);
        $this->assign('fans_totalCount', $fans_totalCount);
        $this->assign('fans_default', $fans_default);

        //我关注的展示
        $map_follow['who_follow'] = $uid;
        $follow_default = D('Follow')->where($map_follow)->field('follow_who')->order('create_time desc')->limit(8)->select();
        $follow_totalCount = D('Follow')->where($map_follow)->count();
        foreach ($follow_default as &$user) {
            $user['user'] = query_user(array('avatar64', 'uid', 'name', 'fans', 'following', 'weibocount', 'space_url', 'title'), $user['follow_who']);
        }
        unset($user);
        $this->assign('follow_totalCount', $follow_totalCount);
        $this->assign('follow_default', $follow_default);
    }

    public function fans($uid = null, $page = 1)
    {
        $uid = isset($uid) ? $uid : is_login();

        $this->assign('tab', 'fans');
        $fans = D('Follow')->getFans($uid, $page, array('avatar128', 'uid', 'name', 'fans', 'following', 'weibocount', 'space_url', 'title'), $totalCount);
        $this->assign('fans', $fans);
        $this->assign('totalCount', $totalCount);

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $this->setTitle($str . L('_FANS_TITLE_'));
        $this->setKeywords($str . L('_FANS_KEYWORDS_'));
        $this->setDescription($str . L('_FANS_TITLE_'));
        //四处一词 seo end

        $this->display();
    }

    public function following($uid = null, $page = 1)
    {
        $uid = isset($uid) ? $uid : is_login();

        $following = D('Follow')->getFollowing($uid, $page, array('avatar128', 'uid', 'name', 'fans', 'following', 'weibocount', 'space_url', 'title'), $totalCount);
       // dump($following);exit;
        $this->assign('following', $following);
        $this->assign('totalCount', $totalCount);
        $this->assign('tab', 'following');

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $this->setTitle($str . L('_FOLLOWING_TITLE_'));
        $this->setKeywords($str . L('_FOLLOWING_KEYWORDS_'));
        $this->setDescription($str . L('_FOLLOWING_DESC_'));
        //四处一词 seo end

        $this->display();
    }

    public function rank($uid = null)
    {
        $uid = isset($uid) ? $uid : is_login();

        $rankList = D('rank_user')->where(array('uid' => $uid, 'status' => 1))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rank');

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $this->setTitle($str . L('_RANK__TITLE_'));
        $this->setKeywords($str . L('_RANK__KEYWORDS_'));
        $this->setDescription($str . L('_RANK__DESC_'));
        //四处一词 seo end

        $this->display('rank');
    }

    public function rankVerifyFailure()
    {
        $uid = isset($uid) ? $uid : is_login();

        $rankList = D('rank_user')->where(array('uid' => $uid, 'status' => -1))->field('id,rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerifyFailure');

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $this->setTitle($str . L('_RANK_TITLE_'));
        $this->setKeywords($str . L('_RANK__KEYWORDS_'));
        $this->setDescription($str . L('_RANK_TITLE_'));
        //四处一词 seo end

        $this->display('rank');
    }

    public function rankVerifyWait()
    {
        $uid = isset($uid) ? $uid : is_login();

        $rankList = D('rank_user')->where(array('uid' => $uid, 'status' => 0))->field('rank_id,reason,create_time')->select();
        foreach ($rankList as &$val) {
            $rank = D('rank')->where('id=' . $val['rank_id'])->find();
            $val['title'] = $rank['title'];
            $val['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
            $val['label_content']=$rank['label_content'];
            $val['label_bg']=$rank['label_bg'];
            $val['label_color']=$rank['label_color'];
        }
        unset($val);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerifyWait');

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $this->setTitle($str . L('_RANK_TITLE_'));
        $this->setKeywords($str . L('_RANK__KEYWORDS_'));
        $this->setDescription($str . L('_RANK_TITLE_'));
        //四处一词 seo end

        $this->display('rank');
    }

    public function rankVerifyCancel($rank_id = null)
    {
        $rank_id = intval($rank_id);
        if (is_login() && $rank_id) {
            $map['rank_id'] = $rank_id;
            $map['uid'] = is_login();
            $map['status'] = 0;
            $result = D('rank_user')->where($map)->delete();
            if ($result) {
                D('Message')->sendMessageWithoutCheckSelf(is_login(),L('_MESSAGE_RANK_CANCEL_1_'),  L('_MESSAGE_RANK_CANCEL_2_'), 'Ucenter/Message/message', array('tab' => 'system'));
                $this->success(L('_SUCCESS_CANCEL_'), U('Ucenter/Index/rankVerifyWait'));
            } else {
                $this->error(L('_FAIL_CANCEL_'));
            }
        }
    }

    public function rankVerify($rank_user_id = null)
    {
        $uid = isset($uid) ? $uid : is_login();

        $rank_user_id = intval($rank_user_id);
        $map_already['uid'] = $uid;
        //重新申请头衔
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            $old_rank_user = $model->field('id,rank_id,reason')->find();
            if (!$old_rank_user) {
                $this->error(L('_ERROR_RANK_RE_SELECT_'));
            }
            $this->assign('old_rank_user', $old_rank_user);
            $map_already['id'] = array('neq', $rank_user_id);
            D('Message')->sendMessageWithoutCheckSelf(is_login(), L(''),L(''),  'Ucenter/Message/message', array('tab' => 'system'));
        }
        $alreadyRank = D('rank_user')->where($map_already)->field('rank_id')->select();
        $alreadyRank = array_column($alreadyRank, 'rank_id');
        if ($alreadyRank) {
            $map['id'] = array('not in', $alreadyRank);
        }
        $map['types'] = 1;
        $rankList = D('rank')->where($map)->select();
        foreach($rankList as &$rank){
            $rank['logo_url'] = get_pic_src(M('picture')->where('id=' . $rank['logo'])->field('path')->getField('path'));
        }
        unset($rank);
        $this->assign('rankList', $rankList);
        $this->assign('tab', 'rankVerify');

        //四处一词 seo
        $str = '{$user_info.name|op_t}';
        $this->setTitle($str . L('_RANK_APPLY_TITLE_'));
        $this->setKeywords($str . L('_RANK_APPLY_KEYWORDS_'));
        $this->setDescription($str . L('_RANK_APPLY_TITLE_'));
        //四处一词 seo end

        $this->display('rank_verify');
    }

    public function verify($rank_id = null, $reason = null, $rank_user_id = 0)
    {
        $rank_id = intval($rank_id);
        $reason = op_t($reason);
        $rank_user_id = intval($rank_user_id);
        if (!$rank_id) {
            $this->error(L('_ERROR_RANK_SELECT_'));
        }
        if ($reason == null || $reason == '') {
            $this->error(L('_ERROR_RANK_REASON_'));
        }
        $data['rank_id'] = $rank_id;
        $data['reason'] = $reason;
        $data['uid'] = is_login();
        $data['is_show'] = 1;
        $data['create_time'] = time();
        $data['status'] = 0;
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            if (!$model->select()) {
                $this->error(L('_ERROR_RANK_RE_SELECT_'));
            }
            $result = D('rank_user')->where(array('id' => $rank_user_id))->save($data);
        } else {
            $result = D('rank_user')->add($data);
        }
        if ($result) {
            D('Message')->sendMessageWithoutCheckSelf(is_login(),L('_MESSAGE_RANK_APPLY_1_'),L('_MESSAGE_RANK_APPLY_2_'),  'Ucenter/Message/message', array('tab' => 'system'));
            $this->success(L('_SUCCESS_RANK_APPLY_'), U('Ucenter/Index/rankVerify'));
        } else {
            $this->error(L('_FAIL_RANK_APPLY_'));
        }
    }

    /**
     * @param $apps
     * @param $vals
     * @return mixed
     * @auth 陈一枭
     */
    private function sortApps($apps)
    {
        return $this->multi_array_sort($apps, 'sort', SORT_DESC);
    }

    function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC)
    {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }

}