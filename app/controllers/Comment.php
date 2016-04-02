<?php
class Comment extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertComment($topic_id = '')
    {
        if ($this->request->isPost()) {
            $content = $this->request->input('post.content');
            $content = trim($content);
            $user_id = $this->uid;
            $handler['topic'] = $this->model->Topic->validateTopicId($topic_id);
            $handler['comment'] = $this->model->Comment->validateComment($topic_id, $user_id, $content);
            $isPass = false;
            foreach ($handler as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $isPass = false;
                    break;
                }
                $isPass = true;
            }
            if ($isPass) {
                $comment['id'] = $this->model->Comment->addCommentId();
                $comment['content'] = $content;
                $comment['created_at'] = strtotime(date('Y-m-d H:i:s'));
                $comment['user_id'] = $user_id;
                $comment['topic_id'] = $topic_id;
                if (preg_match_all('/@([a-z]+)/i', $comment['content'], $matches)) {
                    $notice_username = false;
                    foreach ($matches[1] as $key => $value) {
                        if ($this->model->User->validateUser('username', $value)) {
                            $notice_username[] = $value;
                        }
                    }
                    $notice_username = array_unique($notice_username);
                    if (count($notice_username) >= 1) {
                        $notice_necessary_info = $this->model->Notice->getNoticeNecessaryInfo('reply', $notice_username);
                        foreach ($notice_username as $key => $value) {
                            $notice[$key]['content'] = $content;
                            $notice[$key]['topic_id'] = $topic_id;
                            $notice[$key]['source_id'] = $user_id;
                            $notice[$key]['target_id'] = $notice_necessary_info['user_id'][$key]['id'];
                            $notice[$key]['type'] = $notice_necessary_info['type_id'];
                            $notice[$key]['created_at'] = $comment['created_at'];
                        }
                        $this->model->Notice->addNotice($notice);
                        $updateInfo = array('notice_count[+]' => 1, 'unread_notice_count[+]' => 1);
                        $notice_user_id = false;
                        foreach ($notice_necessary_info['user_id'] as $key => $value) {
                            $notice_user_id[] = $value['id'];
                        }
                        $this->model->User->updateUserRecord($updateInfo, $user_id);
                    }
                }
                $updateInfo = array(
                    'reply_id' => $user_id,
                    'last_reply_username' => rcookie('NA'),
                    'replied_at' => $comment['created_at'],
                    'ranked_at' => $comment['created_at'],
                    'comment_count[+]' => 1,
                );
                $this->model->Topic->updateTopicInfo($updateInfo, $topic_id);
                $updateInfo = array('comment_count[+]' => 1);
                $this->model->User->updateUserRecord($updateInfo, $user_id);
                $insert_comment_count = $this->model->Comment->addComment($comment);
                $url = $this->route->url('t/' . $topic_id . '#reply' . $insert_comment_count);
                $this->response->redirect($url, true);
            } else {
                $topic_info = $this->model->Topic->getTopicInfo($topic_id);
                $topic = $topic_info;
                $this->rightBarInfo['rightBar'] = array('myInfo');
                $problem = $this->model->Error->addComment_error($handler);
                $this->view->assign('problem', $problem)->assign('topic', $topic)->assign('rightBarInfo', $this->rightBarInfo)->assign('repeated_comment', $content)->display('Comment/addComment');
            }
        }
    }
}
