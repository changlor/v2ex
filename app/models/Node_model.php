<?php
class Node_model extends Kotori_Model
{
    public function getTabNode($tabname)
    {
        return $this->db->select('tab',
            array(
                '[><]tab_node' => array('tab.id' => 'tab_id'),
                '[><]node' => array('tab_node.node_id' => 'id'),
            ),
            array(
                'node.name',
                'node.ename',
            ),
            array(
                'tab.ename' => $tabname,
            )
        );
    }
    public function getAllNode()
    {
        $node['chaos'] = $this->getTabNode('chaos');
        return $node;
    }
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
        return $node_id[0]['id'];
    }

    public function updateNodeInfo($updateInfo, $node_id)
    {
        return $this->db->update('node',
            $updateInfo,
            array('id' => $node_id)
        );
    }

    public function validateNode($field, $value)
    {
        return $this->db->has('node',
            array($field => $value)
        );
    }
}
