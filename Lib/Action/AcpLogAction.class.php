<?php
/**
 * acp后台商品类
 */
class AcpLogAction extends AcpAction
{

    /**
     * 初始化
     * @author yzp
     * @return void
     * @todo 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        // 引入商品公共函数库
        require_cache('Common/func_item.php');
    }

    protected function get_search_condition()
    {
        $where = "";

        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        if ($start_time) {
            $where .= ' AND addtime >= ' . $start_time;
            $this->assign('start_time', $start_time);
        }

        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time) {
            $where .= ' AND addtime <= ' . $end_time;
            $this->assign('end_time', $end_time);
        }

        return $where;
    }


    //管理员操作日志列表
    //@author yzp
    public function get_admin_log_list()
    {
        $admin_log_obj = new AdminLogModel();

        $where = '1'.$this->get_search_condition();
        import('ORG.Util.Pagelist');
        $count = $admin_log_obj->getAdminLogNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $admin_log_obj->setStart($Page->firstRow);
        $admin_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $admin_log_list = $admin_log_obj->getAdminLogList('', $where, ' addtime DESC');

        $this->assign('admin_log_list', $admin_log_list ? : array());

        $this->assign('head_title', '操作日志列表');
        $this->display();
    }


}
