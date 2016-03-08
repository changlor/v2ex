<?php
class Comment extends Base
{
    public function insertComment($topic_id = '')
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
    }
}
