<?php
class Member extends Base
{
    public function home($username)
    {
        $user_id = $this->model->User->getUserId('username', $username);
        $recent_activity = $this->model->User->getRecentActivity($user_id);
        $this->view->assign('username', $username)->assign('recent_comments', $recent_activity['comments'])->display();
    }
}
