<?php
class Profit extends Base
{
    public function thankReply($username)
    {
        if ($this->request->isAjax()) {
            $user_id = $this->model->User->getUserId('username', $username);
            $thank_user_name = rcookie('NA');
            $topic_id = $this->request->input('get.t');
            $coin = $this->model->User->getUserCoin($user_id);
            $topic_info = $this->model->Topic->getTopicInfo($topic_id, 'simplified');
            $profit['user_id'] = $user_id;
            $profit['event_coin'] = 10;
            $profit['coin'] = $coin + $profit['event_coin'];
            $profit['about'] = '%username' . $thank_user_name . '感谢你在 %title' . $topic_info['title'] . '% 的回复';
            $this->model->Profit->getThankReplyProfit($profit);
            $consunmption['event_coin'] = -10;
            $consunmption['coin'] = $this->model->User->getUserCoin($this->uid) + $consunmption['event_coin'];
            $consunmption['user_id'] = $this->uid;
            $consunmption['about'] = '感谢' . $username . '的回复' . ' › ' . '%title' . $topic_info['title'] . '%';
            $this->model->Consumption->thankCommentCost($consunmption);
        }
    }   

    public function thankTopic($username)
    {
        if ($this->request->isAjax()) {
            $user_id = $this->model->User->getUserId('username', $username);
            $username = rcookie('NA');
            $topic_id = $this->request->input('get.t');
            $coin = $this->model->User->getUserCoin($user_id);
            $topic_info = $this->model->Topic->getTopicInfo($topic_id, 'simplified');
            $profit['user_id'] = $user_id;
            $profit['event_coin'] = 10;
            $profit['coin'] = $coin + $profit['event_coin'];
            $profit['about'] = '%username' . $username . '%感谢你发布的 ' . $topic_info['title'] . ' 主题';
            $this->model->Profit->getThankTopicProfit($profit);
            $consunmption['event_coin'] = -10;
            $consunmption['coin'] = $this->model->User->getUserCoin($this->uid) + $consunmption['event_coin'];
            $consunmption['user_id'] = $this->uid;
            $consunmption['about'] = '感谢' . $username . '的主题' . ' › ' . '%title' . $topic_info['title'] . '%';
            $this->model->Consumption->thankTopicCost($consunmption);
        }
    }
}
