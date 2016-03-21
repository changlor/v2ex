<?php
// +----------------------------------------------------------------------
// | Kotori_frame [ WE ALL FAN IN KOTORI ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author：Changle <958142428@qq.com>
// +----------------------------------------------------------------------
class Page
{
    public $recordCount; //总数据数
    public $pagination; // 每页显示数据数
    public $totalPage; // 总分页页面数，计算后总共分页数目
    public $totalRows; //总共显示行数，即为分页连接最多显示页数，多出部分省略号代替
    public $param = 'p'; //分页跳转的参数，默认为p
    public $currentPage; // 当前页码

    /**
     * 架构函数
     * @param array $recordCount  总数据数
     * @param array $pagination  每页显示记录数
     */
    public function __construct($recordCount, $pagination = 9)
    {
        $this->recordCount = $recordCount;
        $this->totalPage = ceil($recordCount / $pagination);
        /* 基础设置，可以在配置中修改 */
        $this->pagination = 9;
        $this->param = 'p'; //设置分页参数名称
        $this->totalRows = 9; //在一个分页连接中，只显示到第九页，多出部分省略号代替
        $this->currentPage = $this->totalPage; //当无指定时，默认显示最后一页
    }

    /**
     * 设置当前页码
     * @param $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * 暂不支持其他分页链接参数的自定义修改
     */

    /**
     * 生成页码链接Link
     * @return string
     */
    private function page_link($page)
    {
        $page_link = false;
        for ($i = 0; $i < $this->totalPage; $i++) {
            $page_link .= '<a href="?p=' . ($i + 1) . '" class="';
            $page_link .= ($i + 1) == $this->currentPage ? 'page_current' : 'page_normal';
            $page_link .= '">' . ($i + 1) . '</a>';
            $page_link .= '&nbsp;';
        }
        $page_link .= '<input type="number" class="page_input" autocomplete="off" value="';
        $page_link .= $this->currentPage;
        $page_link .= '" min="1" max="';
        $page_link .= $this->totalPage;
        $page_link .= '" onkeydown="if (event.keyCode == 13) location.href = \'?p=\' + this.value">';
        return str_replace('%PAGE_LINK%', $page_link, $page);
    }

    /**
     * 生成翻页链接css
     * @return string
     */
    private function page_style($page)
    {
        $page_style = '<style type="text/css">.disable_now {color: #ccc !important;background-color: #fff !important;}.hover_now {cursor: pointer;color: #333 !important;background-color: #f9f9f9 !important;text-shadow: 0px 1px 0px #fff !important;}.active_now {background-color: #e2e2e2 !important;}</style>';
        return str_replace('%PAGE_STYLE%', $page_style, $page);
    }

    /**
     * 生成翻页连接Link
     * @return string
     */
    private function turn_page_link($page)
    {
        $page_turn_left = '<td width="50%" align="center" class="super normal button %IF_ENABLE%" style="border-right: none; border-top-right-radius: 0px; border-bottom-right-radius: 0px;" %PAGE_TURN_JS%>❮</td>';
        $page_turn_right = '<td width="50%" align="center" class="super normal_page_right button %IF_ENABLE%" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px;" %PAGE_TURN_JS%>❯</td>';
        if ($this->currentPage == $this->totalPage) {
            $page_turn_right = str_replace('%IF_ENABLE%', 'disable_now', $page_turn_right);
            $page_turn_left = str_replace('%IF_ENABLE%', '', $page_turn_left);
        }
        if ($this->currentPage == 1) {
            $page_turn_left = str_replace('%IF_ENABLE%', 'disable_now', $page_turn_left);
            $page_turn_right = str_replace('%IF_ENABLE%', '', $page_turn_right);
        }
        $page_turn_left = $this->page_js($page_turn_left, 'left');
        $page_turn_right = $this->page_js($page_turn_right, 'right');
        $page = str_replace('%PAGE_TURN_LEFT%', $page_turn_left, $page);
        $page = str_replace('%PAGE_TURN_RIGHT%', $page_turn_right, $page);
        return $page;
    }

    /**
     * 生成翻页js
     * @param js
     * @return string
     */
    private function page_js($page_turn_forward, $forward)
    {
        $page_turn_js = 'onclick="location.href=\'?p=';
        if ($forward == 'left') {
            $page_turn_js .= ($this->currentPage > 1) ? $this->currentPage - 1 : 1;
        }
        if ($forward == 'right') {
            $page_turn_js .= ($this->currentPage < $this->totalPage) ? $this->currentPage + 1 : $this->totalPage;
        }
        $page_turn_js .= '\';" onmouseover="$(this).addClass(\'hover_now\');" onmousedown="$(this).addClass(\'active_now\');" onmouseleave="$(this).removeClass(\'hover_now\'); $(this).removeClass(\'active_now\');" %FORWARD%';
        $page_turn_js = ($forward == 'left') ? str_replace('%FORWARD%', 'title="上一页"', $page_turn_js) : str_replace('%FORWARD%', 'title="下一页"', $page_turn_js);
        if ($this->currentPage == 1 && $forward == 'left') {
            return str_replace('%PAGE_TURN_JS%', '', $page_turn_forward);
        }
        if ($this->currentPage == $this->totalPage && $forward == 'right') {
            return str_replace('%PAGE_TURN_JS%', '', $page_turn_forward);
        }
        return str_replace('%PAGE_TURN_JS%', $page_turn_js, $page_turn_forward);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show($currentPage = '')
    {
        $this->currentPage = empty($currentPage) ? $this->totalPage : $currentPage;
        $page = '<div class="cell">';
        $page .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
        $page .= '<tbody>';
        $page .= '<tr>';
        $page .= '<td width="92%" align="left">';
        $page .= '%PAGE_LINK%';
        $page .= '</td>';
        $page .= '<td width="8%" align="right">';
        $page .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
        $page .= '%PAGE_STYLE%';
        $page .= '<tbody>';
        $page .= '<tr>';
        $page .= '%PAGE_TURN_LEFT%';
        $page .= '%PAGE_TURN_RIGHT%';
        $page .= '</tr>';
        $page .= '</tbody>';
        $page .= '</table>';
        $page .= '</td>';
        $page .= '</tr>';
        $page .= '</tbody>';
        $page .= '</table>';
        $page .= '</div>';
        $page = $this->page_link($page);
        $page = $this->page_style($page);
        $page = $this->turn_page_link($page);
        return $page;
    }
}
