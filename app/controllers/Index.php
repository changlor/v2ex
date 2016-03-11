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
            $author = $this->model->User->getUserInfo($value['user_id']);
            $topic[$key]['author'] = $author['username'];
            if ($value['reply_id'] != 0) {
                $user = $this->model->User->getUserInfo($value['reply_id']);
                $topic[$key]['last_reply_name'] = $user['username'];
            }
        }
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('topic', $topic)->display();
    }
}
