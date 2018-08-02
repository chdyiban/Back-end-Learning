<?php
namespace app\index\model;
use think\Model;
class article extends model{
	protected $name='article';
	public function comments(){
		return $this->hasMany('comment');
		}
	}
?>