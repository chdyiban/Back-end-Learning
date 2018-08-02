<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
	'add'=>'index/add',
	'read/[:id]$'=>'index/read',
	'addarticle'=>'index/addarticle',
	'delete/:id'=>['index/delete',['method'=>'get'],['id'=>'\d+']],
	'addcomment/:id'=>'index/addcomment',
];
