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
            foreach ($recent_activity['comment'] as $key => $value) {
                $recent_activity['comment'][$key] = preg_replace('/@%([a-z0-9]+)%/i', '@<a href="' . $this->route->url('member/' . '$1') . '" title="$1">$1</a>', $value);
            }
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

    public function viewMemberNotice()
    {
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $member_notice_count = $this->model->User->getMemberNoticeCount($this->uid);
        $pagination_rows = 6;
        $page_rows = ceil($member_notice_count / $pagination_rows);
        $p = $this->request->input('get.p');
        if (!is_numeric($p) || $p < 1 || $p > $page_rows) {
            $p = '';
        }
        $current_page = empty($p) ? 1 : $p;
        $notice = $this->model->Notice->getMemberNotice($this->uid, $current_page, $pagination_rows);
        foreach ($notice as $key => $value) {
            $notice[$key] = preg_replace('/@%([a-z0-9]+)%/i', '@<a href="' . $this->route->url('member/' . '$1') . '" title="$1">$1</a>', $value);
        }
        $page = new Page($member_notice_count, $pagination_rows);
        $page_link = $page->show($current_page);
        $this->view->assign('member_notice_count', $member_notice_count)->assign('rightBarInfo', $this->rightBarInfo)->assign('page_link', $page_link)->assign('notice', $notice)->display();
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

    public function memberTask($task_id)
    {
        echo $task_id = decrypt($task_id, 'kotori');
        if (is_numeric($task_id) && $this->model->Task->isUndoTask($task_id, $this->uid)) {
            $task = $this->model->Task->getTaskInfo($task_id);
            $task['user_id'] = $this->uid;
            $task['task_id'] = $task['id'];
            if ($task['role'] == 0) {
                $update_info = array('status' => 3);
                $this->model->User->updateUserInfo($update_info, $this->uid);
            }
            $update_info = array('coin[+]' => $task['coin']);
            $this->model->User->updateUserRecord($update_info, $this->uid);
            $task['task_coin'] = $task['coin'];
            $task['coin'] = $task['coin'] + $this->rightBarInfo['user_record']['coin'];
            $task['created_at'] = strtotime(date('Y-m-d H:i:s'));
            unset($task['id']);
            unset($task['role']);
            print_r($task);
            $this->model->Task->doneTask($task);
        }
    }
}
