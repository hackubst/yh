<?php

class QuestionSortModel extends Model
{
	private $question_sort_id;

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
	public function __construct($question_sort_id = 0)
	{
		parent::__construct();
		$this->question_sort_id = $question_sort_id;
	}

	/**
     * 获取二级分类总数
     * @author 姜伟
     * @param string $where
     * @return int $count
     * @todo 根据where查询条件查找二级分类表中的记录总数
     */
    public function getQuestionSortNum($where = '')
    {
		return $this->where($where)->count();
	}

	/**
     * 获取二级分类列表
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return null或二级分类列表
     * @todo 根据where查询条件查找二级分类表中的相关数据并返回
     */
    public function getQuestionSortList($fields = '', $where = '', $orderby = '', $limit = '')
    {
		$question_sort_list = $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
		return $question_sort_list;
	}

	/**
     * 获取二级分类信息
     * @author 姜伟
     * @param string $where
     * @param string $fields
     * @return null或二级分类信息数组
     * @todo 根据where查询条件查找二级分类表中的相关数据并返回
     */
    public function getQuestionSortInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
	}

	/**
     * 添加一条二级分类
     * @author 姜伟
     * @param array $arr
     * @return 成功返回插入的主键ID，失败返回false
     * @todo 将二级分类信息添加到二级分类表
     */
    public function addQuestionSort($arr)
    {
		if (!isset($arr['sort_name']) || !isset($arr['serial']) || !isset($arr['isuse']))
		{
			return false;
		}

		//添加到数据库
		$question_sort_info = array(
			'sort_name'		=> $arr['sort_name'],
			'serial'		=> $arr['serial'],
			'isuse'			=> $arr['isuse'],
		);
		$question_sort_id = $this->add($question_sort_info);

		return $question_sort_id;
	}

	/**
     * 修改二级分类信息
     * @author 姜伟
     * @param array $arr
     * @return 成功返回1，失败返回0/false
     * @todo 修改二级分类信息
     */
    public function editQuestionSort($arr)
    {
		if (!$this->question_sort_id)
		{
			return false;
		}

		return $this->where('question_sort_id = ' . $this->question_sort_id)->save($arr);
	}

	/**
     * 删除二级分类
     * @author 姜伟
     * @param void
     * @return 成功返回1，失败返回0/false
     * @todo 删除二级分类
     */
    public function deleteQuestionSort()
    {
		if (!$this->question_sort_id)
		{
			return false;
		}

		//查看问题表中是否存在该二级分类关联的数据，若存在，不予删除
		$question_obj = new QuestionModel();
		$where = 'question_sort_id = ' . $this->question_sort_id;
		$question_num = $question_obj->getQuestionNum($where);
		if ($question_num)
		{
			return -1;
		}

		return $this->where('question_sort_id = ' . $this->question_sort_id)->delete();
	}
}
