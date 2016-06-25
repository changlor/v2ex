<?php
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '1';
        //获取tab名和tab下的主题
        $tab_name = $this->request->input('get.tab');
        $tab_name = empty($tab_name) ? 'chaos' : $tab_name;
        $topic = $this->model->Topic->getTabTopic($tab_name);
        //获取tab下的结点
        $tab_node = $this->model->Node->getTabNode($tab_name);
        $this->view->assign('tab_node', $tab_node)->assign('rightBarInfo', $this->rightBarInfo)->assign('topic', $topic)->display();
    }
}
