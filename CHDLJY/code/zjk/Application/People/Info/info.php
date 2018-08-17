<?php

return array(
    //模块名
    'name' => 'People',
    //别名
    'alias' => '专家库',
    //版本号
    'version' => '2.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '创新创业导师展示模块，可以用于专家的查找',
    //开发者
    'developer' => 'Yang',
    //开发者网站
    'website' => 'http://ohao.ren',
    //前台入口，可用U函数
    'entry' => 'People/index/index',

    'admin_entry' => 'People/config',

    'icon' => 'group',

    'can_uninstall' => 1
);