<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: UCToo <contact@uctoo.com> <http://www.uctoo.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;
use Admin\Builder\AdminSortBuilder;
use Common\Model\VerifyModel;

class BuilddemoController extends AdminController
{
    protected $module;

    function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
	    $list = array(
		    array('title'=>L('_LISTBUILD1_'),'type'=>'listbuild/demo/0','desc'=>L('_SIMPLELIST_')),
		    array('title'=>L('_LISTBUILD2_'),'type'=>'listbuild/demo/1','desc'=>L('_LINKSANDMODELWINDOW_')),
		    array('title'=>L('_CONFIGBUILD1_'),'type'=>'configbuild','desc'=>L('_INPUTSDEMO_')),
		    array('title'=>L('_CONFIGBUILD2_'),'type'=>'configbuild1','desc'=>L('_CHOOSENDEMO_')),
		    array('title'=>L('_CONFIGBUILD3_'),'type'=>'configbuild2','desc'=>L('_UPLOADANDEDITOR_')),
		    array('title'=>L('_CONFIGBUILD4_'),'type'=>'configbuild3','desc'=>L('_TABSDEMO_')),
		    array('title'=>L('_SORTBUILD_'),'type'=>'sortbuild','desc'=>L('_SORTDEMO_')),
		    array('title'=>L('_TREEBUILD_'),'type'=>'treebuild','desc'=>L('_TREEDEMO_')),
	    );

