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
                'notice.type_id',
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

    public function getNoticeNecessaryInfo($username, $type)
    {
        $notice_type_id = $this->db->select('notice_type',
            array('id'),
            array('ename' => $type)
        );
        $notice_id = $this->db->select('user',
            array('id'),
            array(
                'username' => $username,
            )
        );
        $info = false;
        foreach ($notice_id as $key => $value) {
            $info['user_id'][$key] = $value['id'];
        }
        $info['type_id'] = $notice_type_id[0]['id'];
        return $info;
    }

    public function addNotice($notice)
    {
        return $this->db->insert('notice',
            $notice
        );
    }

    public function getNoticeTypeId($notice_type)
    {
        $notice_type_id = $this->db->select('notice_type',
            array('id'),
            array('ename' => $notice_type)
        );
        return $notice_type_id[0]['id'];
    }

    public function getMemberNoticeMessage($notice_info)
    {
        $notice_type = $this->db->select('notice_type',
            array(
                'ename',
            ),
            array(
                'id' => $notice_info['notice_type_id'],
            )
        );
        if ($notice_type[0]['ename'] == 'mention') {
            $notice_message = '<a href="/member/';
            $notice_message .= $notice_info['username'];
            $notice_message .= '"><strong>';
            $notice_message .= $notice_info['username'];
            $notice_message .= '</strong></a> 在回复 <a href="/t/';
            $notice_message .= $notice_info['topic_id'];
            $notice_message .= '#reply';
            $notice_message .= $notice_info['comment_count'];
            $notice_message .= '">';
            $notice_message .= $notice_info['title'];
            $notice_message .= '</a> 时提到了你';
        }
        if ($notice_type[0]['ename'] == 'reply') {
            $notice_message = '<a href="/member/';
            $notice_message .= $notice_info['username'];
            $notice_message .= '"><strong>';
            $notice_message .= $notice_info['username'];
            $notice_message .= '</strong></a> 在 <a href="/t/';
            $notice_message .= $notice_info['topic_id'];
            $notice_message .= '#reply';
            $notice_message .= $notice_info['comment_count'];
            $notice_message .= '">';
            $notice_message .= $notice_info['title'];
            $notice_message .= '</a> 回复了你';
        }
        return $notice_message;
    }
}
