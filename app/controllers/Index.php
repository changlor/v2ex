<?php
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $topic = $this->model->Topic->getTopicInfo();
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('topic', $topic)->display();
    }

    public function fenci_test(){
    	$text = '程序员喝酒后可以写代码么？ 0 0 貌似之前认识的一个小伙伴，喝两口就埋头码起来了';
    	print_r(get_tags_arr($text));
    	print_r(get_keywords_str($text));
    }
}
