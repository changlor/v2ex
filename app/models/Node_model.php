<?php
class Node_model extends Kotori_Model
{
    public function getNodeInfo($field, $value)
    {
        $node_info = $this->db->select('node',
            array(
                'id',
                'topic_count',
                'ename',
                'name',
                'about',
            ),
            array(
                $field => $value,
            )
        );
        return $node_info[0];
    }

    public function getNodeId($field, $value)
    {
        if (!$this->db->has('node',
            array($field => $value)
        )) {
            $field = 'ename';
            $value = 'mass';
        }
        $node_id = $this->db->select('node',
            array('id'),
            array($field => $value)
        );
        return $node_id['id'];
    }

    public function validateNode($field, $value)
    {
        return $this->db->has('node',
            array($field => $value)
        );
    }
}
