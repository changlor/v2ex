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
            //获取评论信息
            $content = $this->request->input('post.content');
            $content = trim($content);
            $user_id = $this->uid;
            //验证评论是否非法以及是否具有权限
            $handler['topic'] = $this->model->Topic->validateTopicId($topic_id);
            $handler['comment'] = $this->model->Comment->validateComment($topic_id, $user_id, $content);
            $handler['coin'] = $this->model->User->validateUserCoin($this->rightBarInfo['user_record']['coin'], $user_id);
            $isPass = false;
            foreach ($handler as $key => $value) {
                if ($value['msg'] != 'pass') {
                    $isPass = false;
                    break;
                }
                $isPass = true;
            }
            if ($isPass) {
                //组装评论信息
                $comment['id'] = $this->model->Comment->addCommentId();
                $comment['content'] = $content;
                $comment['created_at'] = strtotime(date('Y-m-d H:i:s'));
                $comment['user_id'] = $user_id;
                $comment['topic_id'] = $topic_id;
                //@回复提醒记录
                if (preg_match_all('/@([^\@]\S+)/i', $comment['content'], $matches)) {
                    $notice_user_name = false;
                    foreach ($matches[1] as $key => $value) {
                        if ($this->model->User->validateUser('username', $value)) {
                            $notice_user_name[] = $value;
                            $comment['content'] = str_replace('%' . $value . '%', $value, $comment['content']);
                            $comment['content'] = str_replace($value, '%' . $value . '%', $comment['content']);
                        }
                    }
                    $notice_user_name = array_values(array_flip(array_flip($notice_user_name)));
                    if (count($notice_user_name) >= 1) {
                        $notice_necessary_info = $this->model->Notice->getNoticeNecessaryInfo($notice_user_name, 'mention');
                        foreach ($notice_user_name as $key => $value) {
                            $target_id = $notice_necessary_info['user_id'][$key];
                            $source_id = $user_id;
                            if ($target_id != $source_id) {
                                $notice[$key]['content'] = $comment['content'];
                                $notice[$key]['topic_id'] = $topic_id;
                                $notice[$key]['source_id'] = $source_id;
                                $notice[$key]['target_id'] = $target_id;
                                $notice[$key]['type'] = $notice_necessary_info['type_id'];
                                $notice[$key]['created_at'] = $comment['created_at'];
                                $user_record_update_info = array('notice_count[+]' => 1, 'unread_notice_count[+]' => 1);
                                $this->model->User->updateUserRecord($user_record_update_info, $notice[$key]['target_id']);
                            }
                        }
                        if (isset($notice) && !empty($notice)) {
                            $this->model->Notice->addNotice($notice);
                            unset($notice);
                        }
                    }
                }
                //主题回复提醒记录
                $author_id = $this->model->Topic->getTopicInfo($topic_id, 'author');
                $target_id = $author_id;
                $source_id = $this->uid;
                $notice_type_id = $this->model->Notice->getNoticeTypeId('reply');
                if ($target_id != $source_id) {
                    $notice['content'] = $comment['content'];
                    $notice['topic_id'] = $topic_id;
                    $notice['source_id'] = $source_id;
                    $notice['target_id'] = $target_id;
                    $notice['type'] = $notice_type_id;
                    $notice['created_at'] = $comment['created_at'];
                    $user_record_update_info = array('notice_count[+]' => 1, 'unread_notice_count[+]' => 1);
                    $this->model->User->updateUserRecord($user_record_update_info, $notice['target_id']);
                    $this->model->Notice->addNotice($notice);
                }
                $topic_update_info = array(
                    'reply_id' => $user_id,
                    'replied_at' => $comment['created_at'],
                    'ranked_at' => $comment['created_at'],
                    'comment_count[+]' => 1,
                );
                $this->model->Topic->updateTopicInfo($topic_update_info, $topic_id);
                $user_record_update_info = array('comment_count[+]' => 1);
                $this->model->User->updateUserRecord($user_record_update_info, $user_id);
                $insert_comment_count = $this->model->Comment->addComment($comment);
                //获取收费收益记录的数据
                $reply_user_name = rcookie('NA');
                $topic_info = $this->model->Topic->getTopicInfo($topic_id, 'simplified');
                $author_info = $this->model->Topic->getAuthorInfo($topic_id);
                $deal['deal_id'] = $comment['id'];
                //创建回复,用户的消费
                $consunmption['event_coin'] = -5;
                $consunmption['coin'] = $this->rightBarInfo['user_record']['coin'] + $consunmption['event_coin'];
                $consunmption['user_id'] = $this->uid;
                $consunmption['about'] = '创建了长度为' . count($comment['content']) . '个字符的回复' . ' › ' . '%comment' . $comment['content'] . '%';
                $deal['user_id'] = $author_info['user_id'];
                $this->model->Consumption->commentCost($consunmption, $deal);
                //创建回复,作者的收益
                if ($this->uid != $author_info['user_id']) {
                    $profit['event_coin'] = 5;
                    $profit['coin'] = $author_info['coin'] + $profit['event_coin'];
                    $profit['user_id'] = $author_info['user_id'];
                    $profit['about'] = '收到 %username' . $username . '% 的回复 › ' . $topic_info['title'];
                    $deal['user_id'] = $this->uid;
                    $this->model->Profit->getCommentProfit($profit, $deal);
                }
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
