<?php
class User_model extends Kotori_Model
{
    public function getUserFavor($user_id)
    {
        $favor = $this->db->select('favorite',
            array(
                'type',
                'source_id',
                'target_id',
            ),
            array(
                'source_id' => $user_id,
            )
        );
        $favor_id['node']['id'] = '';
        $favor_id['topic']['id'] = '';
        $favor_id['user']['id'] = '';
        foreach ($favor as $key => $value) {
            switch ($value['type']) {
                case 0:
                    $favor_id['node']['id'] .= $value['target_id'] . ',';
                    break;
                case 1:
                    $favor_id['topic']['id'] .= $value['target_id'] . ',';
                    break;
                case 2:
                    $favor_id['user']['id'] .= $value['target_id'] . ',';
                    break;
            }
        }
        $favor = '';
        foreach ($favor_id as $key => $value) {
            switch ($key) {
                case 'topic':
                    $value['id'] = rtrim($value['id'], ',');
                    $query = 'SELECT title,id FROM topic WHERE id in (' . $value['id'] . ')';
                    $favor['topic'] = $this->db->query($query)->fetchAll();
                    foreach ($favor['topic'] as $ke => $val) {
                        foreach ($val as $k => $v) {
                            if (!is_numeric($k)) {
                                $favor['topic'][$k][] = $v;
                            }
                        }
                        unset($favor['topic'][$ke]);
                    }
                    break;
            }
        }
        return $favor;
    }

    public function getUserInfo($user_id)
    {
        $user_info = $this->db->select('user_info',
            array(
                'favorite_node_count',
                'favorite_topic_count',
                'favorite_user_count',
            ),
            array(
                'user_id' => $user_id,
            )
        );
        return $user_info[0];
    }

    public function signin($user)
    {
        return $this->db->insert('user',
            $user
        );
    }

    public function checkExist($level, $value)
    {
        return $this->db->select('user',
            array('id'),
            array($level => $value)
        );
    }

    public function checkPass($username)
    {
        return $this->db->select('user',
            array('password_hash', 'auth_key'),
            array('username' => $username)
        );
    }

    public function updateUserInfo($newInfo, $uid)
    {
        return $this->db->update('user_info',
            $newInfo,
            array('user_id' => $uid)
        );
    }
}
