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

    public function addTopic_error($handler)
    {
        return $this->signin_error($handler);
    }
}
