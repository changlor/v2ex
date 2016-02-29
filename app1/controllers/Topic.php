<?php
class Topic extends Base
{
    protected $topic = array('input', 'output');
    public function __construct()
    {
        parent::__construct();
    }

    public function viewTopic($topic_id = '')
    {
        if ($this->request->isPost()) {
            $this->topic['input']['content'] = $this->request->input('post.content');
            $this->topic['input']['user_id'] = $this->uid;
            $this->topic['input']['topic_id'] = $topic_id;
            EventHandle::checkUserId();
            EventHandle::checkTopicId();
            //print_r($this->topic['input']);
            EventHandle::preventReComment();
            $pos = 'pass';
            foreach ($this->topic['output'] as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $pos = 'failure';
                }
            }
            if ($pos == 'pass') {
                $this->topic['input']['id'] = $this->model->Comment->addCommentId();
                $newInfo = array('reply_id' => $this->topic['input']['id']);
                $last_id = $this->model->Topic->updateTopicInfo($newInfo, $topic_id);
                if ($last_id == 0) {
                    $pos = 'failure';
                }
            }
            if ($pos == 'pass') {
                $last_id = $this->model->Comment->addComment($this->topic['input']);
            }
            if (!isset($last_id)) {
                $last_id = 0;
            }
            if ($pos == 'failure' || $last_id == 0) {
                $this->view->assign('output', $this->topic['output']);
            }
            if ($pos == 'pass') {
                $url = $this->route->url('t/' . $topic_id);
                $this->response->redirect($url, true);
            }
        }
        $topic_info = $this->model->Topic->getTopicInfo($topic_id);
        $topic_content = $this->model->Topic->getTopicContent($topic_info[0]['id']);
        $topic = '';
        foreach ($topic_info[0] as $key => $value) {
            $topic[$key] = $value;
        }
        $topic['content'] = $topic_content[0]['content'];
        $md = EventHandle::gtParse($topic['content']);
        $md = EventHandle::hrefParse($md);
        $md = Markdown::convert($md);
        $md = str_replace('&amp;gt;', '&gt;', $md);
        $topic['content'] = str_replace('&amp;lt;', '&lt;', $md);
        $comment = $this->model->Comment->getTopicComment($topic_id);
        $this->view->assign('comment', $comment);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->assign('topic', $topic);
        $this->view->display();
    }

    public function addTopic()
    {
        if ($this->request->isAjax()) {
            $md = $this->request->input('post.md');
            //$md = htmlspecialchars_decode($md);
            $md = EventHandle::gtParse($md);
            $md = EventHandle::hrefParse($md);
            $md = Markdown::convert($md);
            $md = str_replace('&amp;gt;', '&gt;', $md);
            $md = str_replace('&amp;lt;', '&lt;', $md);
            //$md = htmlspecialchars($md);
            $this->response->throwJson($md);
        }
        if ($this->request->isPost()) {
            $this->topic['input']['title'] = $this->request->input('post.title');
            $this->topic['input']['title'] = trim($this->topic['input']['title']);
            $this->topic['input']['content'] = $this->request->input('post.content');
            EventHandle::checkTitle();
            EventHandle::checkContent();
            EventHandle::preventReComment();
            $pos = 'pass';
            foreach ($this->topic['output'] as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $pos = 'failure';
                }
            }
            if ($pos == 'failure') {
                $this->view->assign('output', $this->topic['output']);
                //print_r($this->topic['output']);
                //$this->view->display();
            }
            if ($pos == 'pass') {
                $this->topic['input']['user_id'] = $this->uid;
                $last_id = $this->model->Topic->addTopic($this->topic['input']);
                $topic_id = $last_id['topic_id'];
                if ($last_id['effect'] != 0) {
                    $newInfo = array('topic_count[+]' => '1');
                    $last_id = $this->model->User->updateUserInfo($newInfo, $this->uid);
                    if ($last_id != 0) {
                        $url = $this->route->url('t/' . $topic_id);
                        $this->response->redirect($url, true);
                    }
                }
            }
        }
        //$user_info = $this->model->User->getUserInfo($this->uid);
        //$rightBarInfo['user_info'] = $user_info;
        $this->rightBarInfo['rightBar'] = array('tips', 'rules');
        $this->view->assign('rightBarInfo', $this->rightBarInfo);
        $this->view->display();
    }
}
