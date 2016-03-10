<?php
class Topic extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function viewTopic($topic_id = '')
    {
        $topic_info = $this->model->Topic->getTopicInfo($topic_id);
        $topic_content = $this->model->Topic->getTopicContent($topic_id);
        $comment = $this->model->Comment->getTopicComment($topic_id);
        $topic = false;
        $topic = $topic_info[0];
        $topic['content'] = $topic_content[0]['content'];
        $md = $this->model->Topic->mdTagParse($topic['content']);
        $md = $this->model->Topic->mdAttributeParse($md);
        $md = Markdown::convert($md);
        $md = str_replace('&amp;gt;', '&gt;', $md);
        $topic['content'] = str_replace('&amp;lt;', '&lt;', $md);
        $this->view->assign('comment', $comment);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->assign('topic', $topic);
        $this->view->display();
    }

    public function addTopic()
    {
        $this->rightBarInfo['rightBar'] = array('tips', 'rules');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function previewTopic()
    {
        if ($this->request->isAjax()) {
            $md = $this->request->input('post.md');
            $md = $this->model->Topic->mdTagParse($md);
            $md = $this->model->Topic->mdAttributeParse($md);
            $md = Markdown::convert($md);
            $md = str_replace('&amp;gt;', '&gt;', $md);
            $md = str_replace('&amp;lt;', '&lt;', $md);
            $this->response->throwJson($md);
        }
    }

    public function insertTopic()
    {
        if ($this->request->isPost()) {
            $title = $this->request->input('post.title');
            $title = trim($title);
            $content = $this->request->input('post.content');
            $handler['title'] = $this->model->Topic->validateTitle($title);
            $handler['content'] = $this->model->Topic->validateContent($title);
            $isPass = false;
            foreach ($handler as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $isPass = false;
                    break;
                }
                $isPass = true;
            }
            if ($isPass) {
                $topic['title'] = $title;
                $topic['user_id'] = $this->uid;
                $topic['content'] = $content;
                $topic['created_at'] = strtotime(date('Y-m-d H:i:s'));
                $topic_id = $this->model->Topic->insertTopic($topic);
                $newInfo = array('topic_count[+]' => '1');
                $this->model->User->updateUserInfo($newInfo, $this->uid);
                $url = $this->route->url('t/' . $topic_id);
                $this->response->redirect($url, true);
            } else {
                print_r($handler);
                $problem = $this->model->Error->addTopic_error($handler, $title);
                $this->rightBarInfo['rightBar'] = array('tips', 'rules');
                $this->view->assign('problem', $problem)->assign('rightBarInfo', $this->rightBarInfo)->view->display('Topic/addTopic');
            }
        }
    }
}
