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
        return $user_info = $this->db->select('user',
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
                'replied_at',
                'reply_id',
                'user_id(author_id)',
                'created_at',
                'id',
                'comment_count',
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
        if (!empty($recentActivity['topic'])) {
            $user_id = false;
            foreach ($recentActivity['topic'] as $key => $value) {
                $user_id[] = $value['author_id'];
                $user_id[] = $value['reply_id'];
            }
            $user_id = array_flip(array_flip($user_id));
            $user_info = $this->model->User->getUserInfo($user_id);
            $user_id_to_name = false;
            foreach ($user_info as $key => $value) {
                $user_id_to_name[$value['id']] = $value['username'];
            }
            foreach ($recentActivity['topic'] as $key => $value) {
                $recentActivity['topic'][$key]['author'] = $user_id_to_name[$value['author_id']];
                $recentActivity['topic'][$key]['last_reply_username'] = (isset($user_id_to_name[$value['reply_id']])) ? $user_id_to_name[$value['reply_id']] : '';
            }
        }
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
                'topic.user_id(author_id)',
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
        if (!empty($recentActivity['comment'])) {
            $user_id = false;
            foreach ($recentActivity['comment'] as $key => $value) {
                $user_id[] = $value['author_id'];
            }
            $user_id = array_flip(array_flip($user_id));
            $user_info = $this->model->User->getUserInfo($user_id);
            $user_id_to_name = false;
            foreach ($user_info as $key => $value) {
                $user_id_to_name[$value['id']] = $value['username'];
            }
            foreach ($recentActivity['comment'] as $key => $value) {
                $recentActivity['comment'][$key]['author'] = $user_id_to_name[$value['author_id']];
            }
        }
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
        $user_position = $this->db->max('user',
            'position'
        );
        $user_position++;
        $user['position'] = $user_position;
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

    public function createUserSetting($user_id)
    {
        return $this->db->insert('user_setting',
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

    public function validateEmail($email, $allowExist = false)
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
        ) && !$allowExist) {
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

    public function getUserThankRecord($user_id, $topic_id = '')
    {
        if ($topic_id != '') {
            $comment_thank_record = $this->db->select('cash_flow',
                array(
                    '[><]comment' => array('deal_id' => 'id'),
                ),
                array(
                    'cash_flow.deal_id',
                ),
                array(
                    'AND' => array(
                        'cash_flow.source_id' => $user_id,
                        'cash_flow.type' => 'thank_comment_cost',
                        'comment.topic_id' => $topic_id,
                    ),
                )
            );
            $topic_thank_record = $this->db->select('cash_flow',
                array(
                    'deal_id',
                ),
                array(
                    'AND' => array(
                        'source_id' => $user_id,
                        'type' => 'thank_topic_cost',
                        'deal_id' => $topic_id,
                    ),
                )
            );
            foreach ($comment_thank_record as $key => $value) {
                $thank_record['comment'][] = $value['deal_id'];
            }
            $thank_record['topic'] = (isset($topic_thank_record[0]['deal_id'])) ? $topic_thank_record[0]['deal_id'] : '';
            return $thank_record;
        }
        $thank_record['comment'] = $this->db->select('cash_flow',
            array(
                'deal_id',
            ),
            array(
                'soucre_id' => $user_id,
                'type' => 'thank_comment_cost',
            )
        );
        $thank_record['topic'] = $this->db->has('cash_flow',
            array(
                'soucre_id' => $user_id,
                'type' => 'thank_topic_cost',
            )
        );
        return $thank_record;
    }

    public function validateWebsite($website)
    {
        $event = 'legal';
        $pattern = '/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/i';
        if (!preg_match($pattern, $website)) {
            $event = 'illegal';
        }
        if ($website == '') {
            $event = 'undefined';
        }
        return eventGenerate('website', $event);
    }

    public function validateCompany($company)
    {
        $event = 'legal';
        if (strlen($company) > 90) {
            $event = 'long';
        }
        if ($company == '') {
            $event = 'undefined';
        }
        return eventGenerate('company', $event);
    }

    public function validateJob($job)
    {
        $event = 'legal';
        if (strlen($job) > 30) {
            $event = 'long';
        }
        if ($job == '') {
            $event = 'undefined';
        }
        return eventGenerate('job', $event);
    }

    public function validateLocation($location)
    {
        $event = 'legal';
        if (strlen($location) > 60) {
            $event = 'long';
        }
        if ($location == '') {
            $event = 'undefined';
        }
        return eventGenerate('location', $event);
    }

    public function validateSignature($signature)
    {
        $event = 'legal';
        if (strlen($signature) > 60) {
            $event = 'long';
        }
        if ($signature == '') {
            $event = 'undefined';
        }
        return eventGenerate('signature', $event);
    }

    public function validateIntroduction($introduction)
    {
        $event = 'legal';
        if (strlen($introduction) > 60) {
            $event = 'long';
        }
        if ($introduction == '') {
            $event = 'undefined';
        }
        return eventGenerate('introduction', $event);
    }

    public function updateUserSetting($updateInfo, $uid)
    {
        return $this->db->update('user_setting',
            $updateInfo,
            array('user_id' => $uid)
        );
    }

    public function getUserSetting($user_id)
    {
        $user_setting = $this->db->select('user_setting',
            array(
                '[><]user' => array('user_id' => 'id'),
            ),
            array(
                'user_setting.email',
                'user_setting.website',
                'user_setting.company',
                'user_setting.job',
                'user_setting.location',
                'user_setting.signature',
                'user_setting.introduction',
                'user.position',
                'user.created_at',
            ),
            array(
                'user_setting.user_id' => $user_id,
            )
        );
        return $user_setting[0];
    }

    public function getUserSignature($uid)
    {
        $signature = $this->db->select('user_setting',
            array('signature'),
            array(
                'user_id' => $uid,
            )
        );
        return $signature[0]['signature'];
    }

    public function ifUseAvatar($user_id)
    {
        return $this->db->has('user_setting',
            array(
                'AND' => array(
                    'user_id' => $user_id,
                    'avatar' => 1,
                ),
            )
        );
    }
}
