<?php

class QuestionClassModel extends Model
{
	private $question_class_id;

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
	public function __construct($question_class_id = 0)
	{
		parent::__construct();
		$this->question_class_id = $question_class_id;
	}

	/**
     * 获取分类总数
     * @author 姜伟
     * @param string $where
     * @return int $count
     * @todo 根据where查询条件查找分类表中的记录总数
     */
    public function getQuestionClassNum($where = '')
    {
		return $this->where($where)->count();
	}

	/**
     * 获取分类列表
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return null或分类列表
     * @todo 根据where查询条件查找分类表中的相关数据并返回
     */
    public function getQuestionClassList($fields = '', $where = '', $orderby = '', $limit = '')
    {
		$question_class_list = $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
		return $question_class_list;
	}

	/**
     * 获取分类信息
     * @author 姜伟
     * @param string $where
     * @param string $fields
     * @return null或分类信息数组
     * @todo 根据where查询条件查找分类表中的相关数据并返回
     */
    public function getQuestionClassInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
	}

	/**
     * 添加一条分类
     * @author 姜伟
     * @param array $arr
     * @return 成功返回插入的主键ID，失败返回false
     * @todo 将分类信息添加到分类表
     */
    public function addQuestionClass($arr)
    {
		if (!isset($arr['class_name']) || !isset($arr['serial']) || !isset($arr['isuse']))
		{
			return false;
		}

		//添加到数据库
		$question_class_info = array(
			'class_name'	=> $arr['class_name'],
			'serial'		=> $arr['serial'],
			'isuse'			=> $arr['isuse'],
		);
		$question_class_id = $this->add($question_class_info);

		return $question_class_id;
	}

	/**
     * 修改分类信息
     * @author 姜伟
     * @param array $arr
     * @return 成功返回1，失败返回0/false
     * @todo 修改分类信息
     */
    public function editQuestionClass($arr)
    {
		if (!$this->question_class_id)
		{
			return false;
		}

		return $this->where('question_class_id = ' . $this->question_class_id)->save($arr);
	}

	/**
     * 删除分类
     * @author 姜伟
     * @param void
     * @return 成功返回1，失败返回0/false
     * @todo 删除分类
     */
    public function deleteQuestionClass()
    {
		if (!$this->question_class_id)
		{
			return false;
		}

		//查看问题表中是否存在该分类关联的数据，若存在，不予删除
		$question_obj = new QuestionModel();
		$where = 'question_class_id = ' . $this->question_class_id;
		$question_num = $question_obj->getQuestionNum($where);
		if ($question_num)
		{
			return -1;
		}

		return $this->where('question_class_id = ' . $this->question_class_id)->delete();
	}

	/**
     * 根据分类ID查询分类名称
     * @author 姜伟
     * @param string $question_class_id
     * @param string $question_sort_id
     * @return 成功返回分类名称，失败返回false
     * @todo 根据分类ID查询分类名称
     */
    public function convertQuestionClass($question_class_id, $question_sort_id)
    {
		if (!$question_class_id || !$question_sort_id)
		{
			return false;
		}

		$where = 'question_class_id = ' . $question_class_id;
		$question_class_name = $this->where($where)->getField('class_name');
		$question_sort_obj = new QuestionSortModel();
		$where = 'question_sort_id = ' . $question_sort_id;
		$question_sort_name = $question_sort_obj->where($where)->getField('sort_name');
		$question_class = $question_class_name ? '【' . $question_class_name . '】': '';
		$question_class .= $question_sort_name ? '【' . $question_sort_name . '】': '';

		return $question_class;
	}
}
