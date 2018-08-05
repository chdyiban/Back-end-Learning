<?php
namespace app\index\Controller;
use think\Controller;
use think\Db;
use think\Validate;
use app\index\model\article;
use app\index\model\comment;
class Index extends Controller
{
    public function index()
    {
		$list = article::paginate(5);
		$this->assign('list',$list);
		return $this->fetch();
    }
	public function add(){
		return $this->fetch();
	}
	public function read($id){
		$article=article::get(['id'=>$id]);
		$this->assign('article',$article);
		$comments=$article->comments;
		return view('read',['comments'=>$comments]);
		return $this->fetch();
	}
	public function delete($id){
		$article=article::get($id);
		if($article){
			$article->delete();
			$article->comments()->delete();
			$this->success('删除记录成功','index');
			}
		else{
			$this->error('没有要删除的记录');
			}
		}
	public function addcomment($id){
		$comment=new comment;
		$data['article_id']=$id;
		$data['username']=$_POST['username'];
		$data['content']=$_POST['content'];
		$data['create_time']=time();
		$ret=$comment->save($data);
		if($ret){
			$this->success('添加评论成功','/read/'.$id);
			}
		else{
			$this->error('添加评论错误！');
			}
		
		}
	public function addarticle(){
		$article=new article;
		$data['title']=$_POST['title'];
		$data['content']=htmlspecialchars($_POST['content']);
		$data['create_time']=time();
		$res=$this->validate($data,'article');
		if(true!==$res){

			$this->error($res);
			}
		$ret=$article->save($data);
		if($ret){
			 $this->success('留言发布成功','index');
			}
		else{
			$article->getError();
			}
	}
}
