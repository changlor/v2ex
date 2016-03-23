<?php
class Node_model extends Kotori_Model
{
    public function getNodeInfo($field, $value)
    {
        $node_info = $this->db->select('node',
            array('id', 'topic_count'),
            array(
                $field => $value,
            )
        );
        return $node_info[0];
    }
}
