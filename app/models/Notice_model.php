<?php
class Notice_model extends Kotori_Model
{
    public function getMemberNotice($user_id, $pagination, $pagination_rows)
    {
        return $this->db->select('notice',
            array(
                '[><]user' => array('notice.source_id' => 'id'),
                '[><]topic' => array('notice.topic_id' => 'id'),
            ),
            array(
                'notice.id(notice_id)',
                'notice.created_at',
                'notice.type',
                'notice.status',
                'notice.source_id',
                'notice.topic_id',
                'notice.position',
                'notice.content',
                'topic.title',
                'topic.comment_count',
                'user.username',
            ),
            array(
                'target_id' => $user_id,
                'ORDER' => 'notice.created_at DESC',
                'LIMIT' => array($pagination_rows * ($pagination - 1), $pagination_rows),
            )
        );
    }

    public function getNoticeNecessaryInfo($type, $username)
    {
        $notice_type_id = $this->db->select('type',
            array('id'),
            array('ename' => $type)
        );
        $notice_id = $this->db->select('user',
            array('id'),
            array(
                'username' => $username,
            )
        );
        $info['type_id'] = $notice_type_id[0]['id'];
        $info['user_id'] = $notice_id;
        return $info;
    }

    public function addNotice($notice)
    {
        return $this->db->insert('notice',
        	$notice
        );
    }
}
