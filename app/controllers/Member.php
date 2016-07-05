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
            $rank = new Rank($username);
            $user_rank = $rank->getUserRank();
            $user_id = $this->model->User->getUserId('username', $username);
            $recent_activity = $this->model->User->getRecentActivity($user_id);
            foreach ($recent_activity['comment'] as $key => $value) {
                $recent_activity['comment'][$key] = preg_replace('/@%([a-z0-9]+)%/i', '@<a href="' . $this->route->url('member/' . '$1') . '" title="$1">$1</a>', $value);
            }
            $comment_keys = array_keys($recent_activity['comment']);
            $comment_last_key = end($comment_keys);
            $user_setting = $this->model->User->getUserSetting($user_id);
            $use_avatar = $this->model->User->ifUseAvatar($user_id);
            $is_follow = $this->model->Favorite->isFavoriteMember($user_id, $this->uid);
            $this->view->assign('username', $username)->assign('is_follow', $is_follow)->assign('comment_last_key', $comment_last_key)->assign('recent_comments', $recent_activity['comment'])->assign('user_rank', $user_rank)->assign('recent_topics', $recent_activity['topic'])->assign('user_id', $user_id)->assign('use_avatar', $use_avatar)->assign('user_setting', $user_setting)->display();
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
        //设置提醒分页
        $pagination_rows = 6;
        $page_rows = ceil($member_notice_count / $pagination_rows);
        $p = $this->request->input('get.p');
        if (!is_numeric($p) || $p < 1 || $p > $page_rows) {
            $p = '';
        }
        $current_page = empty($p) ? 1 : $p;
        //获取提醒分页信息
        $notice = $this->model->Notice->getMemberNotice($this->uid, $current_page, $pagination_rows);
        foreach ($notice as $key => $value) {
            $notice[$key] = preg_replace('/@%([a-z0-9]+)%/i', '@<a href="' . $this->route->url('member/' . '$1') . '" title="$1">$1</a>', $value);
        }
        //获取提醒分页链接
        $page = new Page($member_notice_count, $pagination_rows);
        $page_link = $page->show($current_page);
        //获取提醒信息
        foreach ($notice as $key => $value) {
            $notice_message = false;
            $notice_message['username'] = $value['username'];
            $notice_message['comment_count'] = $value['comment_count'];
            $notice_message['title'] = $value['title'];
            $notice_message['topic_id'] = $value['topic_id'];
            $notice_message['notice_type_id'] = $value['type_id'];
            $notice[$key]['notice_message'] = $this->model->Notice->getMemberNoticeMessage($notice_message);
        }
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

    public function queryIp()
    {
        $user_ip = $this->request->input('get.ip');
        $ip = new Ip($user_ip);
        $ip_info = $ip->addr();
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('rightBarInfo', $this->rightBarInfo)->assign('ip', $user_ip)->assign('ip_info', $ip_info)->display();
    }
}
