<?php
/**
 * 基础函数库
 * @package  Common
 */

/**
 * 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位或者m 
 * @return mixed
 */
function G($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 记录时间
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 统计时间和内存使用
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if(MEMORY_LIMIT_ON && $dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);          
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }       
            
    }else{ // 记录时间和内存使用
        $_info[$start]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) $_mem[$start]           =  memory_get_usage();
    }
}

/**
 * 设置和获取统计数据
 * 使用方法:
 * <code>
 * N('db',1); // 记录数据库操作次数
 * N('read',1); // 记录读取次数
 * echo N('db'); // 获取当前页面数据库的所有操作次数
 * echo N('read'); // 获取当前页面读取次数
 * </code> 
 * @param string $key 标识位置
 * @param integer $step 步进值
 * @return mixed
 */
function N($key, $step=0,$save=false) {
    static $_num    = array();
    if (!isset($_num[$key])) {
        $_num[$key] = (false !== $save)? S('N_'.$key) :  0;
    }
    if (empty($step))
        return $_num[$key];
    else
        $_num[$key] = $_num[$key] + (int) $step;
    if(false !== $save){ // 保存结果
        S('N_'.$key,$_num[$key],$save);
    }
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {

            /*---------xyc 2017-10-11 start------------*/
            $filename = str_replace('\\', '/', $filename);
            $filename = substr($filename, strrpos($filename, '/') + 1 );
            $filename_arr = explode('.', $filename);
            $class = $filename_arr[0];
            // echo $file.'<br>';
            //到Apps中查找
            if(substr($class,-5)=='Model'){
                // $class = trim(require_apps($class, 'Model'), '*') .'.class.php';
                // $class = LIB_APP_PATH.$class;echo $class;die;
                // echo require_apps($class, 'Model');die;
                $class = str_replace('*/', LIB_APP_PATH, require_apps($class, 'Model')) .'.class.php';
                $class = str_replace('/', '\\', $class);
                if(!isset($_importFiles[$class])){
                    if (file_exists_case($class)) {
                        require $class;
                        $_importFiles[$class] = true;
                    }else{
                        $_importFiles[$class] = false;
                    }
                }
                
            }else{
                $_importFiles[$filename] = false;
            }
            /*---------xyc 2017-10-11 end------------*/
            
        }
    }
    return $_importFiles[$filename];
}

/**
 * 批量导入文件 成功则返回
 * @param array $array 文件数组
 * @param boolean $return 加载成功后是否返回
 * @return boolean
 */
function require_array($array,$return=false){
    foreach ($array as $file){
        if (require_cache($file) && $return) return true;
    }
    if($return) return false;
}

/**
 * 加载Apps下的类
 * @param   $filename 文件名
 * @param   $type     类型：Action、Model、SDK
 * @return [type]           [description]
 */
function require_apps($filename, $type){
    $data=array();
    $apps_db = dir(LIB_APP_PATH);
    while($apps_file=$apps_db->read()){
        if($apps_file!='.'&& $apps_file!='..'){
            // echo $apps_file.'<br>';
            $apps_path = LIB_APP_PATH.$apps_file.'/Lib/';
            // searchDir($dir.'/'.$file,$data, $type);
            if(is_dir($apps_path)){
                $lib_dp=dir($apps_path);
                while($lib_file=$lib_dp->read()){
                    if($lib_file!='.'&& $lib_file!='..'&& $lib_file == $type){
                        // echo $lib_file.'<br>';
                        $lib_path =  $apps_path.$lib_file;

                        // echo $lib_file;die;
                        // searchDir($dir.'/'.$file,$data, $type);
                        $type_db = dir($lib_path);

                        while($type_file=$type_db->read()){
                            if($type_file!='.'&& $type_file!='..'){
                                if(is_file($lib_path.'/'.$type_file)){
                                    $type_file_arr = explode('.', $type_file);
                                    if($type_file_arr[0] == $filename){
                                        return '*/'.$apps_file.'/Lib/'.$lib_file.'/'.$filename;
                                    }
                                    // $data[]=$lib_file.'/'.$type_file;
                                }
                            }
                        }
                        $type_db->close();
                    }
                }
                $lib_dp->close();
            }
        }
    }
    $apps_db->close();
    // dump($data);die;
    // foreach ($data as $file){
    //     require_cache($file);
    // }
    return $filename;
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && C('APP_FILE_CASE')) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * 导入所需的类库 同java的Import 本函数有缓存功能
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return boolean
 */
