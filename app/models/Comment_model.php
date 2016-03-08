<?php
class Comment_model extends Kotori_Model
{
    public function addComment($comment)
    {
        return $this->db->insert('comment', $comment);
    }

    public function addCommentId()
    {
        $max_id = $this->db->max('commentid', 'id');
        $id = $max_id + 1;
        $last_id = $this->db->insert('commentid',
            array('id' => $id)
        );
        if ($last_id != 0) {
            return $id;
        }
        return '';
    }

    public function getTopicComment($topic_id)
    {
        return $this->db->select('comment',
            array(
                '[><]user' => array('user_id' => 'id'),
            ),
            array(
                'comment.id',
                'comment.user_id',
                'comment.content',
                'user.username',
            ),
            array(
                "ORDER" => 'comment.id ASC',
            )
        );
    }

    public function preventReComment($pos = 0)
    {
        $event = 'legal';
        $topic_id = $this->topic['input']['topic_id'];
        $user_id = $this->topic['input']['user_id'];
        $content = $this->topic['input']['content'];
        $uid = $this->model->User->checkExist('id', $user_id);
        $tid = $this->model->Topic->checkExist('id', $topic_id);
        if ($uid[0]['id'] != '' && $tid[0]['id'] != '') {
            $last_id = $this->model->Topic->checkContent('comment', $content);
            if ($last_id != '') {
                $event = 'illegal';
            }
        }
        //允许重复提交
        if ($pos == 1) {
            $event = 'legal';
        }
        $this->_eventGenerate('recomment', $event, $content);
    }
}
