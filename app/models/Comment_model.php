<?php
class Comment_model extends Kotori_Model
{
    public function addComment($comment)
    {
        $this->db->insert('comment', $comment);
        $topic_comment_count = $this->db->select('topic',
            array(
                'comment_count',
            ),
            array(
                'id' => $comment['topic_id'],
            )
        );
        return $topic_comment_count[0]['comment_count'];
    }

    public function addCommentId()
    {
        $max_id = $this->db->max('commentid', 'id');
        $id = $max_id + 1;
        $this->db->insert('commentid',
            array('id' => $id)
        );
        return $id;
    }

    public function getTopicComment($topic_id, $pagination, $pagination_rows)
    {
        return $this->db->select('comment',
            array(
                '[><]user' => array('user_id' => 'id'),
            ),
            array(
                'comment.id',
                'comment.user_id',
                'comment.content',
                'comment.created_at',
                'comment.position',
                'user.username',
            ),
            array(
                'comment.topic_id' => $topic_id,
                'LIMIT' => array($pagination_rows * ($pagination - 1), $pagination_rows),
                'ORDER' => 'comment.id ASC',
            )
        );
    }

    public function validateComment($topic_id, $user_id, $content, $pos = 0)
    {
        $event = 'legal';
        if ($this->db->has('comment',
            array(
                'AND' => array(
                    'user_id' => $user_id,
                    'topic_id' => $topic_id,
                    'content' => $content,
                ),
            ))) {
            $event = 'repeated';
            //允许重复提交
            if ($pos == 1) {
                $event = 'legal';
            }
        }
        if (trim($content) == '') {
            $event = 'undefined';
        }
        return eventGenerate('comment', $event, $content);
    }
}
