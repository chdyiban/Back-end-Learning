<?php


namespace People\Controller;

use Think\Controller;
use Core\Controller\YibanController;

class IndexController extends YibanController
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $map = $this->setMap();
        $map['status'] = 1;
        $map['last_login_time'] = array('neq', 0);
        //$peoples = S('People_peoples_' . I('page', 0, 'intval') . '_' . serialize($map));
        if (empty($peoples)) {
            $peoples = D('Member')->where($map)->field('uid', 'reg_time', 'last_login_time')->order('last_login_time desc')->findPage(12);
            $userConfigModel = D('Ucenter/UserConfig');
            $titleModel = D('Ucenter/Title');
            foreach ($peoples['data'] as &$v) {
                $v = query_user(array('title', 'avatar128', 'name', 'uid', 'space_url', 'title', 'fans', 'following', 'rank_link', 'pos_province', 'pos_city', 'pos_district','score1'), $v['uid']);
                $v['province'] = get_area_name($v['pos_province']);
                $v['city'] = get_area_name($v['pos_city']);
                $v['district'] = get_area_name($v['pos_district']);

                $v['level'] = $titleModel->getCurrentTitleInfo($v['uid']);
                //获取用户封面id
                $where = getUserConfigMap('user_cover', '', $v['uid']);
                $where['role_id'] = 0;
                $model = $userConfigModel;
                $cover = $model->findData($where);
                $v['cover_id'] = $cover['value'];
                $v['cover_path'] = getThumbImageById($cover['value'], 273, 80);

                //获取用户个人简介，字段为15(根据数据库结构而定的，开发者请注意)
                $profileMap['uid'] = $v['uid'];
                $profileMap['field_id'] = '15';
                $v['introduction'] = M('Field')->where($profileMap)->getField('field_data');
            }
            unset($v);
            S('People_peoples_' . I('page', 0, 'intval') . '_' . serialize($map), $peoples, 3600);
        }
        //指导项目数循环遍历，影响效率，待优化
        foreach ($peoples['data'] as $key => $value) {
            # code...
            //指导项目数 count($issue_list)
            $issue_list = query_issue_by_advisor($value['uid']);
            //关注项目数
            $watching_issue_list = query_watching_issue_by_advisor($value['uid']);
            //热度 score1值
            // $hot = query_user(array('score1'), is_login());
            
            $peoples['data'][$key]['issue_list'] = count($issue_list);
            $peoples['data'][$key]['watching_issue_list'] = count($watching_issue_list);
            // $peoples['data'][$key]['hot'] = $hot['score1'];
        }
        // dump($peoples);

        $this->assign('tab', 'index');
        $this->assign('lists', $peoples);

        //yiban
        $yb_user = session('yb_user');
        $this->assign('yb_user',$yb_user);

        $this->display();
    }

    /***********************area***************************************/
    public function area()
    {
        $map = $this->setMap();
        $arearank = I('get.arearank', 0);
        $arealv = I('get.arealv');
        $areaname = I('get.areaname');
        if ($arearank == null || $arearank == 0) {
            $map['pos_province'] =array('neq','');
        } else {
            switch ($arealv) {
                case 1:
                    $map['pos_province'] = $arearank;
                    break;
                case 2:
                    $map['pos_city'] = $arearank;
                    break;
                case 3:
                    $map['pos_district'] = $arearank;
                    break;
                default:
                    $map['pos_province'] != null;
            }
        }


        $map['status'] = 1;
        $map['last_login_time'] = array('neq', 0);
        $peoples = S('People_peoples_' . I('page', 0, 'intval') . '_' . serialize($map));
        if (empty($peoples)) {
            $peoples = D('Member')->where($map)->field('uid', 'reg_time', 'last_login_time')->order('last_login_time desc')->findPage(12);

            $userConfigModel = D('Ucenter/UserConfig');
            $titleModel = D('Ucenter/Title');


            foreach ($peoples['data'] as &$v) {
                $v = query_user(array('title', 'avatar128', 'name', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'rank_link', 'pos_province', 'pos_city', 'pos_district'), $v['uid']);
                $v['province'] = get_area_name($v['pos_province']);
                $v['city'] = get_area_name($v['pos_city']);
                $v['district'] = get_area_name($v['pos_district']);

                $v['level'] = $titleModel->getCurrentTitleInfo($v['uid']);
                //获取用户封面id
                $where = getUserConfigMap('user_cover', '', $v['uid']);
                $where['role_id'] = 0;
                $model = $userConfigModel;
                $cover = $model->findData($where);
                $v['cover_id'] = $cover['value'];
                $v['cover_path'] = getThumbImageById($cover['value'], 273, 80);
            }
            unset($v);
            S('People_peoples_' .I('page',0,'intval').'_' . serialize($map), $peoples, 3600);
        }

        //地区信息
        $district = M('district');
        $areanumber = M('member');
        $areadata = $district->where('upid=' . $arearank)->select();
        //地区人数
        foreach ($areadata as &$v1) {
            switch ($v1['level']) {
                case 1:
                    $res = $areanumber->where(array('pos_province' => $v1['id']))->count();
                    $v1['number'] = $res;
                    break;
                case 2:
                    $res = $areanumber->where(array('pos_city' => $v1['id']))->count();
                    $v1['number'] = $res;
                    break;
                case 3:
                    $res = $areanumber->where(array('pos_district' => $v1['id']))->count();
                    $v1['number'] = $res;
                    break;
                default:
                    $res = 0;
            }
        }
        unset($v1);


        if ($areadata == null) {
            $areadata = $district->where('id=' . $arearank)->field('upid', true)->select();
        }

        $this->assign('tag_arealist', $areadata);
        if ($areaname == null) {
            $this->assign('areaname', "");
            $this->assign('goback', "");
        } else {
            $this->assign('areaname', $areaname . ':');
            $this->assign('goback', "返回");
        }
        $this->assign('tab', 'area');
        $this->assign('lists', $peoples);
        $this->display();


    }

    /***********************area***************************************/
    private function setMap()
    {
        $aTag = I('tag', 0, 'intval');
        $aRole = I('role', 0, 'intval');
        //限制专家库人员类型
        $aRole = 2;
        //---------------

        $role_list = modC('SHOW_ROLE_TAB', '', 'People');
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
}