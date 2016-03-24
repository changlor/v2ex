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
        $topic_keys = array_keys($topic);
        $topic_first_key = reset($topic_keys);
        $this->view->assign('topic_first_key', $topic_first_key)->assign('rightBarInfo', $this->rightBarInfo)->assign('topic', $topic)->display();
    }
}
