<?php
class User_model extends Kotori_Model
{
    public function getMemberNoticeCount($user_id)
    {
        $member_notice_count = $this->db->select('user_record',
            array(
                'notice_count',
            ),
            array(
                'user_id' => $user_id,
            )
        );
        return $member_notice_count[0]['notice_count'];
    }

    public function getUserInfo($user_id)
    {
        $user_info = $this->db->select('user',
            array(
                'id', 'username',
            ),
            array(
                'id' => $user_id,
            )
        );
    }

    public function getUserCommentCount($user_id)
    {
        return $this->db->count('comment',
            array('user_id' => $user_id)
        );
    }

    public function getUserTopicCount($user_id)
    {
        return $this->db->count('topic',
            array('user_id' => $user_id)
        );
    }

    public function getRecentActivity($user_id)
    {
        $current_time = strtotime(date('Y-m-d H:i:s'));
        $recent_time = 7 * 24 * 60 * 60;
        $recentActivity['topic'] = $this->db->select('topic',
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
                'LIMIT' => [0, 3],
            )
        );
        $recentActivity['comment'] = $this->db->select('comment',
            array(
                '[><]topic' => array('topic_id' => 'id'),
            ),
            array(
                'comment.created_at',
                'comment.topic_id',
                'comment.content',
                'topic.comment_count',
                'topic.title',
                'topic.author',
            ),
            array(
                'AND' => array(
                    'comment.created_at[>]' => $current_time - $recent_time,
                    'comment.user_id' => $user_id,

                ),
                'ORDER' => 'comment.created_at DESC',
                'LIMIT' => [0, 6],
            )
        );
        return $recentActivity;
    }

    public function getUserRecord($user_id)
    {
        $user_record = $this->db->select('user_record',
            array(
                'favorite_node_count',
                'favorite_topic_count',
                'favorite_user_count',
                'unread_notice_count',
                'coin',
            ),
            array(
                'user_id' => $user_id,
            )
        );
        return $user_record[0];
    }

    public function getUserRole($user_id)
    {
        $user_role = $this->db->select('user',
            array(
                'status',
                'role',
            ),
            array(
                'id' => $user_id,
            )
        );
        return $user_role[0];
    }

    public function signin($user)
    {
        return $this->db->insert('user',
            $user
        );
    }
    public function createUserRecord($user_id)
    {
        return $this->db->insert('user_record',
            array('user_id' => $user_id)
        );
    }

    public function updateUserRecord($updateInfo, $uid)
    {
        return $this->db->update('user_record',
            $updateInfo,
            array('user_id' => $uid)
        );
    }

    public function updateUserInfo($updateInfo, $uid)
    {
        return $this->db->update('user',
            $updateInfo,
            array('id' => $uid)
        );
    }

    public function getUserId($field, $value)
    {
        $user_id = $this->db->select('user',
            array('id'),
            array(
                $field => $value,
            )
        );
        return $user_id[0]['id'];
    }

    public function validateUser($field, $value)
    {
        return $this->db->has('user',
            array(
                $field => $value,
            )
        );
    }

    public function validatePass($username)
    {
        $auth_key = $this->db->select('user',
            array('password_hash', 'auth_key'),
            array('username' => $username)
        );
        return $auth_key[0];
    }

    public function validateUsername($username)
    {
        $event = 'legal';
        //用户名太长
        if (strlen($username) > 16) {
            $event = 'long';
        }
        //用户名不合法
        if (!ctype_alnum($username)) {
            $event = 'illegal';
        }
        //用户名存在
        if ($this->db->has('user',
            array(
                'username' => $username,
            )
        )) {
            $event = 'exist';
        }
        //未输入用户名
        if ($username == '') {
            $event = 'undefined';
        }
        return eventGenerate('username', $event, $username);
    }

    public function validateEmail($email)
    {
        $event = 'legal';
        //邮箱格式错误
        $pattern = '/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/i';
        if (!preg_match($pattern, $email)) {
            $event = 'illegal';
        }
        //邮箱存在
        if ($this->db->has('user',
            array(
                'email' => $email,
            )
        )) {
            $event = 'exist';
        }
        //未输入邮箱
        if ($email == '') {
            $event = 'undefined';
        }
        return eventGenerate('email', $event, $email);
    }

    public function validateUserId($user_id)
    {
        $event = 'legal';
        //用户不存在
        if (!$this->db->has('user',
            array(
                'id' => $user_id,
            )
        )) {
            $event = 'undefined';
        }
        return eventGenerate('user', $event, $user_id);
    }

    public function validateUserCoin($coin, $user_id)
    {
        $event = 'legal';
        //请充值，这样才能变得更强
        if ($coin < 20) {
            $event = 'lack';
        }
        return eventGenerate('user', $event, $user_id);
    }

    public function getUserAsset($user_id)
    {
        return $this->db->select('user_asset',
            array(
                'type',
                'about',
                'event_coin',
                'coin',
                'created_at',
            ),
            array(
                'user_id' => $user_id,
            )
        );
    }

    public function getUserCoin($user_id)
    {
        $coin = $this->db->select('user_record',
            array(
                'coin',
            ),
            array(
                'user_id' => $user_id,
            )
        );
        return $coin[0]['coin'];
    }
}
