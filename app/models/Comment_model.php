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

    public function getUserComment($user_id, $pagination, $pagination_rows)
    {
        $current_time = strtotime(date('Y-m-d H:i:s'));
        $recent_time = 7 * 24 * 60 * 60;
        return $this->db->select('comment',
            array(
                '[><]topic' => array('topic_id' => 'id'),
                '[><]user' => array('user_id' => 'id'),
            ),
            array(
                'comment.created_at',
                'comment.topic_id',
                'comment.content',
                'topic.comment_count',
                'topic.title',
                'user.username(author)',
                'user.avatar(default_avatar)',
            ),
            array(
                'AND' => array(
                    'comment.created_at[>]' => $current_time - $recent_time,
                    'comment.user_id' => $user_id,

                ),
                'ORDER' => 'comment.created_at DESC',
                'LIMIT' => [$pagination_rows * ($pagination - 1), $pagination_rows],
            )
        );
    }

    public function getTopicComment($topic_id, $pagination, $pagination_rows)
    {
        return $this->db->select('comment',
            array(
                '[><]user' => array('user_id' => 'id'),
                '[><]user_setting' => array('user_id' => 'user_id'),
            ),
            array(
                'comment.id(comment_id)',
                'comment.user_id',
                'comment.content',
                'comment.created_at',
                'comment.position',
                'user.username',
                'user_setting.avatar',
                'user.avatar(default_avatar)',
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

    public function validateExistComment($topic_id, $comment_id)
    {
        return $this->db->has('comment',
            array(
                'AND' => array(
                    'topic_id' => $topic_id,
                    'id' => $comment_id,
                ),
            )
        );
    }

    public function getCommentInfo($comment_id, $type = '')
    {
        if ($type == 'user_info') {
            $comment_info = $this->db->select('comment',
                array(
                    '[><]user' => array('user_id' => 'id'),
                ),
                array(
                    'user.username',
                    'comment.user_id',
                ),
                array(
                    'comment.id' => $comment_id,
                )
            );
        }
        if ($type == 'topic_info') {
            $comment_info = $this->db->select('comment',
                array(
                    '[><]topic' => array('topic_id' => 'id'),
                ),
                array(
                    'topic.title(topic_title)',
                ),
                array(
                    'comment.id' => $comment_id,
                )
            );
        }
        return $comment_info[0];
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
}
