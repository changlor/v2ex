<?php
class Topic_model extends Kotori_Model
{
    public function updateTopicInfo($updateInfo, $topic_id)
    {
        return $this->db->update('topic',
            $updateInfo,
            array('id' => $topic_id)
        );
    }

    public function getTabTopic($tabname)
    {
        return $this->db->select('tab',
            array(
                '[><]tab_node' => array('tab.id' => 'tab_id'),
                '[><]node' => array('tab_node.node_id' => 'id'),
                '[><]topic' => array('tab_node.node_id' => 'node_id'),
            ),
            array(
                'node.ename',
                'node.name',
                'topic.id',
                'topic.user_id',
                'topic.title',
                'topic.created_at',
                'topic.comment_count',
                'topic.replied_at',
                'topic.reply_id',
                'topic.author',
                'topic.last_reply_username',
            ),
            array(
                'tab.ename' => $tabname,
                'ORDER' => 'topic.ranked_at DESC',
            )
        );
    }

    public function getNodeTopic($node_id, $pagination, $pagination_rows)
    {
        return $this->db->select('topic',
            array(
                'title',
                'author',
                'replied_at',
                'created_at',
                'id',
                'comment_count',
                'last_reply_username',
            ),
            array(
                'node_id' => $node_id,
                'ORDER' => 'created_at DESC',
                'LIMIT' => array($pagination_rows * ($pagination - 1), $pagination_rows),
            )
        );
    }

    public function getUserTopic($user_id, $pagination, $pagination_rows)
    {
        $current_time = strtotime(date('Y-m-d H:i:s'));
        $recent_time = 7 * 24 * 60 * 60;
        return $this->db->select('topic',
            array(
                'title',
                'author',
                'replied_at',
                'created_at',
                'id',
                'comment_count',
                'last_reply_username',
            ),
            array(
                'AND' => array(
                    'created_at[>]' => $current_time - $recent_time,
                    'user_id' => $user_id,
                ),
                'ORDER' => 'created_at DESC',
                'LIMIT' => array($pagination_rows * ($pagination - 1), $pagination_rows),
            )
        );
    }

    public function getTopicInfo($topic_id = '')
    {
        if ($topic_id != '') {
            $topic_info = $this->db->select('topic',
                array(
                    'id',
                    'user_id',
                    'title',
                    'client',
                    'comment_count',
                    'reply_id',
                    'replied_at',
                    'created_at',
                    'hits',
                    'author',
                ),
                array(
                    'topic.id' => $topic_id,
                )
            );
            return $topic_info[0];
        }
        $topic = $this->db->select('topic',
            array(
                'id',
                'user_id',
                'title',
                'created_at',
                'comment_count',
                'replied_at',
                'reply_id',
                'author',
                'last_reply_username',
            ),
            array(
                'ORDER' => 'ranked_at DESC',
            )
        );
        return $topic;
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

    public function insertTopic($topic)
    {
        $topic_content['content'] = $topic['content'];
        unset($topic['content']);
        $this->db->insert('topic',
            $topic
        );
        $topic_id = $this->db->select('topic',
            array('id'),
            array('title' => $topic['title'])
        );
        $topic_content['topic_id'] = $topic_id[0]['id'];
        $this->db->insert('topic_content',
            $topic_content
        );
        return $topic_id[0]['id'];
    }

    public function validateTopic($field, $value)
    {
        return $this->db->has('topic',
            array($field => $value)
        );
    }

    public function validateTitle($title)
    {
        $event = 'legal';
        //标题太长
        if (strlen($title) > 120) {
            $event = 'long';
        }
        //标题存在
        if ($this->db->has('topic',
            array(
                'title' => $title,
            )
        )) {
            $event = 'exist';
        }
        //未输入标题
        if ($title == '') {
            $event = 'undefined';
        }
        return eventGenerate('title', $event, $title);
    }

    public function validateContent($content)
    {
        $event = 'legal';
        //内容太长
        if (strlen($content) > 20000) {
            $event = 'long';
        }
        return eventGenerate('content', $event, $content);
    }

    public function mdTagParse($str)
    {
        $md = str_replace(chr(10), '@###@', $str);
        $md .= '@###@';
        if ($md != $str) {
            $pattern = '/(?!=@###@)(.*?)(?<=@###@)/i';
            preg_match_all($pattern, $md, $matches);
            $md = '';
            foreach ($matches[1] as $key => $value) {
                if ($value != '') {
                    $pos = trim($value);
                    if (strpos($pos, '&gt;') === 0) {
                        $temp = '@begin@ ' . $value;
                        $pattern = '/(@begin@[ ]+)((&gt;)+)/i';
                        preg_match_all($pattern, $temp, $matches);
                        $replace['number'] = strlen($matches[2][0]) / 4;
                        $replace['quote'] = '';
                        for ($i = 0; $i < 3 && $i < $replace['number']; $i++) {
                            $replace['quote'] .= '>';
                        }
                        $replace['header'] = str_replace('@begin@ ', '', $matches[1][0]);
                        $temp = str_replace($matches[0][0], $replace['header'] . $replace['quote'], $temp);
                        $md .= str_replace('&gt;', '>', $temp);
                    } else {
                        $md .= $value;
                    }
                }
            }
            $md = str_replace('@###@', chr(10), $md);
            return $md;
        }
        return $str;
    }

    public function mdAttributeParse($str)
    {
        $md = $str;
        $str = str_replace('[', ' [', $str);
        $pattern = '/(\[(.*?)\](\((.*?)( \'(.*?)\')?\)))+/i';
        preg_match_all($pattern, $str, $matches);
        $pattern = '/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|love|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/i';
        foreach ($matches[4] as $key => $value) {
            if (!preg_match($pattern, $value)) {
                $md = str_replace($matches[3][$key], '(javascript:; \'此链接并不是一个有效的链接，因而被替换成无害链接\')', $md);
            }
        }
        return $md;
    }

    public function validateTopicId($topic_id)
    {
        $event = 'legal';
        if (!$this->db->has('topic',
            array(
                'id' => $topic_id,
            )
        )) {
            $event = 'undefined';
        }
        return eventGenerate('topic', $event, $topic_id);
    }
}
