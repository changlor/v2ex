<?php
class Error_model extends Kotori_Model
{
    public function signin_error($handler)
    {
        $problem = '<div class="problem">';
        $problem .= '请解决以下问题然后再提交：';
        $problem .= '<ul>';
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $problem .= '<li>' . $value['msg'] . '</li>';
            }
        }
        $problem .= '</ul>';
        $problem .= '</div>';
        return $problem;
    }

    public function signup_error($handler)
    {
        return $this->signin_error($handler);
    }

    public function addTopic_error($handler, $title = '')
    {
        $problem = '<div class="problem">';
        $problem .= '保存新主题过程中遇到一些问题：';
        $problem .= '<ul>';
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $problem .= '<li>' . $value['msg'] . '</li>';
            }
        }
        $problem .= '</ul>';
        if ($title != '') {
            $callback['exist'] = $this->db->select('topic',
                array(
                    '[><]user' => array('user_id' => 'id'),
                ),
                array(
                    'topic.title',
                    'user.username(author)',
                    'topic.created_at',
                ),
                array(
                    'title' => $title,
                )
            );
        }
        if ($handler['title']['event'] == 'exist') {
            $problem .= '<div class="sep20"></div>';
            $problem .= '以下文章标题重复，你可以点击标题在新窗口中查看其内容：';
            $problem .= '<ul>';
            foreach ($callback['exist'] as $key => $value) {
                $problem .= '<li>';
                $problem .= '<a href="javascript:;" target="_blank"> ' . $value['title'] . ' </a>';
                $problem .= 'by';
                $problem .= '<a href="javascript:;" target="_blank"> ' . $value['author'] . ' </a>';
                $problem .= '-';
                $problem .= '<span class="fade"> ' . fadeTime($value['created_at']) . ' </span>';
                $problem .= '</li>';
            }
            $problem .= '</ul>';
        }
        $problem .= '</div>';
        return $problem;
    }

    public function addComment_error($handler)
    {
        $problem = '<div class="problem">创建新回复过程中遇到一些问题：';
        $problem .= '<ul>';
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $problem .= '<li>' . $value['msg'] . '</li>';
            }
        }
        $problem .= '</ul>';
        $problem .= '</div>';
        return $problem;
    }

    public function userSetting_error($handler)
    {
        $problem = '<div class="problem">保存设置的过程中遇到一些问题：';
        $problem .= '<ul>';
        foreach ($handler as $key => $value) {
            if ($value['msg'] != 'pass') {
                $problem .= '<li>' . $value['msg'] . '</li>';
            }
        }
        $problem .= '</ul>';
        $problem .= '</div>';
        return $problem;
    }
}
