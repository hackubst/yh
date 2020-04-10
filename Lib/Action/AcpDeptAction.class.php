<?php
/**
 * 系统设置
 */
class AcpDeptAction extends AcpAction {
	/**
     * 构造函数
     * @return void
     * @todo
     */
	public function AcpDept()
	{
		parent::_initialize();
	}

	/**
     * 友情链接列表
     * @author 姜伟
     * @return void
     * @todo 从link表取数据
     */
	public function dept_list()
	{
        $obj = D('Dept');

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $obj->count();
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $obj->setStart($Page->firstRow);
        $obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('pagination', $show);

        $list = $obj->where()->limit()->select();
        $this->assign('dept_list', $list);
		$this->assign('head_title', '门店列表');
		$this->display();
	}

	/**
     * 添加友情链接
     * @author 姜伟
     * @return void
     * @todo 把数据写进tp_link表
     */
	public function sync_data()
	{
        D('Dept')->syncData();
        exit('success');
	}

	/**
     * 添加友情链接
     * @author 姜伟
     * @return void
     * @todo 把数据写进tp_link表
     */
	public function set_enable()
	{
        $id = I('get.id', 0, 'int');
        $status = I('get.st', 0, 'int');
        if ($id) {
            D('Dept')->where('dept_id=%d', intval($id))->setField('isuse', intval($status));
            exit('success');
        } else {
            exit('failure');
        }
	}

    /**
     * 修改门店排序
     * @author wzg
     * @todo 修改表中的serial
     * @return JSON 数据
     */
    public function edit_serial()
    {
        $id = I('id', 0, 'int');
        $serial = I('serial', 0, 'int');

        if ($this->isAjax() && $id) {
            //验证ID是否为数字
            if(!$id)
            {
                $this->_ajaxFeedback(0, null, '参数无效！');
            }
            if(!$serial)
            {
                $this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
            } 

            $dept_obj = new DeptModel();
            if(false !== $dept_obj->where('dept_id = ' . $id)->setField('serial', $serial))
            {
                $this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
            }
        }
        $this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');

    }
}
?>
