#!/usr/bin/env php
<?php
create_model($argv);
function create_model($params)
{
    if($params)
    {
        $table_name = $params[1]; //表名用下划线隔开
        if(!$table_name)
        {
            echo 'Please fill in the name of the table !';die;
        }

        $model_name = implode(array_map('ucfirst', explode('_', $table_name)));
        $py_key     = $table_name .'_id';
        $remark     = '模型';
        if(!$remark)
        {
            echo 'Please fill out the note in Chinese !';die;
        }

        $content = file_get_contents('./Public/template/model.template');
        $content = str_replace("__REMARK__",$remark,$content);
        $content = str_replace("__MODEL_NAME__",$model_name,$content);
        $content = str_replace("__PY_KEY__",$py_key,$content);
        $content = str_replace("__TABLE_NAME__",$table_name,$content);
        
        $save_path = './Lib/Model/'.$model_name.'Model.class.php';
        if(file_exists($save_path))
        {
            echo 'The model file already exists！';
        }
        $result = file_put_contents($save_path, $content);
        if($result) {
            echo 'Model "'.$model_name.'Model.class.php" create success!';
        }else{
            echo 'Model create field !';
        }
    }
}
