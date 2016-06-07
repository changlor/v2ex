<?php
class Profit_model extends Kotori_Model
{
    public function getCommentProfit($profit, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'comment_profit';
        $cash_flow['source_id'] = $profit['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $profit['event_coin'];
        $cash_flow['created_at'] = $profit['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }

    public function getThankReplyProfit($profit, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'thank_comment_profit';
        $cash_flow['source_id'] = $profit['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $profit['event_coin'];
        $cash_flow['created_at'] = $profit['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }

    public function getThankTopicProfit($profit, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'thank_topic_profit';
        $cash_flow['source_id'] = $profit['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $profit['event_coin'];
        $cash_flow['created_at'] = $profit['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }
}
