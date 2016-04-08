<?php
class Node extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function viewAllNode()
    {
        $node = $this->model->Node->getAllNode();
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('node', $node)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function viewNodeTopic($nodename)
    {
        if ($this->model->Node->validateNode('ename', $nodename)) {
            $node_info = $this->model->Node->getNodeInfo('ename', $nodename);
            $pagination_rows = 6;
            $page_rows = ceil($node_info['topic_count'] / $pagination_rows);
            $page = new Page($node_info['topic_count'], $pagination_rows);
            $p = $this->request->input('get.p');
            if (!is_numeric($p) || $p < 1 || $p > $page_rows) {
                $p = '';
            }
            $current_page = empty($p) ? $page_rows : $p;
            $node_topic = $this->model->Topic->getNodeTopic($node_info['id'], $current_page, $pagination_rows);
            $page_link = $page->show($current_page);
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $this->view->assign('page_link', $page_link)->assign('node_topic', $node_topic)->assign('rightBarInfo', $this->rightBarInfo)->assign('node_info', $node_info)->display();
        } else {
            $this->response->setStatus('404');
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $this->view->assign('rightBarInfo', $this->rightBarInfo)->display('Node/nodeNotFound');
        }
    }
}
