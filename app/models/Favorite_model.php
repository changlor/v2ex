<?php
class Favorite_model extends Kotori_Model
{

    public function insertFavorNode($node_id, $user_id)
    {
        if (!$this->model->Node->validateNode('id', $node_id)) {
            return false;
        }
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'node',
            )
        );
        if ($this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $node_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        )) {
            return false;
        }
        $this->db->insert('favorite',
            array(
                'target_id' => $node_id,
                'source_id' => $user_id,
                'type' => $favorite_type_id[0]['id'],
            )
        );
        return $this->model->User->updateUserRecord(array('favorite_node_count[+]' => 1), $user_id);
    }

    public function getUserFavorNode($user_id)
    {
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'node',
            )
        );
        return $favorite_node = $this->db->select('favorite',
            array(
                '[><]node' => array('target_id' => 'id'),
            ),
            array(
                'node.ename',
                'node.name',
                'node.topic_count',
            ),
            array(
                'AND' => array(
                    'favorite.source_id' => $user_id,
                    'favorite.type' => $favorite_type_id[0]['id'],
                ),
            )
        );
    }

    public function getUserFavorMemberTopic($user_id)
    {
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'member',
            )
        );
        $favorite_member_topic = $this->db->select('favorite',
            array(
                '[><]topic' => array('target_id' => 'user_id'),
                '[><]node' => array('topic.node_id' => 'id'),
                '[><]user' => array('topic.user_id' => 'id'),
                '[><]user_setting' => array('topic.user_id' => 'user_id'),
            ),
            array(
                'node.ename',
                'node.name',
                'topic.id',
                'topic.user_id(author_id)',
                'user_setting.avatar(author_avatar)',
                'user.avatar(default_avatar)',
                'topic.title',
                'topic.created_at',
                'topic.comment_count',
                'topic.replied_at',
                'topic.reply_id',
            ),
            array(
                'AND' => array(
                    'favorite.source_id' => $user_id,
                    'favorite.type' => $favorite_type_id[0]['id'],
                ),
            )
        );
        $user_id = false;
        if (!empty($favorite_member_topic)) {
            foreach ($favorite_member_topic as $key => $value) {
                $user_id[] = $value['author_id'];
                $user_id[] = $value['reply_id'];
            }
            $user_id = array_flip(array_flip($user_id));
            $user_info = $this->model->User->getUserInfo($user_id);
            $user_id_to_name = false;
            foreach ($user_info as $key => $value) {
                $user_id_to_name[$value['id']] = $value['username'];
            }
            foreach ($favorite_member_topic as $key => $value) {
                $favorite_member_topic[$key]['author'] = $user_id_to_name[$value['author_id']];
                $favorite_member_topic[$key]['last_reply_username'] = (isset($user_id_to_name[$value['reply_id']])) ? $user_id_to_name[$value['reply_id']] : '';
            }
        }
        return $favorite_member_topic;
    }

    public function getUserFavorTopic($user_id)
    {
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'topic',
            )
        );
        $favorite_topic = $this->db->select('favorite',
            array(
                '[><]topic' => array('target_id' => 'id'),
                '[><]node' => array('topic.node_id' => 'id'),
                '[><]user_setting' => array('topic.user_id' => 'user_id'),
                '[><]user' => array('topic.user_id' => 'id'),
            ),
            array(
                'node.ename',
                'node.name',
                'topic.id',
                'topic.user_id(author_id)',
                'user_setting.avatar(author_avatar)',
                'user.avatar(default_avatar)',
                'topic.title',
                'topic.created_at',
                'topic.comment_count',
                'topic.replied_at',
                'topic.reply_id',
            ),
            array(
                'AND' => array(
                    'favorite.source_id' => $user_id,
                    'favorite.type' => $favorite_type_id[0]['id'],
                ),
            )
        );
        if (!empty($favorite_topic)) {
            $user_id = false;
            foreach ($favorite_topic as $key => $value) {
                $user_id[] = $value['author_id'];
                $user_id[] = $value['reply_id'];
            }
            $user_id = array_flip(array_flip($user_id));
            $user_info = $this->model->User->getUserInfo($user_id);
            $user_id_to_name = false;
            foreach ($user_info as $key => $value) {
                $user_id_to_name[$value['id']] = $value['username'];
            }
            foreach ($favorite_topic as $key => $value) {
                $favorite_topic[$key]['author'] = $user_id_to_name[$value['author_id']];
                $favorite_topic[$key]['last_reply_username'] = (isset($user_id_to_name[$value['reply_id']])) ? $user_id_to_name[$value['reply_id']] : '';
            }
        }
        return $favorite_topic;
    }

    public function deleteFavorNode($node_id, $user_id)
    {
        if (!$this->model->Node->validateNode('id', $node_id)) {
            return false;
        }
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'node',
            )
        );
        if (!$this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $node_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        )) {
            return false;
        }
        $this->db->delete('favorite',
            array(
                'AND' => array(
                    'target_id' => $node_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        );
        return $this->model->User->updateUserRecord(array('favorite_node_count[-]' => 1), $user_id);
    }

    public function isFavoriteMember($member_id, $user_id)
    {
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'member',
            )
        );
        return $this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $member_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        );
    }

    public function isFavoriteNode($node_id, $user_id)
    {
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'node',
            )
        );
        return $this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $node_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        );
    }

    public function isFavoriteTopic($topic_id, $user_id)
    {
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'topic',
            )
        );
        return $this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $topic_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        );
    }

    public function insertFavorTopic($topic_id, $user_id)
    {
        if (!$this->model->Topic->validateTopicInfo('id', $topic_id)) {
            return false;
        }
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'topic',
            )
        );
        if ($this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $topic_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        )) {
            return false;
        }
        $this->db->insert('favorite',
            array(
                'target_id' => $topic_id,
                'source_id' => $user_id,
                'type' => $favorite_type_id[0]['id'],
            )
        );
        return $this->model->User->updateUserRecord(array('favorite_topic_count[+]' => 1), $user_id);
    }

    public function deleteFavorTopic($topic_id, $user_id)
    {
        if (!$this->model->Topic->validateTopicInfo('id', $topic_id)) {
            return false;
        }
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'topic',
            )
        );
        if (!$this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $topic_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        )) {
            return false;
        }
        $this->db->delete('favorite',
            array(
                'AND' => array(
                    'target_id' => $topic_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        );
        return $this->model->User->updateUserRecord(array('favorite_topic_count[-]' => 1), $user_id);
    }

    public function insertFavorMember($member_id, $user_id)
    {
        if (!$this->model->User->validateUser('id', $member_id)) {
            return false;
        }
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'member',
            )
        );
        if ($this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $member_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        )) {
            return false;
        }
        $this->db->insert('favorite',
            array(
                'target_id' => $member_id,
                'source_id' => $user_id,
                'type' => $favorite_type_id[0]['id'],
            )
        );
        return $this->model->User->updateUserRecord(array('favorite_user_count[+]' => 1), $user_id);
    }

    public function deleteFavorMember($member_id, $user_id)
    {
        if (!$this->model->User->validateUser('id', $member_id)) {
            return false;
        }
        $favorite_type_id = $this->db->select('favorite_type',
            array(
                'id',
            ),
            array(
                'name' => 'member',
            )
        );
        if (!$this->db->has('favorite',
            array(
                'AND' => array(
                    'target_id' => $member_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        )) {
            return false;
        }
        $this->db->delete('favorite',
            array(
                'AND' => array(
                    'target_id' => $member_id,
                    'source_id' => $user_id,
                    'type' => $favorite_type_id[0]['id'],
                ),
            )
        );
        return $this->model->User->updateUserRecord(array('favorite_user_count[-]' => 1), $user_id);
    }
}
