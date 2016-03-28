<?php
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $tabname = $this->request->input('get.tab');
        $tabname = empty($tabname) ? 'chaos' : $tabname;
        $tab_node = $this->model->Node->getTabNode($tabname);
        $topic = $this->model->Topic->getTabTopic($tabname);
        $topic_keys = array_keys($topic);
        $topic_first_key = reset($topic_keys);
        $this->view->assign('topic_first_key', $topic_first_key)->assign('tab_node', $tab_node)->assign('rightBarInfo', $this->rightBarInfo)->assign('topic', $topic)->display();
    }
}
