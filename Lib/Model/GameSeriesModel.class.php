<?php
/**
 * 游戏系列模型类
 * table_name = tp_game_series
 * py_key = game_series_id
 */

class GameSeriesModel extends Model
{
    //该表主键id不允许修改，修改会出错
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化游戏系列id
     */
    public function GameSeriesModel()
    {
        parent::__construct('game_series');
    }

    /**
     * 获取游戏系列信息
     * @author 姜伟
     * @param int $game_series_id 游戏系列id
     * @param string $fields 要获取的字段名
     * @return array 游戏系列基本信息
     * @todo 根据where查询条件查找游戏系列表中的相关数据并返回
     */
    public function getGameSeriesInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改游戏系列信息
     * @author 姜伟
     * @param array $arr 游戏系列信息数组
     * @return boolean 操作结果
     * @todo 修改游戏系列信息
     */
    public function editGameSeries($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加游戏系列
     * @author 姜伟
     * @param array $arr 游戏系列信息数组
     * @return boolean 操作结果
     * @todo 添加游戏系列
     */
    public function addGameSeries($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除游戏系列
     * @author 姜伟
     * @param int $game_series_id 游戏系列ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delGameSeries($game_series_id,$opt = false)
    {
        if (!is_numeric($game_series_id)) return false;
        if($opt)
        {
            return $this->where('game_series_id = ' . $game_series_id)->delete();
        }else{
           return $this->where('game_series_id = ' . $game_series_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取游戏系列数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的游戏系列数量
     * @todo 根据where子句获取游戏系列数量
     */
    public function getGameSeriesNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询游戏系列信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 游戏系列基本信息
     * @todo 根据SQL查询字句查询游戏系列信息
     */
    public function getGameSeriesList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    public function getGameSeriesAll($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getGameSeriesField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取游戏系列列表页数据信息列表
     * @author 姜伟
     * @param array $game_series_list
     * @return array $game_series_list
     * @todo 根据传入的$GameSeries_list获取更详细的游戏系列列表页数据信息列表
     */
    public function getListData($game_series_list,$is_app = 0)
    {
        if($is_app){
            $where = ' AND is_show_app = 1';
        }else{
            $where = '';
        }
        $game_type_obj = new GameTypeModel();
        foreach($game_series_list as $k => &$v){
            $game_type_list = $game_type_obj->getGameTypeAll('game_type_id,game_type_name,table_type',
                'game_series_id ='.$v['game_series_id'].' AND isuse = 1'.$where);
            $v['game_type_list'] = $game_type_list;
        }
        return $game_series_list;
    }

}
