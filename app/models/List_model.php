<?php
class List_model extends Kotori_Model
{
    public function getTopicInfo($topic_id = '')
    {
        if ($topic_id != '') {
            return $this->db->select('topic',
                array(
                    'id',
                    'user_id',
                    'title',
                    'created_at',
                )
            );
        }
        return $this->db->select('topic',
            array(
                'id',
                'user_id',
                'title',
                'created_at',
                'comment_count',
                'reply_id',
            )
        );
    }
    public function getUserInfo($id = '')
    {
        if ($id != '') {
            return $this->db->select('user',
                array(
                    'id',
                    'username',
                ),
                array('id' => $id)
            );
        }
    }
}
