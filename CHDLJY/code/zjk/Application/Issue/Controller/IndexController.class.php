<?php


namespace Issue\Controller;

use Think\Controller;
use Core\Controller\YibanController;


class IndexController extends YibanController
{
    /**
     * 业务逻辑都放在 WeiboApi 中
     * @var
     */
    public function _initialize()
    {
        $tree = D('Issue')->getTree();
        $this->assign('tree', $tree);


        $sub_menu =
            array(
                'left' =>
                    array(
                        array('tab' => 'home', 'title' =>L('_HOME_'), 'href' => U('Issue/index/index')),
                    ),
            );
        if (check_auth('addIssueContent')) {
            $sub_menu['right'] = array(
                array('tab' => 'post', 'title' => L('_RELEASE_'), 'href' => '#frm-post-popup','a_class'=>'open-popup-link')
            );
        }
        foreach ($tree as $cat) {
            if ($cat['_']) {
                $children = array();
                $children[] = array('tab' => 'cat_' . $cat['id'], 'title' => L('_ALL_'), 'href' => U('Issue/index/index', array('issue_id' => $cat['id'])));
                foreach ($cat['_'] as $child) {
                    $children[] = array('tab' => 'cat_' . $cat['id'], 'title' => $child['title'], 'href' => U('Issue/index/index', array('issue_id' => $child['id'])));
                }

            }
            $menu_item = array('children' => $children, 'tab' => 'cat_' . $cat['id'], 'title' => $cat['title'], 'href' => U('Issue/Index/index', array('issue_id' => $cat['id'])));
            $sub_menu['left'][] = $menu_item;
            unset($children);
        }
        $sub_menu['first']=array('title'=>L('_MODULE_'));
        $this->assign('sub_menu', $sub_menu);

        $issue_type =  array(
            array('id' => '1','title'=> '“互联网+”现代农业' ),
            array('id' => '2','title'=> '“互联网+”制造业' ),
            array('id' => '3','title'=> '“互联网+”信息技术服务' ),
            array('id' => '4','title'=> '“互联网+”文化创意服务' ),
            array('id' => '5','title'=> '“互联网+”商务服务' ),
            array('id' => '6','title'=> '“互联网+”公共服务' ),
            array('id' => '7','title'=> '“互联网+”公益创业' )
        );
        $this->assign('issue_type',$issue_type);
    }

    public function index($page = 1, $issue_id = 0)
    {
        //设置展示方式 列表；瀑布流
        $aDisplay_type=I('display_type','','text');
        $cookie_type=cookie('issue_display_type');
        if($aDisplay_type==''){
            if($cookie_type){
                $aDisplay_type=$cookie_type;
            }else{
                $aDisplay_type=modC('DISPLAY_TYPE','list','Issue');
                cookie('issue_display_type',$aDisplay_type);
            }
        }else{
            if($cookie_type!=$aDisplay_type){
                cookie('issue_display_type',$aDisplay_type);
            }
        }
        $this->assign('display_type',$aDisplay_type);
        //设置展示方式 列表；瀑布流 end

        $issue_id = intval($issue_id);
        $issue = D('Issue')->find($issue_id);
        if (!$issue_id == 0) {
            $issue_id = intval($issue_id);
            $issues = D('Issue')->where("id=%d OR pid=%d", array($issue_id, $issue_id))->limit(999)->select();
            $ids = array();
            foreach ($issues as $v) {
                $ids[] = $v['id'];
            }
            $map['issue_id'] = array('in', implode(',', $ids));
        }
        $map['status'] = 1;
        $content = D('IssueContent')->where($map)->order('step desc,create_time desc')->page($page, 10)->select();
        $totalCount = D('IssueContent')->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'name', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            if($aDisplay_type=='masonry'){
                $cover = M('Picture')->where(array('status' => 1))->getById($v['cover_id']);
                $c_path=$cover['path'];
                $tag='ttp:';
                if(!strpos($c_path,$tag))
                    $c_path='.'.$cover['path'];
                $imageinfo = getimagesize($c_path);
                $v['cover_height']=round($imageinfo[1]*255/$imageinfo[0]);
                $v['cover_height']=$v['cover_height']?$v['cover_height']:253;
            }
        }
        unset($v);
        $this->assign('contents', $content);

