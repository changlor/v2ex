<?php
class Topic extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function viewTopic($topic_id = '')
    {
        $updateInfo = array('hits[+]' => 1);
        $this->model->Topic->updateTopicInfo($updateInfo, $topic_id);
        $topic_info = $this->model->Topic->getTopicInfo($topic_id);
        $topic_content = $this->model->Topic->getTopicContent($topic_id);
        $comment = $this->model->Comment->getTopicComment($topic_id);
        $topic = false;
        $topic = $topic_info;
        $topic['content'] = $topic_content[0]['content'];
        $md = $this->model->Topic->mdTagParse($topic['content']);
        $md = $this->model->Topic->mdAttributeParse($md);
        $md = Markdown::convert($md);
        $md = str_replace('&amp;gt;', '&gt;', $md);
        $topic['content'] = str_replace('&amp;lt;', '&lt;', $md);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('comment', $comment)->assign('rightBarInfo', $this->rightBarInfo)->assign('topic', $topic)->display();
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
            $handler['content'] = $this->model->Topic->validateContent($content);
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
                $topic['ranked_at'] = $topic['created_at'];
                $insert_topic_id = $this->model->Topic->insertTopic($topic);
                $updateInfo = array('topic_count[+]' => '1');
                $this->model->User->updateUserRecord($updateInfo, $this->uid);
                $url = $this->route->url('t/' . $insert_topic_id);
                $this->response->redirect($url, true);
            } else {
                $problem = $this->model->Error->addTopic_error($handler, $title);
                $this->rightBarInfo['rightBar'] = array('tips', 'rules');
                $this->view->assign('problem', $problem)->assign('rightBarInfo', $this->rightBarInfo)->display('Topic/addTopic');
            }
        }
    }
}
