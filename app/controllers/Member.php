<?php
class Member extends Base
{
    public function home($username)
    {
        $user_id = $this->model->User->getUserId('username', $username);
        $recent_activity = $this->model->User->getRecentActivity($user_id);
        $keys = array_keys($recent_activity['comment']);
        $last_key = end($keys);
        $this->view->assign('username', $username)->assign('last_key', $last_key)->assign('recent_comments', $recent_activity['comment'])->assign('recent_topics', $recent_activity['topic'])->display();
    }
}