	    $build = new AdminListBuilder();
	    $build
		    ->title('builder四种类型界面基本示例')
		    ->suggest('结合源码查看会更好点')
		    ->keyLinkByFlag('title','示例链接','admin/builddemo/###','type')
		    ->keytext('desc','简要说明')
		    ->data($list)
		    ->display();
    }

	public function listbuild()
	{
		if(IS_POST)
		{
			//
			$this->success('操作成功');
		}
		$build = new AdminListBuilder();
		//标题 到 搜索和筛选 示例
		$build
			->title('我是标题')
			->suggest('我是提示')
			->buttonNew(U('admin/builddemo/listbuild/demo/0'),'示例1')
			->buttonNew(U('admin/builddemo/listbuild/demo/1'),'示例2')
			->search('关键字', 'key', 'text', '无示例数据', '', '', '')
			->search('限定搜索', 'key', 'select', '无示例数据', '', '', array(array('id'=>0,'value'=>'类型1'),array('id'=>1,'value'=>'类型2')))
			->select('筛选界面示例：', 'style', 'select', '', '', '', array(array('id'=>0,'value'=>'类型1'),array('id'=>1,'value'=>'类型2')));
		//批量处理 界面示例
		$build
			->ajaxButton(U('admin/builddemo/listbuild'),'','批处理') //界面示例 数据处理接口木写的
			->buttonModalPopup(U('admin/builddemo/popup'),'','模态窗口批处理',array('target-form'=>'ids')); //界面示例 数据处理接口木写的
		if(!I('demo'))
		{
			//数据结构 示例
			$list = array(
				array('id'     => 1,
				      'title'  => '键值为title',
				      'text'   => '键值为text',
				      'map'    => 0,
				      'yesno'  => 1,
				      'bool'   => 1,
				      'status' => 0,
				      'update_time'=>time(),
				      'create_time'=>time(),
				      'modify_time'=>time(),
				      'content'=>'0123567890000',
				      'image' => 6,
				      'icon'  =>'icon-cloud',

				),
				array('id'     => 1,
				      'title'  => '键值为title',
				      'text'   => '键值为text',
				      'map'    => 1,
				      'yesno'  => 0,
				      'bool'   => 0,
				      'status' => 1),
				array('id'     => 1,
				      'title'  => '键值为title',
				      'text'   => '键值为text',
				      'map'    => 100,
				      'yesno'  => 1,
				      'bool'   => 0,
				      'status' => 2),
				array('id'     => 1,
				      'title'  => '键值为title',
				      'text'   => '键值为text',
				      'map'    => 100,
				      'yesno'  => 1,
				      'bool'   => 0,
				      'status' => -1),
			);

			$build
				->keyText('text','文字示例') //普通文字 加载对应键名的内容
				->keyId()// text 衍生 默认加载键名为id的值 标题默认为id
				->keyTitle()//text 衍生 默认加载键名为title的值 标题默认为标题
				->keyTruncText('content','限制长度的文字',10)//输出 指定长度 的内容
				->keyMap('map','n选择1示例',array('11','22','100'=>'100')) //n选1 在定义的数组中选指定键名的内容
				->keyYesNo('yesno','yesno') // map 衍生 只会返回是否
				->keyBool('bool','bool') //map 衍生 只会返回是否
				->keyStatus() //map 衍生 默认加载键名为status的内容
				->keyImage('image','图片示例')// uctoo_picture 中id
				->keyIcon()
				->keyTime('modify_time','时间示例') //指定键名 用时间函数处理
				->keyCreateTime()// time 衍生 默认处理键名 create_time
				->keyUpdateTime();// time 衍生 默认处理键名 update_time
		}
		else
		{
			//数据结构 示例
			$list = array(
				array(
					'id'    => 1,
					'uid'   => 1,
					'flag'  => 'aaa',
					'join_id'=>1,
					'join2_id'=>1,
					'name'=>'123',
					'html'=>'<a href="http://www.uctoo.cn">uctoo<a/>'
				),
			);
			$build
				->keyUid()
				->keyNickname('uid','用户名')
				->keyLink('link','默认链接示例','admin/builddemo/listbuild2/id/###') //链接类型示例 ### 会有键名id的内容替换 新窗口打开
				->keyDoAction('admin/builddemo/listbuild2/id/###','查看') // link 衍生 本窗口跳转
				->keyDoActionEdit('admin/builddemo/listbuild2/id/###')// link 衍生 指定了标题为编辑 本窗口跳转
				->keyDoActionRestore() //link 衍生 需结合特定字段
				->keyDoActionModalPopup('admin/builddemo/popup/id/###','示例模态窗口','示例模态窗口')
				//模态窗口 示例
				->keyLinkByFlag('name','链接示例','admin/builddemo/listbuild2/flag/###','flag')
				//link衍生 较强大 显示内容又键值为name对应 ###又键值为flag对应内容替换
				->keyJoin('join_id','用户名','uid','nickname','member')
				//显示 后面sql返回结果 select nickname from uctoo_member where uid={join_id}  {join_id} 由对应内容替换
				->keyJoin('join2_id','用户名','uid','nickname','member','/admin/user/index/')
				//显示内容加跳转链接 链接为U('/admin/user/index/',array('nickname'=>{join2_id}))
				->keyHtml('html','原始html','');//前面解决不了的 用这个
		}
		$build
			->data($list)
			->display();
	}


	/*
	 * 模态窗口回调接口
	 */
	public function popup()
	{
		if(IS_POST)
		{
			$this->success('成功操作');
		}
		else{
			//指定前端模板
			$this->display('Builddemo@Builddemo/popup');
		}
	}

	public function configbuild()
	{
		$list =array(
			'id'=>1,
			'title'=>'标题',
			'cnt'=>'123',
			'text'=>'普通文本',
			'textareat'=>'多行输入框',
			'readonly'=>'只读文本',
			'hidden'=>'看不见我看不见我 ',
			'uid'=>1,
			'icon'  =>'icon-cloud',
			'time'  =>time(),
			'create_time'  =>time(),
			'update_time'  =>time(),
			'input1'=>'多控件',
			'input2'=>'1',
			'input3'=>'2',
			'input4'=>'2',
			);
		$build = new AdminConfigBuilder();
		$build
			->title('我是标题')
			->suggest('输入框相关')
			->keyId()
			->keyTitle()
			->keyInteger('cnt','时候显示数字')
			->keyText('text','单行输入框','提示信息')
			->keyTextArea('textareat','多行输入框','提示信息')
			->keyReadOnly('readonly','只读文本','提示信息')
			->keyHidden('hidden','隐藏文本','只有标题，不显示输入框')
			->keyUid('uid','uid','显示键名为uid的键值')
			->keyIcon('icon','我是图标','')
			->keyTime('time','显示时间','','datetime')//显示时间 自动将时间戳处理为 第四个参数设定类型 默认 datetime
			->keyTime('time','显示时间','','date') //只显示 年月日
			->keyTime('time','显示时间','','time')//只显示 时分秒
			->keyCreateTime()//默认 处理键名为create_time的值 以默认时间类型显示
			->keyUpdateTime()//默认 处理键名为update_time的值 以默认时间类型显示
			->keyMultiInput('input1|input2|input3|input4','单行多控件','',
				array(
					array('type'=>'text','style'=>'width:95px;margin-right:5px'),
					array('type'=>'text','style'=>'width:95px;margin-right:5px'),
					array('type'=>'text','style'=>'width:95px;margin-right:5px'),
					array('type'=>'select','style'=>'width:95px;margin-right:5px','opt'=>array('下拉1','下拉2','下拉3')),
				)
				)
			->buttonSubmit()
			->buttonBack()
			->buttonLink('编辑框示例1',array('href'=>U('admin/builddemo/configbuild'),'class'=>'btn'))
			->buttonLink('选项示例2',array('href'=>U('admin/builddemo/configbuild1'),'class'=>'btn'))
			->buttonLink('上传示例3',array('href'=>U('admin/builddemo/configbuild2'),'class'=>'btn'))
			->buttonLink('分组示例4',array('href'=>U('admin/builddemo/configbuild3'),'class'=>'btn'))
			->data($list)
			->display();
	}

	public function configbuild1()
	{
		if(IS_POST)
		{
			var_dump($_REQUEST);exit;//打开调试工具可以看到提交的的数据
		}
		$list =array(
			'radio'=>1,
			'Switch'=>0,
			'Bool'=>1,
			'select'=>1,
			'status'=>1,
			'color'=>'#0033ff',
		    'chose'=>array(1,2,3),
		    'checkbox'=>'1,2,3',
			'province'=>440000,
			'city'=>440300,
			'district'=>440305,
		);
		$build = new AdminConfigBuilder();
		$build
			->title('我是标题')
			->suggest('选框相关的')
			->keyRadio('radio','单选框','保存的是单个键名',array('0','1','2')) //radio对面键值为1 则显示选中第四个参数数组总 键名为1的1
			->keySwitch('Switch','单选框','我是提示')//radio 衍生 默认设置了 数组
			->keyBool('Bool','单选框','我是提示')//radio 衍生 默认设置了 数组
			->keySelect('select','我是下拉框','保存的是单个键名',array('下拉1','下拉2','下拉3'))
			->keyStatus()//select 衍生 默认了设置了数组 默认处理键名status

			->keyColor('color','颜色选择器','保存内容如#0033ff')
			->keyChosen('chose','多选框','保存的是一个或多个键值',array(1,2,3,4,5))
			->keyCheckBox('checkbox','复选框','保存的是一个或多个键名',array(1,2,3,4,5,6))
			->keyCity(array('province','city','district'),'选择你的位置','需安装城市联动插件')
			->buttonSubmit()
			->buttonBack()
			->buttonLink('编辑框示例1',array('href'=>U('admin/builddemo/configbuild'),'class'=>'btn'))
			->buttonLink('选项示例2',array('href'=>U('admin/builddemo/configbuild1'),'class'=>'btn'))
			->buttonLink('上传示例3',array('href'=>U('admin/builddemo/configbuild2'),'class'=>'btn'))
			->buttonLink('分组示例4',array('href'=>U('admin/builddemo/configbuild3'),'class'=>'btn'))
			->data($list)
			->display();
	}

	public function configbuild2()
	{
		$build = new AdminConfigBuilder();
		$data = $build->handleConfig();//自动处理配置储存 数据库 uctoo_config
//		$data = array('MIMAGE'=>'');
		//modC('IMAGE', '', 'BUILDDEMO') //其他位置取回配置内容 示例
		$build
		->title('我是标题')
		->suggest('上传和富文本编辑和自动存储')
		->keySingleImage('IMAGE','单图片上传','我是提示')
		->keyMultiImage('MIMAGE','多图片上传','我是提示')
		->keySingleFile('FILE','单文件上传','')
		->keyMultiFile('MFILE','多文件上传','')
		->keyEditor('CONTENT','内容','富文本编辑器','all')//编辑菜单可根据需要再定义
		->keyEditor('CONTENTS','内容','简单富文本编辑器')
		->buttonSubmit()
		->buttonBack()
		->buttonLink('编辑框示例1',array('href'=>U('admin/builddemo/configbuild'),'class'=>'btn'))
		->buttonLink('选项示例2',array('href'=>U('admin/builddemo/configbuild1'),'class'=>'btn'))
		->buttonLink('上传示例3',array('href'=>U('admin/builddemo/configbuild2'),'class'=>'btn'))
		->buttonLink('分组示例4',array('href'=>U('admin/builddemo/configbuild3'),'class'=>'btn'))
		->data($data)
		->display();
	}

	public function configbuild3()
	{
		$data = array(
			'text1'=>'text1',
			'text2'=>'text2',
			'text3'=>'text3',
			'text4'=>'text4',
			'group1'=>'123'
		);
		$build = new AdminConfigBuilder();
		$build
			->title('我是标题')
			->suggest('选项卡分页，全部数据会一次提交，与单行多控件冲突不能一起用')
			->keytext('text1','text1')
			->keytext('text2','text2')
			->keytext('text3','text3')
			->keytext('text4','text4')
			->group('group1',array('text1','text2'))
			->group('gourp2',array('text1','text2'))
//			->groups(array('groups1'=>array('text1','text2'),'groups2'=>array('text3','text4')))//group 一次分组写法
			->buttonSubmit()
			->buttonBack()
			->buttonLink('编辑框示例1',array('href'=>U('admin/builddemo/configbuild'),'class'=>'btn'))
			->buttonLink('选项示例2',array('href'=>U('admin/builddemo/configbuild1'),'class'=>'btn'))
			->buttonLink('上传示例3',array('href'=>U('admin/builddemo/configbuild2'),'class'=>'btn'))
			->buttonLink('分组示例4',array('href'=>U('admin/builddemo/configbuild3'),'class'=>'btn'))
			->data($data)
			->display();
	}

	/*
	 * 排序页面
	 */
	public function sortbuild()
	{
		$build = new AdminSortBuilder();
		if(IS_POST)
		{
			/*
			 * 返回数据结构
			 * $_REQUEST = array(1) {
							  ["ids"]=>
							  string(7) "1,2,3,4"
							}
			 */
//			$build->doSort('table',$_REQUEST);// dosort 接口 需要操作表 有id 和sort 字段
			var_dump($_REQUEST);exit;
		}
		//数据结果示例
		$data =array(
			array('id'=>'1','title'=>'一'),
			array('id'=>'2','title'=>'二'),
			array('id'=>'3','title'=>'三'),
			array('id'=>'4','title'=>'四'),
		);
		$build
			->title('排序界面示例')
			->data($data)
//			->savePostUrl(U('admin/builddemo/sortbuild'))//自定post提交的链接 根据需要修改 默认为原链接
			->buttonSubmit(U('admin/builddemo/sortbuild'))
			->buttonBack()
			->button('站内已有页面应用',array('href'=>U('/admin/user/sortprofile'),'class'=>'btn'))
			->display();
	}

	public function treebuild()
	{
		//官方 专辑模块 已完美示例
		redirect(U('admin/issue/issue'));
	}
}