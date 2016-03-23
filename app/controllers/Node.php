<?php
class Node extends Base
{
    public function viewNode($nodename)
    {
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
        $this->view->assign('page_link', $page_link)->assign('node_topic', $node_topic)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }
}
