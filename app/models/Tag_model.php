<?php
class Tag_model extends Kotori_Model
{
    public function getTopicTag($topic_id)
    {
        return $this->db->select('tag_topic',
            array(
                '[><]tag' => array('tag_id' => 'id'),
            ),
            array(
                'tag.name',
                'tag.id',
            ),
            array(
                'topic_id' => $topic_id,
            )
        );
    }

    public function insertTag($tags, $topic_id)
    {
        foreach ($tags as $key => $value) {
            $tag_id = $this->db->select('tag',
                array('id'),
                array('name' => $value)
            );
            if (isset($tag_id[0]['id'])) {
                $this->db->update('tag',
                    array(
                        'topic_count[+]' => 1,
                    ),
                    array('name' => $value)
                );
            }
            if (!isset($tag_id[0]['id'])) {
                $this->db->insert('tag',
                    array(
                        'name' => $value,
                        'topic_count' => 1,
                    )
                );
                $tag_id = $this->db->select('tag',
                    array('id'),
                    array('name' => $value)
                );
            }
            $this->db->insert('tag_topic',
                array(
                    'topic_id' => $topic_id,
                    'tag_id' => $tag_id[0]['id'],
                )
            );
        }
    }
}
