<?php
/**
 * acp后台商品类
 */
class AcpMaterialAction extends AcpAction
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
    }

    protected function get_search_condition()
    {
        $where = "";
        $name = $this->_request('name');
        if($name){
            $where .= ' AND name LIKE "%' . $name . '%"';
            $this->assign('name', $name);
        }
        return $where;
    }


    /**
     * 实物礼品列表
     */
    public function get_material_list()
    {
        $where = 'isuse = 1';
        $where .= $this->get_search_condition();
        $material_obj = new MaterialModel();

        import('ORG.Util.Pagelist');
        $count = $material_obj->getMaterialNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $material_obj->setStart($Page->firstRow);
        $material_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $material_list = $material_obj->getMaterialList('',$where,'serial desc');
        // dump($user_gift_password_list);die;

        $this->assign('material_list', $material_list ? : array());
        $this->assign('head_title', '礼品列表');
        $this->display();
    }


    /**
     * 添加/修改礼品
     */
    public function add_material()
    {
        $material_obj = new MaterialModel();

        $id = I('id');
        $material_info = $material_obj->getMaterialInfo('material_id = ' . $id, '');
        $material_info['content'] = htmlspecialchars_decode($material_info['content']);
        $this->assign('material_info', $material_info);
        $this->assign('pic_data', array(
            'name' => 'img_url',
            'title' => '实物图',
            'url' => $material_info['img_url'],
            'help' => '<span style="color:red;">图片尺寸：188*146；</span>'
        ));
        if (IS_POST) {
            $name = $this->_post('name');
            $id = $this->_post('id');
            $money = $this->_post('money');
            $img_url = $this->_post('img_url');
            $content = $this->_post('content');
            $serial = $this->_post('serial');
            if (!$name) {
                $this->error('对不起，请填写礼品名称');
            }
            if (!$money) {
                $this->error('对不起，请填写兑换所需金豆');
            }
            if (!$img_url) {
                $this->error('对不起，请上传图片');
            }

            $data = array(
                'name' => $name,
                'money' => $money,
                'img_url' => $img_url,
                'content' => $content ?: '',
                'serial' => $serial ?: 0,
            );
            if (!$id) //执行添加操作
            {
                $material_id = $material_obj->addMaterial($data);
                if ($material_id) {
                    $this->success('恭喜您，实物礼品添加成功', '/AcpMaterial/get_material_list');
                } else {
                    $this->success('抱歉，添加失败');
                }
            } else {
                $res = $material_obj->editMaterial('material_id =' . $id, $data);
                if ($res !== false) {
                    $this->success('恭喜您，实物礼品修改成功', '/AcpMaterial/get_material_list');
                } else {
                    $this->success('抱歉，修改失败');
                }
            }


        }
        $this->assign('head_title','添加实物礼品卡');
        $this->assign('action','add');
        $this->display();
    }



    //删除礼品
    public function delete_material()
    {
        $id = I('post.id', 0, 'int');

        if ($id) {
            $material_obj = new MaterialModel();
            $status = $material_obj->delMaterial($id);

            exit($status ? 'success' : 'failure');
        }
        exit('failure');
    }


    /**
     * 快速修改排序号
     */
    public function edit_serial()
    {
        $id = $this->_get('id');
        $serial = $this->_get('serial');
        if($this->isAjax() && $id)
        {
            //验证ID是否为数字
            if(!ctype_digit($id))
            {
                $this->_ajaxFeedback(0, null, '参数无效！');
            }
            if(!ctype_digit($serial))
            {
                $this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
            }

            $material_obj = new MaterialModel();
            if (false !== $material_obj->editMaterial('material_id =' . $id, array('serial' => $serial)))
            {
                $this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
            }
            $this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
        }
    }
}
