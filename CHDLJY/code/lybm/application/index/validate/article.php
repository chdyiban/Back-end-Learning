<?php
namespace app\index\validate;
use think\validate;
class article extends validate{
	protected $rule=[
		'title|标题'=>'require',
		'content|内容'=>'require',
		];
	}
?>