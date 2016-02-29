<?php
class EventHandle extends Base
{
    public function checkTitle()
    {
        $event = 'legal';
        $title = $this->topic['input']['title'];
        //标题太长
        if (strlen($title) > 120) {
            $event = 'long';
        }
        //标题存在
        $id = $this->model->Topic->checkExist('title', $title);
        if (isset($id[0]['id'])) {
            $event = 'exist';
        }
        //未输入标题
        if ($title == '') {
            $event = 'undefined';
        }
        $this->_eventGenerate('title', $event, $title);
    }

    public function checkContent()
    {
        $event = 'legal';
        $content = $this->topic['input']['content'];
        //内容太长
        if (strlen($content) > 2000) {
            $event = 'long';
        }
        $this->_eventGenerate('content', $event, $content);
    }

    public function eventGenerate($level, $event, $value = '')
    {
        $this->topic['output'][$level]['event'] = $event;
        $this->topic['output'][$level]['msg'] = custom_error($level, $event, $value);
    }

    public function gtParse($str)
    {
        $md = str_replace(chr(10), '@###@', $str);
        $md .= '@###@';
        if ($md != $str) {
            $pattern = '/(?!=@###@)(.*?)(?<=@###@)/i';
            preg_match_all($pattern, $md, $matches);
            $md = '';
            foreach ($matches[1] as $key => $value) {
                if ($value != '') {
                    $pos = trim($value);
                    if (strpos($pos, '&gt;') === 0) {
                        $temp = '@begin@ ' . $value;
                        $pattern = '/(@begin@[ ]+)((&gt;)+)/i';
                        preg_match_all($pattern, $temp, $matches);
                        $replace['number'] = strlen($matches[2][0]) / 4;
                        $replace['quote'] = '';
                        for ($i = 0; $i < 3 && $i < $replace['number']; $i++) {
                            $replace['quote'] .= '>';
                        }
                        $replace['header'] = str_replace('@begin@ ', '', $matches[1][0]);
                        $temp = str_replace($matches[0][0], $replace['header'] . $replace['quote'], $temp);
                        //echo '<pre>';print_r($matches);echo '</pre>';
                        //echo '<pre>' . $temp . '</pre>';
                        //'&gt;&gt;&gt;xx&gt;&gt;&gt;'
                        $md .= str_replace('&gt;', '>', $temp);
                    } else {
                        $md .= $value;
                    }
                }
            }
            $md = str_replace('@###@', chr(10), $md);
            return $md;
        }
        return $str;
    }

    public function hrefParse($str)
    {
        $md = $str;
        $str = str_replace('[', ' [', $str);
        $pattern = '/(\[(.*?)\](\((.*?)( \'(.*?)\')?\)))+/i';
        preg_match_all($pattern, $str, $matches);
        $pattern = '/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|love|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/i';
        foreach ($matches[4] as $key => $value) {
            if (!preg_match($pattern, $value)) {
                $md = str_replace($matches[3][$key], '(javascript:; \'此链接并不是一个有效的链接，因而被替换成无害链接\')', $md);
            }
        }
        return $md;
    }

    public function checkUserId()
    {
        $event = 'legal';
        //用户不存在
        $id = $this->model->User->checkExist('id', $this->uid);
        if ($id[0]['id'] == '') {
            $event = 'undefined';
        }
        $this->_eventGenerate('user', $event, $this->uid);
    }

    public function checkTopicId()
    {
        $event = 'legal';
        //主题未找到
        $id = $this->model->Topic->checkExist('id', $this->topic['input']['topic_id']);
        if ($id[0]['id'] == '') {
            $event = 'undefined';
        }
        $this->_eventGenerate('topic', $event, $this->uid);
    }

    public function preventReComment($pos = 0)
    {
        $event = 'legal';
        $topic_id = $this->topic['input']['topic_id'];
        $user_id = $this->topic['input']['user_id'];
        $content = $this->topic['input']['content'];
        $uid = $this->model->User->checkExist('id', $user_id);
        $tid = $this->model->Topic->checkExist('id', $topic_id);
        if ($uid[0]['id'] != '' && $tid[0]['id'] != '') {
            $last_id = $this->model->Topic->checkContent('comment', $content);
            if ($last_id != '') {
                $event = 'illegal';
            }
        }
        //允许重复提交
        if ($pos == 1) {
            $event = 'legal';
        }
        $this->_eventGenerate('recomment', $event, $content);
    }
}