function import($class, $baseUrl = '', $ext='.class.php') {
    static $_file = array();
    $class = str_replace(array('.', '#'), array('/', '.'), $class);
    if ('' === $baseUrl && false === strpos($class, '/')) {
        // 检查别名导入
        return alias_import($class);
    }
    if (isset($_file[$class . $baseUrl]))
        return true;
    else
        $_file[$class . $baseUrl] = true;
    $class_strut     = explode('/', $class);
    if (empty($baseUrl)) {
        $libPath    =   defined('BASE_LIB_PATH')?BASE_LIB_PATH:LIB_PATH;
        if ('@' == $class_strut[0] || APP_NAME == $class_strut[0]) {
            //加载当前项目应用类库
            $baseUrl = dirname($libPath);
            $class   = substr_replace($class, basename($libPath).'/', 0, strlen($class_strut[0]) + 1);
        }elseif ('shagua' == strtolower($class_strut[0])){ // 官方基类库
            $baseUrl = CORE_PATH;
            $class   = substr($class,6);
        }elseif (in_array(strtolower($class_strut[0]), array('org', 'com'))) {
            // org 第三方公共类库 com 企业公共类库
            $baseUrl = LIBRARY_PATH;
        }elseif('*' == strtolower($class_strut[0])){
            $baseUrl = LIB_APP_PATH;
            $class   = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
        }else { // 加载其他项目应用类库
            $class   = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
            $baseUrl = APP_PATH . '../' . $class_strut[0] . '/'.basename($libPath).'/';
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl    .= '/';
    $classfile       = $baseUrl . $class . $ext;
    if (!class_exists(basename($class),false)) {
        // 如果类不存在 则导入类库文件
        return require_cache($classfile);
    }
}

/**
 * 基于命名空间方式导入函数库
 * load('@.Util.Array')
 * @param string $name 函数库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return void
 */
function load($name, $baseUrl='', $ext='.php') {
    $name = str_replace(array('.', '#'), array('/', '.'), $name);
    if (empty($baseUrl)) {
        if (0 === strpos($name, '@/')) {
            //加载当前项目函数库
            $baseUrl    = COMMON_PATH;
            $name       = substr($name, 2);
        } else {
            //加载ShaGua 系统函数库
            $baseUrl    = EXTEND_PATH . 'Function/';
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl       .= '/';
    require_cache($baseUrl . $name . $ext);
}

/**
 * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
 * @param string $class 类库
 * @param string $baseUrl 基础目录
 * @param string $ext 类库后缀 
 * @return boolean
 */
function vendor($class, $baseUrl = '', $ext='.php') {
    if (empty($baseUrl))
        $baseUrl = VENDOR_PATH;
    return import($class, $baseUrl, $ext);
}


/**
 * 快速导入Apps中的第三方框架类库 
 * @param string $class 类库
 * @param string $baseUrl 基础目录
 * @param string $ext 类库后缀 
 * @return boolean
 */
function vendor_apps($class, $baseUrl = '', $ext='.php') {
    // if (empty($baseUrl))
    //     $baseUrl = LIB_APP_PATH;
    $class = require_apps($class, 'SDK');
    // dump($class);die;
    return import($class, $baseUrl, $ext);
}

/**
 * 快速定义和导入别名 支持批量定义
 * @param string|array $alias 类库别名
 * @param string $classfile 对应类库
 * @return boolean
 */
function alias_import($alias, $classfile='') {
    static $_alias = array();
    if (is_string($alias)) {
        if(isset($_alias[$alias])) {
            return require_cache($_alias[$alias]);
        }elseif ('' !== $classfile) {
            // 定义别名导入
            $_alias[$alias] = $classfile;
            return;
        }
    }elseif (is_array($alias)) {
        $_alias   =  array_merge($_alias,$alias);
        return;
    }
    return false;
}

/**
 * D函数用于实例化Model 格式 项目://分组/模块
 * @param string $name Model资源地址
 * @param string $layer 业务层名称
 * @return Model
 */
function D($name='',$layer='') {
    if(empty($name)) return new Model;
    static $_model  =   array();
    $layer          =   $layer?$layer:C('DEFAULT_M_LAYER');

    /*---------xyc 2017-10-11 start --------------*/
    if(strpos($name,'://')) {// 指定项目
        $name       =   str_replace('://','/'.$layer.'/',$name);
    }elseif(strpos($name,'Apps/')){
        $name =  str_replace('/Apps/','',$name);
        $name = require_apps($name.$layer, 'Model');
        $layer = '';
    }else{
        $name       =   C('DEFAULT_APP').'/'.$layer.'/'.$name;
    }
    /*---------xyc 2017-10-11 end --------------*/

    if(isset($_model[$name]))   return $_model[$name];
    import($name.$layer);
    $class          =   basename($name.$layer);
    if(class_exists($class)) {
        $model      =   new $class();
    }else {
        $model      =   new Model(basename($name));
    }
    $_model[$name]  =  $model;
    return $model;
}

/**
 * M函数用于实例化一个没有模型文件的Model
 * @param string $name Model名称 支持指定基础模型 例如 MongoModel:User
 * @param string $tablePrefix 表前缀
 * @param mixed $connection 数据库连接信息
 * @return Model
 */
function M($name='', $tablePrefix='',$connection='') {
    static $_model  = array();
    if(strpos($name,':')) {
        list($class,$name)    =  explode(':',$name);
    }else{
        $class      =   'Model';
    }
    $guid           =   $tablePrefix . $name . '_' . $class;
    if (!isset($_model[$guid]))
        $_model[$guid] = new $class($name,$tablePrefix,$connection);
    return $_model[$guid];
}

/**
 * A函数用于实例化Action 格式：[项目://][分组/]模块
 * @param string $name Action资源地址
 * @param string $layer 控制层名称
 * @param boolean $common 是否公共目录
 * @return Action|false
 */
function A($name,$layer='',$common=false) {
    $name_1 = $name;
    static $_action = array();
    $layer      =   $layer?$layer:C('DEFAULT_C_LAYER');

    /*---------xyc 2017-10-11 start --------------*/
    if(strpos($name,'://')) {// 指定项目
        $name   =  str_replace('://','/'.$layer.'/',$name);
    }elseif(strpos($name,'Apps/')){
        $name =  str_replace('/Apps/','',$name);
        $name = require_apps($name.$layer, 'Action');
        $layer = '';
    }else{
        $name   =  '@/'.$layer.'/'.$name;
    }
    
    /*---------xyc 2017-10-11 end --------------*/

    if(isset($_action[$name]))  return $_action[$name];
    if($common){ // 独立分组情况下 加载公共目录类库
        import(str_replace('@/','',$name).$layer,LIB_PATH);
    }else{
        import($name.$layer); 
    }    
    $class      =   basename($name.$layer);
    if(class_exists($class,false)) {
        $action             = new $class();
        $_action[$name]     =  $action;
        return $action;
    }else {
        return false;
    }
}

/**
 * 远程调用模块的操作方法 URL 参数格式 [项目://][分组/]模块/操作
 * @param string $url 调用地址
 * @param string|array $vars 调用参数 支持字符串和数组 
 * @param string $layer 要调用的控制层名称
 * @return mixed
 */
function R($url,$vars=array(),$layer='') {
    $info   =   pathinfo($url);
    $action =   $info['basename'];
    $module =   $info['dirname'];
    $class  =   A($module,$layer);
    if($class){
        if(is_string($vars)) {
            parse_str($vars,$vars);
        }
        return call_user_func_array(array(&$class,$action.C('ACTION_SUFFIX')),$vars);
    }else{
        return false;
    }
}

/**
 * 获取和设置语言定义(不区分大小写)
 * @param string|array $name 语言变量
 * @param string $value 语言值
 * @return mixed
 */
function L($name=null, $value=null) {
    static $_lang = array();
    // 空参数返回所有定义
    if (empty($name))
        return $_lang;
    // 判断语言获取(或设置)
    // 若不存在,直接返回全大写$name
    if (is_string($name)) {
        $name = strtoupper($name);
        if (is_null($value))
            return isset($_lang[$name]) ? $_lang[$name] : $name;
        $_lang[$name] = $value; // 语言定义
        return;
    }
    // 批量定义
    if (is_array($name))
        $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
    return;
}

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @return mixed
 */
function C($name=null, $value=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        if(!empty($value) && $array = S('c_'.$value)) {
            $_config = array_merge($_config, array_change_key_case($array));
        }
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    // 批量设置
    if (is_array($name)){
        foreach ($name as $k => $v) {
            if(is_array($v) && isset($_config[strtolower($k)])){
                $name[$k] = array_merge($v, $_config[strtolower($k)]);
            }
        }

        $_config = array_merge($_config, array_change_key_case($name));
        if(!empty($value)) {// 保存配置值
            S('c_'.$value,$_config);
        }
        return;
    }
    return null; // 避免非法参数
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @return mixed
 */
function I($name,$default='',$filter=null) {
    if(strpos($name,'.')) { // 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ // 默认为自动判断
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   $input =& $_GET;break;
        case 'post'    :   $input =& $_POST;break;
        case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'request' :   $input =& $_REQUEST;   break;
        case 'session' :   $input =& $_SESSION;   break;
        case 'cookie'  :   $input =& $_COOKIE;    break;
        case 'server'  :   $input =& $_SERVER;    break;
        case 'globals' :   $input =& $GLOBALS;    break;
        default:
            return NULL;
    }
    if(empty($name)) { // 获取全部变量
        $data       =   $input;
        array_walk_recursive($data,'filter_exp');
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            $filters    =   explode(',',$filters);
            foreach($filters as $filter){
                $data   =   array_map_recursive($filter,$data); // 参数过滤
            }
        }
    }elseif(isset($input[$name])) { // 取值操作
        $data       =   $input[$name];
        is_array($data) && array_walk_recursive($data,'filter_exp');
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            $filters    =   explode(',',$filters);
            foreach($filters as $filter){
                if(function_exists($filter)) {
                    $data   =   is_array($data)?array_map_recursive($filter,$data):$filter($data); // 参数过滤
                }else{
                    $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                    if(false === $data) {
                        return   isset($default)?$default:NULL;
                    }
                }
            }
        }
    }else{ // 变量默认值
        $data       =    isset($default)?$default:NULL;
    }
    return $data;
}

function array_map_recursive($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
            ? array_map_recursive($filter, $val)
            : call_user_func($filter, $val);
    }
    return $result;
}

/**
 * 处理标签扩展
 * @param string $tag 标签名称
 * @param mixed $params 传入参数
 * @return mixed
 */
function tag($tag, &$params=NULL) {
    // 系统标签扩展
    $extends    = C('extends.' . $tag);
    // 应用标签扩展
    $tags       = C('tags.' . $tag);

    if (!empty($tags)) {
        if(empty($tags['_overlay']) && !empty($extends)) { // 合并扩展
            $tags = array_unique(array_merge($extends,$tags));
        }elseif(isset($tags['_overlay'])){ // 通过设置 '_overlay'=>1 覆盖系统标签
            unset($tags['_overlay']);
        }
    }elseif(!empty($extends)) {
        $tags = $extends;
    }
    if($tags) {
        if(APP_DEBUG) {
            G($tag.'Start');
            trace('[ '.$tag.' ] --START--','','INFO');
        }
        // 执行扩展
        foreach ($tags as $key=>$name) {
            if(!is_int($key)) { // 指定行为类的完整路径 用于模式扩展
                $name   = $key;
            }
            B($name, $params);
        }
        if(APP_DEBUG) { // 记录行为的执行日志
            trace('[ '.$tag.' ] --END-- [ RunTime:'.G($tag.'Start',$tag.'End',6).'s ]','','INFO');
        }
    }else{ // 未执行任何行为 返回false
        return false;
    }
}

/**
 * 动态添加行为扩展到某个标签
 * @param string $tag 标签名称
 * @param string $behavior 行为名称
 * @param string $path 行为路径 
 * @return void
 */
function add_tag_behavior($tag,$behavior,$path='') {
    $array      =  C('tags.'.$tag);
    if(!$array) {
        $array  =  array();
    }
    if($path) {
        $array[$behavior] = $path;
    }else{
        $array[] =  $behavior;
    }
    C('tags.'.$tag,$array);
}

/**
 * 执行某个行为
 * @param string $name 行为名称
 * @param Mixed $params 传人的参数
 * @return void
 */
function B($name, &$params=NULL) {
    $class      = $name.'Behavior';
    if(APP_DEBUG) {
        G('behaviorStart');
    }
    $behavior   = new $class();
    $behavior->run($params);
    if(APP_DEBUG) { // 记录行为的执行日志
        G('behaviorEnd');
        trace('Run '.$name.' Behavior [ RunTime:'.G('behaviorStart','behaviorEnd',6).'s ]','','INFO');
    }
}

/**
 * 去除代码中的空白和注释
 * @param string $content 代码内容
 * @return string
 */
function strip_whitespace($content) {
    $stripStr   = '';
    //分析php源码
    $tokens     = token_get_all($content);
    $last_space = false;
    for ($i = 0, $j = count($tokens); $i < $j; $i++) {
        if (is_string($tokens[$i])) {
            $last_space = false;
            $stripStr  .= $tokens[$i];
        } else {
            switch ($tokens[$i][0]) {
                //过滤各种PHP注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //过滤空格
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr  .= ' ';
                        $last_space = true;
                    }
                    break;
                case T_START_HEREDOC:
                    $stripStr .= "<<<SHAGUA\n";
                    break;
                case T_END_HEREDOC:
                    $stripStr .= "SHAGUA;\n";
                    for($k = $i+1; $k < $j; $k++) {
                        if(is_string($tokens[$k]) && $tokens[$k] == ';') {
                            $i = $k;
                            break;
                        } else if($tokens[$k][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr  .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}

//[RUNTIME]
// 编译文件
function compile($filename) {
    $content        = file_get_contents($filename);
    // 替换预编译指令
    $content        = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s', '', $content);
    $content        = substr(trim($content), 5);
    if ('?>' == substr($content, -2))
        $content    = substr($content, 0, -2);
    return $content;
}

// 根据数组生成常量定义
function array_define($array,$check=true) {
    $content = "\n";
    foreach ($array as $key => $val) {
        $key = strtoupper($key);
        if($check)   $content .= 'defined(\'' . $key . '\') or ';
        if (is_int($val) || is_float($val)) {
            $content .= "define('" . $key . "'," . $val . ');';
        } elseif (is_bool($val)) {
            $val = ($val) ? 'true' : 'false';
            $content .= "define('" . $key . "'," . $val . ');';
        } elseif (is_string($val)) {
            $content .= "define('" . $key . "','" . addslashes($val) . "');";
        }
        $content    .= "\n";
    }
    return $content;
}
//[/RUNTIME]

/**
 * 添加和获取页面Trace记录
 * @param string $value 变量
 * @param string $label 标签
 * @param string $level 日志级别 
 * @param boolean $record 是否记录日志
 * @return void
 */
function trace($value='[shagua]',$label='',$level='DEBUG',$record=false) {
    static $_trace =  array();
    if('[shagua]' === $value){ // 获取trace信息
        return $_trace;
    }else{
        $info   =   ($label?$label.':':'').print_r($value,true);
        if('ERR' == $level && C('TRACE_EXCEPTION')) {// 抛出异常
            throw_exception($info);
        }
        $level  =   strtoupper($level);
        if(!isset($_trace[$level])) {
                $_trace[$level] =   array();
            }
        $_trace[$level][]   = $info;
        if((defined('IS_AJAX') && IS_AJAX) || !C('SHOW_PAGE_TRACE')  || $record) {
            Log::record($info,$level,$record);
        }
    }
}

/*
 * URL地址加密
 */
function url_jiami($string, $key='abcdefghijklmnopqrstuvwxyz', $rand=true) {
	$encrypt_key = md5($key[rand(0, (strlen($key) -1))]);
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($string); $i++) 
	{
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $rand ? $encrypt_key[$ctr] . ($string[$i] ^ $encrypt_key[$ctr++]) : $string[$i];
	}
	//采用base64_encode加密再将字符串放入函数中再次进行处理
	return str_replace('\\', ';fxx;', str_replace('/', ';xx;', base64_encode(url_jm_key($tmp,$key))));
}

/*
 * URL地址解密（将已加密的地址解密）
 */
function url_jiemi($string, $key='abcdefghijklmnopqrstuvwxyz', $rand=true) {
	//使用base64_encode解密再将字符串进行处理
	$string = url_jm_key(base64_decode(str_replace(';fxx;', '\\', str_replace(';xx;', '/', $string))), $key);
	//直接将字符串进行处理
	$tmp = '';
	for($i = 0;$i < strlen($string); $i++)
	{
		$md5 = $string[$i];
		$tmp .= $rand ? $string[++$i] ^ $md5 : $md5;
	}
	return $tmp;
} 

/*
 * 生成URL加密解密的KEY
 */
function url_jm_key($string, $encrypt_key) {
	$encrypt_key = md5($encrypt_key);
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($string); $i++)
	{
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $string[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}

/**
 * 清除缓存目录
 * @param $type 目录类型，0表示所有目录、1模版缓存目录CACHE_PATH、2数据缓存目录TEMP_PATH、3日志目录LOG_PATH、4数据目录DATA_PATH
 * @return bool
 */
function clear_cache($type = 0) {
	switch($type) {
	case 1://模版缓存目录
		$path = CACHE_PATH;
		break;
	case 2://数据缓存目录
		$path = TEMP_PATH;
		break;
	case 3://日志目录
		$path = LOG_PATH;
		break;
	case 4://数据目录
		$path = DATA_PATH;
	default:
		$path = array(CACHE_PATH, TEMP_PATH, LOG_PATH, DATA_PATH);
		break;
	}
	
	$rs = true;
	
	if (is_array($path)) {
		foreach ($path as $k => $v) {
			if (!delete_folder($v)) {
				$rs = false;
			}
		}
	} else {
		if (!delete_folder($path)) {
			$rs = false;
		}
	}
	
	return $rs;
}

/**
 * 删除目录下的文件
 * @param $tmp_path 目录路径
 * @param $del_self 是否删除目录本身
 * @param $del_sub 是否删除子目录
 * @return bool
 */
function delete_folder($tmp_path, $del_self = false, $del_sub = false) {
	$tmp_path = rtrim(rtrim($tmp_path, '/'), "\\");
	
	if (!is_writeable($tmp_path) && is_dir($tmp_path)) {
		chmod($tmp_path, 0777);
	}
	
	$handle = opendir($tmp_path);
	
	while (false !== ($tmp = readdir($handle))) {
		if ($tmp != '..' && $tmp != '.' && $tmp != '') {
			if (is_writeable($tmp_path . DIRECTORY_SEPARATOR . $tmp) && is_file($tmp_path . DIRECTORY_SEPARATOR . $tmp)){
				unlink($tmp_path . DIRECTORY_SEPARATOR . $tmp);
			} elseif (!is_writeable($tmp_path . DIRECTORY_SEPARATOR . $tmp) && is_file($tmp_path . DIRECTORY_SEPARATOR . $tmp)){
				chmod($tmp_path . DIRECTORY_SEPARATOR . $tmp, 0666);
				unlink($tmp_path . DIRECTORY_SEPARATOR . $tmp);
			}
	
			if (is_writeable($tmp_path . DIRECTORY_SEPARATOR . $tmp) && is_dir($tmp_path . DIRECTORY_SEPARATOR . $tmp) && $del_sub === true){
				delete_folder($tmp_path . DIRECTORY_SEPARATOR . $tmp, true, true);
			} elseif (!is_writeable($tmp_path . DIRECTORY_SEPARATOR . $tmp) && is_dir($tmp_path . DIRECTORY_SEPARATOR . $tmp) && $del_sub === true){
				chmod($tmp_path . DIRECTORY_SEPARATOR . $tmp, 0777);
				delete_folder($tmp_path . DIRECTORY_SEPARATOR . $tmp, true, true);
			}
		}
	}
	
	closedir($handle);
	
	if ($del_self === true) {
		rmdir($tmp_path);
		
		if (!is_dir($tmp_path)){
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return true;
	}
}

/**
 * 输出JS提示
 * @param $mesg
 * @return NULL
 */
function alert_mesg($mesg) {
	header("Content-Type:text/html; charset=utf-8");
	echo '<script>alert("'. $mesg . '");</script>';
	exit;
}

/**
 * 显示JS
 * @param $message JS提示信息
 * @param $focus　要激活的表单项ID
 * @param $url 要重定向的URL地址
 * @param $other 其他JS信息
 * @return NULL
 */
function show_alert($message, $focus = '', $url = '', $other = '') {
	header("Content-Type:text/html; charset=utf-8");
	echo '<script>alert("' . $message . '");';
	if ($focus) {
		echo $focus . '.focus();';
	}

	if ($url) {
		if ( $url == -1 || $url == -2 || $url == -3 || $url == -4 || $url == -5 )
		{
			echo 'window.history.go(' . $url . ');';
		}
		else
		{
			echo "top.location.href = '" . $url . "';";
		}
	}

	if ($other) {
		echo $other;
	}

	echo '</script>';
	exit;
}

//给文本内容中的图片加链接
function set_img_link($txt, $file_url='/Index/imgviewer/') {
	//取得文本内容的图片网址
	@preg_match_all('/<img[^>]*(\/Upload\/editor\/[^ \'"]*)[^>]*>/i', $txt, $txt_img);
	
	if (isset($txt_img) && isset($txt_img[0]) && isset($txt_img[1])) {
		//给图片加链接
		
		foreach ($txt_img[0] as $k => $v) {
			$txt = str_replace($v, '<a href="' . $file_url . 'pic/' . url_jiami($txt_img[1][$k]) . '" target="_blank">' . $v . '</a>', $txt);
		}
	}
	
	return $txt;
}

/**
 *  验证固话
 * @param $tel 电话
 * @return bool 返回真表示正确，否则为不正确
 */
function check_tel($tel) {
	$preg = C('TEL_PREG');
	$preg = $preg?$preg:'/(\d{2,5}-\d{7,8}(-\d{1,})?)/';
	if (preg_match($preg, $tel))
	{
		return true;
	}

	return false;
}

/**
 * 验证手机号
 * @param $mobile 手机号
 * @return bool 返回真表示正确，否则为不正确
 */
function check_mobile($mobile) {
	$preg = C('MOBILE_PREG');
	$preg = $preg?$preg:'/^(0)?(13[0-9]|15[0-9]|18[0-9])\d{8}$/i';
	if (preg_match($preg, $mobile))
	{
		return true;
	}

	return false;
}

/**
 * 验证网址
 * @param $url 网址
 * @return bool 返回真表示正确，否则为不正确
 */
function check_url($url) {
	if (preg_match("/^http:\/\/([0-9a-zA-Z_-]+)*.[0-9a-zA-Z_-]+(.[0-9a-zA-Z]{2,4}){1,2}(\/[^\/]*)*$/i", $url))
	{
		return true;
	}

	return false;
}

/**
 * 验证邮箱
 * @param $email 邮箱
 * @return bool 返回真表示正确，否则为不正确
 */
function check_email($email) {
	if (preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9_-]+\.[a-zA-Z0-9._-]+$/i", $email))
	{
		return true;
	}

	return false;
}

/**
 * 截取字符串
 * @param $str 要截取的字符串
 * @param $length 要截取的长度
 * @param $start 开始位置
 * @param $suffix 是否追加
 * @return String 字符串
 */
function substr_utf8($str, $length, $start=0, $suffix=true)
{
	if(function_exists("mb_substr")){
		if ($suffix && strlen($str)>$length)
			return mb_substr($str, $start, $length, 'utf-8') . "...";
		else
			return mb_substr($str, $start, $length, 'utf-8');
	}
	elseif(function_exists('iconv_substr')) {
		if ($suffix && strlen($str)>$length)
			return iconv_substr($str, $start, $length, 'utf-8') . "...";
		else
			return iconv_substr($str, $start, $length, 'utf-8');
	}
	
	$re = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	preg_match_all($re, $str, $match);
	$slice = join("", array_slice($match[0], $start, $length));
	if($suffix) return $slice . "...";
	return $slice;
}

/**
 * 统计字符串字数
 * @param $str 字符串
 * @return Number 字数　
 */
function strlen_utf8($str) {
	$i = 0;
	$count = 0;
	$len = strlen ($str);
	while ($i < $len) {
		$chr = ord ($str[$i]);
		$count++;
		$i++;
		if($i >= $len) break;
		if($chr & 0x80) {
			$chr <<= 1;
			while ($chr & 0x80) {
				$i++;
				$chr <<= 1;
			}
		}
	}
	return $count;
}

/**
 * 访问网址并取得其内容
 * @param $url String 网址
 * @param $postFields Array 将该数组中的内容用POST方式传递给网址中
 * @return String 返回网址内容
 */
function curl_content($url, $postFields = null)
{
    $postUrl = $url.'/api/api';
//    echo $postUrl;
    $curlPost = $postFields;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return $data;
//    echo ($data);die;
}

/**
 * 创建GET方式的URL网址
 * @param $url String 网址
 * @param $get_arr String|Array 网址后面要连接的内容
 * @param $append_wh Bool 是否在网址后面追加问号
 * @param $append_and Bool 是否在网址后面追加&号
 * @return String 返回网址
 */
function create_get_url($url, $get_arr, $append_wh = true, $append_and = false) {
	if (is_string($get_arr)) {
		if ($append_wh) {
			$url .= '?' . $get_arr;
		} elseif ($append_and) {
			$url .= '&' . $get_arr;
		} else {
			$url .= $get_arr;
		}
	}
	
	if (is_array($get_arr)) {
		if ($append_wh) {
			$url .= '?';
		} elseif ($append_and) {
			$url .= '&';
		}
		$i = 0;
		foreach ($get_arr as $k => $v) {
			$url .= ($i > 0) ? '&' : '';
			if (is_array($v)) $v = implode(',', $v);
			$url .= $k . '=' . $v;
			$i++;
		}
	}
	
	return $url;
}


/**
 * 加载Apps中个模块的config.php
 * @author xyc 2017-10-11
 * @return [type] [description]
 */
function include_apps_conf(){
    $apps_db = dir(LIB_APP_PATH);
    while($apps_file=$apps_db->read()){
        if($apps_file!='.'&& $apps_file!='..'){
            $conf_path = LIB_APP_PATH.$apps_file.'/Conf/config.php';
            if(is_file($conf_path)){
                 C(include $conf_path);
            }
        }
    }
    $apps_db->close();
}


/**
 * 获取Apps中控制器所在的目录名
 * @param  [type] $moudle [description]
 * @return [type]         [description]
 */
function get_app_name($module = ''){
    if(!$module) return '';

    $path = require_apps($module.'Action', 'Action');
    $path_arr = explode('/', $path);
    return isset($path_arr[1]) ? $path_arr[1] : '';
    
}
