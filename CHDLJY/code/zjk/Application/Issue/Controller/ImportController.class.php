<?php


namespace Issue\Controller;

use Think\Controller;


class ImportController extends Controller
{
	public function index(){
		//header("Content-type:text/html;charset=gbk");
		$file = fopen('/Users/yang/Desktop/1.csv','r');
		while($data = fgetcsv($file)){
			
			foreach ($data as $key => $value) {
				
				$data[$key] = mb_convert_encoding($value,'utf-8', 'gbk');

			}
			$write_data = $this->write($data);
			dump($write_data);

			$issue_list[] = $data;

		}
		dump($issue_list);
		fclose($file);
	}

	private function write($value){
		$remark['mobile'] = $value[4];
		$remark['advisor'] = $value[5];
		$remark['type'] = $value[7];

		$data['id'] = 16+ $value[0];
		$data['title'] = $value[2];
		$data['type'] = 0;
		$data['bind_unitech'] = 0;
		$data['description'] = '略';
		$data['content'] = '见项目计划书(仅导师可见）';
		$data['tc_name'] = $value[3];
		$data['stage'] = 0;
		$data['step'] = 0;
		$data['cover_id'] = 65534;
		$data['plan_id'] = 0;
		$data['uid'] = 0;
		$data['status'] = 1;
		$data['remark'] = json_encode($remark);

		if(M('issue_content')->add($data)){
			return true;
		}else{
			return false;
		}
	}

	public function getName()
    {
        $name = '';
        $rule = array('uniqid', '');
        $filename = 'test';
        if (is_array($rule)) { //数组规则
            $func = $rule[0];
            $param = (array)$rule[1];
            foreach ($param as &$value) {
                $value = str_replace('__FILE__', $filename, $value);
            }
            $name = call_user_func_array($func, $param);
        } elseif (is_string($rule)) { //字符串规则
            if (function_exists($rule)) {
                $name = call_user_func($rule);
            } else {
                $name = $rule;
            }
        }
        echo $name;
    }
}