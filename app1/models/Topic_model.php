<?php
class Topic_model extends Kotori_Model
{
    public function getTopicInfo($topic_id = '')
    {
        if ($topic_id != '') {
            return $this->db->select('topic',
                array(
                    '[><]user' => array('user_id' => 'id'),
                ),
                array(
                    'topic.id',
                    'topic.user_id',
                    'topic.title',
                    'topic.reply_id',
                    'user.username',
                ),
                array(
                    'topic.id' => $topic_id,
                )
            );
        }
        return $this->db->select('topic',
            array(
                'id',
                'user_id',
                'title',
                'comment_count',
                'reply_id',
            )
        );
    }

    public function addTopic($topic)
    {
        $topic_content['content'] = $topic['content'];
        unset($topic['content']);
        if (is_array($topic)) {
            $last_id = $this->db->insert('topic',
                $topic
            );
            if ($last_id != 0) {
                $topic_id = $this->db->select('topic',
                    array('id'),
                    array('title' => $topic['title'])
                );
            }
            if ($topic_id[0]['id'] != '') {
                $topic_content['topic_id'] = $topic_id[0]['id'];
                $last_id = array();
                $last_id['effect'] = $this->db->insert('topic_content',
                    $topic_content
                );
                $last_id['topic_id'] = $topic_id[0]['id'];
                return $last_id;
            }
        }
    }

    public function getTopicContent($topic_id = '')
    {
        if ($topic_id != '') {
            return $this->db->select('topic_content',
                array(
                    'topic_id',
                    'content',
                ),
                array(
                    'topic_id' => $topic_id,
                )
            );
        }
    }

    public function checkExist($level, $value)
    {
        return $this->db->select('topic',
            array(
                'id',
            ),
            array(
                $level => $value,
            )
        );
    }

    public function checkContent($type, $value)
    {
        $level = 'id';
        if ($type == 'topic_content') {
            $level = 'topic_id';
        }
        $last_id = $this->db->select($type,
            array($level),
            array(
                'content' => $value,
            )
        );
        if (isset($last_id[0][$level])) {
            return $last_id[0][$level];
        }
        return '';
    }

    public function updateTopicInfo($newInfo, $topic_id)
    {
        return $this->db->update('topic',
            $newInfo,
            array('id' => $topic_id)
        );
    }
}
