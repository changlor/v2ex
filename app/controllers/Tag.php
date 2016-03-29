<?php
class Tag extends Base
{
    public function viewTagTopic($tagname)
    {
        $tag_info = $this->model->Tag->getTagInfo($tagname);
        $this->rightBarInfo['rightBar'] = array('myInfo');
        if ($tag_info) {
            $topic = $this->model->Topic->getTagTopic($tag_info['id']);
            $topic_keys = array_keys($topic);
            $topic_first_key = reset($topic_keys);
            $this->view->assign('topic', $topic)->assign('tagname', $tagname)->assign('tag_topic_count', $tag_info['topic_count'])->assign('topic_first_key', $topic_first_key)->assign('rightBarInfo', $this->rightBarInfo)->display();
        } else {
            $this->view->assign('rightBarInfo', $this->rightBarInfo)->display('Tag/notFoundTagTopic');
        }

    }
}
