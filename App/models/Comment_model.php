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
}
