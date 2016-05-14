<?php
class Consumption_model extends Kotori_Model
{
    public function topicCost($consumption)
    {
        $consumption['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $consumption['event_id'] = 1;
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
}
