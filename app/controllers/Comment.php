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
                $updateInfo = array(
                    'reply_id' => $user_id,
                    'replied_at' => $comment['created_at'],
                    'ranked_at' => $comment['created_at'],
                    'comment_count[+]' => 1,
                );
                $this->model->Topic->updateTopicInfo($updateInfo, $topic_id);
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
