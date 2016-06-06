<?php
class Profit extends Base
{
    public function thankReply($comment_id)
    {
        $topic_id = $this->request->input('get.t');
        $thank_user_name = rcookie('NA');
        $thank_user_coin = $this->model->User->getUserCoin($this->uid);
        $comment_info = $this->model->Comment->getCommentInfo($comment_id, 'user_info');
        $comment_user_id = $comment_info['user_id'];
        $comment_user_name = $comment_info['username'];
        if (!$this->model->User->validateUser('username', $thank_user_name)) {
            $handler['status'] = 'error';
            $handler['event'] = 'illegal';
            $handler['describe'] = '请登录后再发表感谢';
            $this->response->throwJson($handler);
        }
        if (!$this->model->Comment->validateExistComment($topic_id, $comment_id)) {
            $handler['status'] = 'error';
            $handler['event'] = 'notfound';
            $handler['describe'] = '未找到你要感谢的回复';
            $this->response->throwJson($handler);
        }
        if ($thank_user_name == $comment_user_name) {
            $handler['status'] = 'error';
            $handler['event'] = 'illegal';
            $handler['describe'] = '你无法感谢自己的回复';
            $this->response->throwJson($handler);
        }
        if ($thank_user_coin < 10) {
            $handler['status'] = 'error';
            $handler['event'] = 'lack';
            $handler['describe'] = '你没钱辣，想要快速来钱吗，赶紧+哥哥扣扣958142428来一发py交易，马上就能获取大量金币';
            $this->response->throwJson($handler);
        }
        if ($this->request->isAjax()) {
            $coin = $this->model->User->getUserCoin($comment_user_id);
            $topic_info = $this->model->Topic->getTopicInfo($topic_id, 'simplified');
            $profit['user_id'] = $comment_user_id;
            $profit['event_coin'] = 10;
            $profit['coin'] = $coin + $profit['event_coin'];
            $profit['about'] = '%username' . $thank_user_name . '感谢你在 %title' . $topic_info['title'] . '% 的回复';
            $this->model->Profit->getThankReplyProfit($profit);
            $consunmption['event_coin'] = -10;
            $consunmption['coin'] = $thank_user_coin + $consunmption['event_coin'];
            $consunmption['user_id'] = $this->uid;
            $consunmption['about'] = '感谢' . $comment_user_name . '的回复' . ' › ' . '%title' . $topic_info['title'] . '%';
            $this->model->Consumption->thankCommentCost($consunmption);
            $handler['status'] = 'success';
            $handler['event'] = 'pass';
            $handler['describe'] = '感谢已发送';
            $this->response->throwJson($handler);
        }
        $handler['status'] = 'error';
        $handler['event'] = 'illegal';
        $handler['describe'] = '非法请求！';
        $this->response->throwJson($handler);
    }

    public function thankTopic($author)
    {
        $thank_user_name = rcookie('NA');
        $thank_user_coin = $this->model->User->getUserCoin($this->uid);
        $topic_id = $this->request->input('get.t');
        $author_id = $this->model->User->getUserId('username', $author);
        if (!$this->model->User->validateUser('username', $thank_user_name)) {
            $handler['status'] = 'error';
            $handler['event'] = 'illegal';
            $handler['describe'] = '请登录后再发表感谢';
            $this->response->throwJson($handler);
        }
        if (!$this->model->Topic->validateTopic('user_id', $author_id, $topic_id)) {
            $handler['status'] = 'error';
            $handler['event'] = 'notfound';
            $handler['describe'] = '未找到你要感谢的主题';
            $this->response->throwJson($handler);
        }
        if ($thank_user_name == $author) {
            $handler['status'] = 'error';
            $handler['event'] = 'illegal';
            $handler['describe'] = '你无法感谢自己的主题';
            $this->response->throwJson($handler);
        }
        if ($thank_user_coin < 10) {
            $handler['status'] = 'error';
            $handler['event'] = 'lack';
            $handler['describe'] = '你没钱辣，想要快速来钱吗，赶紧+哥哥扣扣958142428来一发py交易，马上就能获取大量金币';
            $this->response->throwJson($handler);
        }
        if ($this->request->isAjax()) {
            $coin = $this->model->User->getUserCoin($author_id);
            $topic_info = $this->model->Topic->getTopicInfo($topic_id, 'simplified');
            $profit['user_id'] = $author_id;
            $profit['event_coin'] = 10;
            $profit['coin'] = $coin + $profit['event_coin'];
            $profit['about'] = '%username' . $thank_user_name . '%感谢你发布的 ' . $topic_info['title'] . ' 主题';
            $this->model->Profit->getThankTopicProfit($profit);
            $consunmption['event_coin'] = -10;
            $consunmption['coin'] = $this->model->User->getUserCoin($this->uid) + $consunmption['event_coin'];
            $consunmption['user_id'] = $this->uid;
            $consunmption['about'] = '感谢' . $author . '的主题' . ' › ' . '%title' . $topic_info['title'] . '%';
            $this->model->Consumption->thankTopicCost($consunmption);
            $handler['status'] = 'success';
            $handler['event'] = 'pass';
            $handler['describe'] = '感谢已发送';
            $this->response->throwJson($handler);
        }
    }
}
