<?php
class Favorite extends Base
{
    public function favorNode()
    {
        $favorite_node = $this->model->Favorite->getUserFavorNode($this->uid);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('favorite_node', $favorite_node)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function favorTopic()
    {
        $favorite_topic = $this->model->Favorite->getUserFavorTopic($this->uid);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('favorite_topic', $favorite_topic)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function favorMember()
    {
        $favorite_member_topic = $this->model->Favorite->getUserFavorMemberTopic($this->uid);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        $this->view->assign('favorite_member_topic', $favorite_member_topic)->assign('rightBarInfo', $this->rightBarInfo)->display();
    }

    public function insertFavorNode($node_id)
    {
        $this->model->Favorite->insertFavorNode($node_id, $this->uid);
        $referer = $_SERVER['HTTP_REFERER'];
        $url = $referer;
        $this->response->redirect($url, true);
    }

    public function deleteFavorNode($node_id)
    {
        $this->model->Favorite->deleteFavorNode($node_id, $this->uid);
        $referer = $_SERVER['HTTP_REFERER'];
        $url = $referer;
        $this->response->redirect($url, true);
    }

    public function insertFavorTopic($topic_id)
    {
        $this->model->Favorite->insertFavorTopic($topic_id, $this->uid);
        $referer = $_SERVER['HTTP_REFERER'];
        $url = $referer;
        $this->response->redirect($url, true);
    }

    public function deleteFavorTopic($topic_id)
    {
        $this->model->Favorite->deleteFavorTopic($topic_id, $this->uid);
        $referer = $_SERVER['HTTP_REFERER'];
        $url = $referer;
        $this->response->redirect($url, true);
    }

    public function insertFavorMember($username)
    {
        $user_id = $this->model->User->getUserId('username', $username);
        $this->model->Favorite->insertFavorMember($user_id, $this->uid);
        $referer = $_SERVER['HTTP_REFERER'];
        $url = $referer;
        $this->response->redirect($url, true);
    }

    public function deleteFavorMember($username)
    {
        $user_id = $this->model->User->getUserId('username', $username);
        $this->model->Favorite->deleteFavorMember($user_id, $this->uid);
        $referer = $_SERVER['HTTP_REFERER'];
        $url = $referer;
        $this->response->redirect($url, true);
    }
}
