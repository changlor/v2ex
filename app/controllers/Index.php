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
        foreach ($topic as $key => $value) {
            $author = $this->model->List->getUserInfo($value['user_id']);
            $topic[$key]['author'] = $author['0']['username'];
            if ($value['reply_id'] != 0) {
                $user = $this->model->List->getUserInfo($value['reply_id']);
                $topic[$key]['last_reply_name'] = $user['0']['username'];
            }
        }
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->assign('topic', $topic);
        $this->view->display();
    }
}
