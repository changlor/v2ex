<?php
class Member extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function home($username)
    {
        if ($this->model->User->validateUser('username', $username)) {
            $user_id = $this->model->User->getUserId('username', $username);
            $recent_activity = $this->model->User->getRecentActivity($user_id);
            $comment_keys = array_keys($recent_activity['comment']);
            $comment_last_key = end($comment_keys);
            $this->view->assign('username', $username)->assign('comment_last_key', $comment_last_key)->assign('recent_comments', $recent_activity['comment'])->assign('recent_topics', $recent_activity['topic'])->display();
        } else {
            $this->response->setStatus(404);
            $this->view->display('Member/memberNotFound');
        }
    }

    public function viewMemberComment($username)
    {
        if ($this->model->User->validateUser('username', $username)) {
            $user_id = $this->model->User->getUserId('username', $username);
            $member_comment_count = $this->model->User->getUserCommentCount($user_id);
            $pagination_rows = 6;
            $page_rows = ceil($member_comment_count / $pagination_rows);
            $p = $this->request->input('get.p');
            if (!is_numeric($p) || $p < 1 || $p > $page_rows) {
                $p = '';
            }
            $current_page = empty($p) ? 1 : $p;
            $member_comment = $this->model->Comment->getUserComment($user_id, $current_page, $pagination_rows);
            $comment_keys = array_keys($member_comment);
            $comment_last_key = end($comment_keys);
            $page = new Page($member_comment_count, $pagination_rows);
            $page_link = $page->show($current_page);
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $this->view->assign('page_link', $page_link)->assign('member_comment_count', $member_comment_count)->assign('comment_last_key', $comment_last_key)->assign('member_comment', $member_comment)->assign('rightBarInfo', $this->rightBarInfo)->assign('username', $username)->display();
        } else {
            $this->view->display('Member/memberNotFound');
        }
    }

    public function memberNotice()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function viewMemberTopic($username)
    {
        if ($this->model->User->validateUser('username', $username)) {
            $user_id = $this->model->User->getUserId('username', $username);
            $member_topic_count = $this->model->User->getUserTopicCount($user_id);
            $pagination_rows = 6;
            $page_rows = ceil($member_topic_count / $pagination_rows);
            $p = $this->request->input('get.p');
            if (!is_numeric($p) || $p < 1 || $p > $page_rows) {
                $p = '';
            }
            $current_page = empty($p) ? 1 : $p;
            $member_topic = $this->model->Topic->getUserTopic($user_id, $current_page, $pagination_rows);
            $topic_keys = array_keys($member_topic);
            $topic_last_key = end($topic_keys);
            $page = new Page($member_topic_count, $pagination_rows);
            $page_link = $page->show($current_page);
            $this->rightBarInfo['rightBar'] = array('myInfo');
            $this->view->assign('page_link', $page_link)->assign('member_topic_count', $member_topic_count)->assign('topic_last_key', $topic_last_key)->assign('member_topic', $member_topic)->assign('rightBarInfo', $this->rightBarInfo)->assign('username', $username)->display();
        } else {
            $this->view->display('Member/memberNotFound');
        }
    }
}