        $this->assign('totalPageCount', $totalCount);
        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);

        $this->assign('issue_id', $issue_id);
        $this->setTitle(L('_MODULE_'));
        $this->display();
    }

    public function doPost($id = 0, $cover_id = 0, $title = '', $content = '', $issue_id = 0, $url = '',
                            $plan_id='',$tc_name='',$members='',$stage='',
                            $type='',$bind_unitech='',$description='')
    {
        if (!check_auth('addIssueContent')) {
            $this->error(L('_AUTHORITY_LACK_'));
        }
        $issue_id = intval($issue_id);
        if (!is_login()) {
            $this->error(L('_FIRST_LOGIN_'));
        }
        if (!$cover_id) {
            $this->error(L('_NEED_COVER_'));
        }
        if (trim(op_t($title)) == '') {
            $this->error(L('_NEED_TITLE_'));
        }
        if (trim(op_h($content)) == '') {
            $this->error(L('_NEED_CONTENT_'));
        }
        //if ($issue_id == 0) {
        //    $this->error(L('_NEED_CATEGORY_'));
        //}
        //if (trim(op_h($url)) == '') {
        //   $this->error(L('_NEED_WEBSITE_'));
        //}
        $content = D('IssueContent')->create();
        $content['content'] = filter_content($content['content']);
        $content['title'] = op_t($content['title']);
        $content['url'] = op_t($content['url']); //新增链接框
        $content['issue_id'] = $issue_id;
        $content['type'] = $type;
        $content['plan_id'] = $plan_id;
        $content['tc_name'] = $tc_name;
        $content['members'] = $members;
        $content['stage'] = $stage;
        $content['bind_unitech'] = $bind_unitech;
        $content['description'] = $description;
        if ($id) {
            $content_temp = D('IssueContent')->find($id);
            if (!check_auth('editIssueContent')) { //不是管理员则进行检测
                if ($content_temp['uid'] != is_login()) {
                    $this->error(L('_FORBID_TO_OTHER_'));
                }
            }
            $content['uid'] = $content_temp['uid']; //权限矫正，防止被改为管理员
            $rs = D('IssueContent')->save($content);
            if ($rs) {
                $this->success(L('_SUCCESS_EDIT_'), U('issueContentDetail', array('id' => $content['id'])));
            } else {
                $this->success(L('_FAIL_EDIT_'), '');
            }
        } else {
            if (modC('NEED_VERIFY', 0) && !is_administrator()) //需要审核且不是管理员
            {
                $content['status'] = 0;
                $tip = L('_TIP_AUDIT_');
                $user = query_user(array('name'), is_login());
                $admin_uids = explode(',', C('USER_ADMINISTRATOR'));
                foreach ($admin_uids as $admin_uid) {
                    D('Common/Message')->sendMessage($admin_uid, $title = L('_WARN_CONTRIBUTE_'),"{$user['name']}".L('_PLEASE_AUDIT_'),  'Admin/Issue/verify', array(),is_login(), 2);
                }
            }
            $rs = D('IssueContent')->add($content);
            if ($rs) {
                $this->success(L('_SUCCESS_CONTRIBUTE_') . $tip, 'refresh');
            } else {
                $this->success(L('_FAIL_CONTRIBUTE_'), '');
            }
        }


    }

    public function issueContentDetail($id = 0)
    {


        $issue_content = D('IssueContent')->find($id);
        if (!$issue_content) {
            $this->error('404 not found');
        }
        D('IssueContent')->where(array('id' => $id))->setInc('view_count');
        $issue = D('Issue')->find($issue_content['issue_id']);
        
        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);
        $this->assign('issue_id', $issue['id']);
        $this->assign('issue_type_id',$issue['type']);
        $issue_content['user'] = query_user(array('id', 'name', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $issue_content['uid']);
        $this->assign('content', $issue_content);

        //导师指导状态按钮
        $guide_button = D('Common/AdvisorIssue')->check_advisor_auth($id);
        $this->assign('guide_button',$guide_button);
        //关注状态按钮
        $watching_button = D('Common/AdvisorWatching')->check_advisor_watching_auth($id);
        $this->assign('watching_button',$watching_button);

        $map = $this->setMap();
        $map['status'] = 1;
        $map['last_login_time'] = array('neq', 0);
        $peoples = S('People_peoples_' . I('page', 0, 'intval') . '_' . serialize($map));
        $this->assign('advisorlist',$peoples['data']);
        //dump($peoples);
        $this->setTitle('{$content.title|op_t}' . '——'.L('_MODULE_'));
        $this->setKeywords($issue_content['title']);
        $this->display();
    }

    public function selectDropdown($pid)
    {
        $issues = D('Issue')->where(array('pid' => $pid, 'status' => 1))->limit(999)->select();
        exit(json_encode($issues));


    }

    public function edit($id)
    {
        if (!check_auth('addIssueContent') && !check_auth('editIssueContent')) {
            $this->error(L('_ERROR_SORRY_'));
        }
        $issue_content = D('IssueContent')->find($id);
        if (!$issue_content) {
            $this->error('404 not found');
        }
        if (!check_auth('editIssueContent')) { //不是管理员则进行检测
            if ($issue_content['uid'] != is_login()) {
                $this->error('404 not found');
            }
        }

        $issue = D('Issue')->find($issue_content['issue_id']); 

        $this->assign('top_issue', $issue['pid'] == 0 ? $issue['id'] : $issue['pid']);
        $this->assign('issue_id', $issue['id']);
        $issue_content['user'] = query_user(array('id', 'name', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $issue_content['uid']);
        //$issue_content['type'] = explode(',', $issue_content['type']);
        $this->assign('content', $issue_content);
        //dump($issue_content);
        //专家列表
        $map['show_role'] = '2';
        $peoples = D('Member')->where($map)->field('uid,name')->select();
        //dump($peoples);
        $this->assign('advisor',$peoples);
        
        //是否为创业项目
        // if(C('COMPANY_ID') == $issue['id']){
        //     $isCompany = 1;
        // }else{
        //     $isCompany = 0;
        // }
        //  dump(C('COMPANY_ID'));
        //   dump($isCompany);
        // $this->assign('isCompany',$isCompany);
        $this->assign('reg_company_id',C('COMPANY_ID'));
        $this->display();
    }

    /***********************area***************************************/
    private function setMap()
    {
        $aTag = 0;
        //限制专家库人员类型
        $aRole = 2;
        //---------------

        $role_list = modC('SHOW_ROLE_TAB', '', 'People');
        //dump($role_list);
        if ($role_list != '') {
            $role_list = json_decode($role_list, true);
            $role_list = $role_list[1]['items'];
            if (count($role_list)) {
                foreach ($role_list as &$val) {
                    $val['id'] = $val['data-id'];
                }
                unset($val);
                $this->assign('role_list', $role_list);
            } else {
                $aRole = 0;
            }
        } else {
            $aRole = 0;
        }
        $map = array();
        if ($aTag && $aRole) {//同时选择标签和身份
            !isset($_GET['tag']) && $_GET['tag'] = $_POST['tag'];
            $map_uids['tags'] = array('like', '%[' . $aTag . ']%');
            $tag_links = D('Ucenter/UserTagLink')->getListByMap($map_uids);
            $tag_uids = array_column($tag_links, 'uid');
            $this->assign('tag_id', $aTag);

            !isset($_GET['role']) && $_GET['role'] = $_POST['role'];
            $map_role['role_id'] = $aRole;
            $map_role['status'] = 1;
            $role_links = M('UserRole')->where($map_role)->limit(999)->field('uid')->select();
            $role_uids = array_column($role_links, 'uid');
            $this->assign('role_id', $aRole);
            if ($tag_uids && $role_uids) {
                $uids = array_intersect($tag_uids, $role_uids);
            } else {
                $uids = array();
            }
            $map['uid'] = array('in', $uids);
        } else if ($aTag) {//选择标签，没选择身份
            !isset($_GET['tag']) && $_GET['tag'] = $_POST['tag'];
            $map_uids['tags'] = array('like', '%[' . $aTag . ']%');
            $tag_links = D('Ucenter/UserTagLink')->getListByMap($map_uids);
            $tag_uids = array_column($tag_links, 'uid');
            $this->assign('tag_id', $aTag);

            //手动屏蔽学生角色
            /*$map_role['role_id'] = array('in','2,3');
            $map_role['status'] = 1;
            $role_links = M('UserRole')->where($map_role)->limit(999)->field('uid')->select();
            $role_uids = array_column($role_links, 'uid');
            if ($tag_uids && $role_uids) {
                $uids = array_intersect($tag_uids, $role_uids);
            } else {
                $uids = array();
            }*/
            $map['uid'] = array('in', $uids);
        } else if ($aRole) {//选择身份，没选择标签
            !isset($_GET['role']) && $_GET['role'] = $_POST['role'];
            $map_role['role_id'] = $aRole;
            $map_role['status'] = 1;
            $role_links = M('UserRole')->where($map_role)->limit(999)->field('uid')->select();
            $role_uids = array_column($role_links, 'uid');
            $map['uid'] = array('in', $role_uids);
            $this->assign('role_id', $aRole);
        }
        $userTagModel = D('Ucenter/UserTag');
        if ($aRole) {
            $map_tags = getRoleConfigMap('user_tag', $aRole);
            $can_config = M('RoleConfig')->where($map_tags)->field('value')->find();
            if ($can_config['value'] != '') {
                $tag_list = $userTagModel->getTreeListByIds($can_config['value']);
            } else {
                $tag_list = null;
            }
        } else {
            $tag_list = $userTagModel->getTreeList();
        }
        //dump($tag_list);
        $this->assign('tag_list', $tag_list);
        $name = I('keywords', '', 'op_t');
        if ($name != '') {
            !isset($_GET['keywords']) && $_GET['keywords'] = $_POST['keywords'];
            $map['name'] = array('like', '%' . $name . '%');
            $this->assign('name', $name);
        }
//        //dump($map);
//        //疑问 为何不支持表达式查询？
//        $role_links = M('UserRole')->where('`status` = 1 AND `role_id` <> 1')->limit(999)->field('uid')->select();
//        //dump(M('UserRole')->getLastSql());
//        $role_uids = array_column($role_links, 'uid');
//        if($map['uid']){
//            $test_uids = array_intersect($map['uid'][1], $role_uids);
//            $map['uid'] = array('in',$test_uids);
//        }else{
//            $map['uid'] = array('in',$role_uids);
//        }
         return $map;
    }

    // //邀请导师
    // public function invite(){
    //     $data['status'] = 'true';
    //     $ids = I('post.ids');
    //     $issue = I('post.issueid');
    //     $data['info'] = $ids;
    //     $this->ajaxReturn($data);
    // }
}