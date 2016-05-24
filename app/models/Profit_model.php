<?php
class Profit_model extends Kotori_Model
{
    public function getCommentProfit($profit)
    {
        $profit['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $profit['event_id'] = 1;
        $profit['type'] = '主题回复收益';
        $profit['event_type'] = 'profit';
        $this->db->insert('user_asset',
            $profit
        );
        $this->db->update('user_record',
            array(
                'coin' => $profit['coin'],
            ),
            array(
                'user_id' => $profit['user_id'],
            )
        );
    }

    public function getThankReplyProfit($profit)
    {
        $profit['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $profit['event_id'] = 2;
        $profit['type'] = '收到谢意';
        $profit['event_type'] = 'profit';
        $this->db->insert('user_asset',
            $profit
        );
        $this->db->update('user_record',
            array(
                'coin' => $profit['coin'],
            ),
            array(
                'user_id' => $profit['user_id'],
            )
        );
    }

    public function getThankTopicProfit($profit)
    {
        $profit['created_at'] = strtotime(date('Y-m-d H:i:s'));
        $profit['event_id'] = 3;
        $profit['type'] = '收到谢意';
        $profit['event_type'] = 'profit';
        $this->db->insert('user_asset',
            $profit
        );
        $this->db->update('user_record',
            array(
                'coin' => $profit['coin'],
            ),
            array(
                'user_id' => $profit['user_id'],
            )
        );
    }
}
