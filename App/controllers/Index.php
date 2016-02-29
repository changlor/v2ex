<?php
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }
    //测试一下注释功能
    public function index()
    {
        if ($this->uid != '') {
            $favorite = $this->model->User->getUserFavor($this->uid);
        }
        //echo'<pre>';print_r($favorite);echo'</pre>';
        //$user_info = $this->UserModel->getUserInfo($this->uid);
        $topic = $this->model->Topic->getTopicInfo();
        foreach ($topic as $key => $value) {
            $author = $this->model->List->getUserInfo($value['user_id']);
            $topic[$key]['author'] = $author['0']['username'];
            if ($value['reply_id'] != 0) {
                $user = $this->model->List->getUserInfo($value['reply_id']);
                $topic[$key]['last_reply_name'] = $user['0']['username'];
            }
        }
        //$rightBarInfo['user_info'] = $user_info;
        //echo'<pre>';print_r($topic);echo'</pre>';
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->assign('topic', $topic);
        $this->view->display();
    }
}
