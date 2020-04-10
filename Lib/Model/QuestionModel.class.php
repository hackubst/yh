<?php

class QuestionModel extends Model
{
	private $question_id;

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
	public function __construct($question_id = 0)
	{
		parent::__construct();
		$this->question_id = $question_id;
	}

	/**
     * 获取问题总数
     * @author 姜伟
     * @param string $where
     * @return int $count
     * @todo 根据where查询条件查找问题表中的记录总数
     */
    public function getQuestionNum($where = '')
    {
		return $this->where($where)->count();
	}

	/**
     * 获取问题列表
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return null或问题列表
     * @todo 根据where查询条件查找问题表中的相关数据并返回
     */
    public function getQuestionList($fields = '', $where = '', $orderby = '', $limit = '')
    {
		$question_list = $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
		return $question_list;
	}

	/**
     * 获取问题信息
     * @author 姜伟
     * @param string $where
     * @param string $fields
     * @return null或问题信息数组
     * @todo 根据where查询条件查找问题表中的相关数据并返回
     */
    public function getQuestionInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
	}

	/**
     * 添加一条问题
     * @author 姜伟
     * @param array $arr
     * @param array $question_arr	问题物数组，二维
     * @return 成功返回插入的主键ID，失败返回false
     * @todo 将问题信息添加到问题表
     */
    public function addQuestion($arr)
    {
		if (!isset($arr['question_class_id']) || !isset($arr['question_sort_id']) || !isset($arr['question_title']) || !isset($arr['question_keywords']) || !isset($arr['insert_user_id']) || !isset($arr['answer']) || !isset($arr['isuse']))
		{
			return false;
		}

		//添加到数据库
		$question_info = array(
			'question_class_id'	=> $arr['question_class_id'],
			'question_sort_id'	=> $arr['question_sort_id'],
			'question_title'	=> $arr['question_title'],
			'question_keywords'	=> $arr['question_keywords'],
			'insert_user_id'	=> $arr['insert_user_id'],
			'update_user_id'	=> $arr['insert_user_id'],
			'answer'			=> $arr['answer'],
			'isuse'				=> $arr['isuse'],
			'insert_time'		=> time(),
			'update_time'		=> time(),
		);
		$question_id = $this->add($question_info);

		return $question_id;
	}

	/**
     * 修改问题信息
     * @author 姜伟
     * @param array $arr
     * @return 成功返回1，失败返回0/false
     * @todo 修改问题信息
     */
    public function editQuestion($arr)
    {
		if (!$this->question_id)
		{
			return false;
		}

		return $this->where('question_id = ' . $this->question_id)->save($arr);
	}

	/**
     * 删除问题
     * @author 姜伟
     * @param void
     * @return 成功返回1，失败返回0/false
     * @todo 删除问题
     */
    public function deleteQuestion()
    {
		if (!$this->question_id)
		{
			return false;
		}

		//查看问题赞/踩表中是否存在该问题关联的数据，若存在，不予删除
		/*$question_agree_obj = new QuestionAgreeModel();
		$where = 'question_id = ' . $this->question_id;
		$question_agree_num = $question_agree_obj->getQuestionAgreeNum($where);
		if ($question_agree_num)
		{
			return -1;
		}*/

		return $this->where('question_id = ' . $this->question_id)->delete();
	}
}
