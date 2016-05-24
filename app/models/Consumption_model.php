<?php
class Consumption_model extends Kotori_Model
{
    public function topicCost($consumption)
    {
        $consumption['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $consumption['event_id'] = 1;
        $consumption['type'] = '创建主题';
        $consumption['event_type'] = 'consumption';
        $this->db->insert('user_asset',
            $consumption
        );
        $this->db->update('user_record',
            array(
                'coin' => $consumption['coin'],
            ),
            array(
                'user_id' => $consumption['user_id'],
            )
        );
    }

    public function commentCost($consumption)
    {
        $consumption['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $consumption['event_id'] = 2;
        $consumption['type'] = '创建回复';
        $consumption['event_type'] = 'consumption';
        $this->db->insert('user_asset',
            $consumption
        );
        $this->db->update('user_record',
            array(
                'coin' => $consumption['coin'],
            ),
            array(
                'user_id' => $consumption['user_id'],
            )
        );
    }

    public function thankCommentCost($consumption)
    {
        $consumption['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $consumption['event_id'] = 3;
        $consumption['type'] = '发送谢意';
        $consumption['event_type'] = 'consumption';
        $this->db->insert('user_asset',
            $consumption
        );
        $this->db->update('user_record',
            array(
                'coin' => $consumption['coin'],
            ),
            array(
                'user_id' => $consumption['user_id'],
            )
        );
    }

    public function thankTopicCost($consumption)
    {
        $consumption['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $consumption['event_id'] = 4;
        $consumption['type'] = '发送谢意';
        $consumption['event_type'] = 'consumption';
        $this->db->insert('user_asset',
            $consumption
        );
        $this->db->update('user_record',
            array(
                'coin' => $consumption['coin'],
            ),
            array(
                'user_id' => $consumption['user_id'],
            )
        );
    }
}
