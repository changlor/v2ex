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
}
