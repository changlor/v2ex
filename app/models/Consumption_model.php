<?php
class Consumption_model extends Kotori_Model
{
    public function topicCost($consumption, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'topic_cost';
        $cash_flow['source_id'] = $consumption['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $consumption['event_coin'];
        $cash_flow['created_at'] = $consumption['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }

    public function commentCost($consumption, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'comment_cost';
        $cash_flow['source_id'] = $consumption['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $consumption['event_coin'];
        $cash_flow['created_at'] = $consumption['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }

    public function thankCommentCost($consumption, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'thank_comment_cost';
        $cash_flow['source_id'] = $consumption['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $consumption['event_coin'];
        $cash_flow['created_at'] = $consumption['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }

    public function thankTopicCost($consumption, $deal)
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
        //py交易记录
        $cash_flow['type'] = 'thank_topic_cost';
        $cash_flow['source_id'] = $consumption['user_id'];
        $cash_flow['target_id'] = $deal['user_id'];
        $cash_flow['deal_id'] = $deal['deal_id'];
        $cash_flow['coin'] = $consumption['event_coin'];
        $cash_flow['created_at'] = $consumption['created_at'];
        $this->db->insert('cash_flow', $cash_flow);
    }

    public function ifThankComment($comment_id, $thank_user_id, $comment_user_id)
    {
        return $this->db->has('cash_flow',
            array(
                'AND' => array(
                    'deal_id' => $comment_id,
                    'target_id' => $comment_user_id,
                    'source_id' => $thank_user_id,
                    'type' => 'thank_comment_cost',
                ),
            )
        );
    }

    public function ifThankTopic($comment_id, $thank_user_id, $author_id)
    {
        return $this->db->has('cash_flow',
            array(
                'AND' => array(
                    'deal_id' => $comment_id,
                    'target_id' => $author_id,
                    'source_id' => $thank_user_id,
                    'type' => 'thank_topic_cost',
                ),
            )
        );
    }
}
