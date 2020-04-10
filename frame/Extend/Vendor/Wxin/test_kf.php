<?php
require('./WeiXin.php');
$appid = 'wxf39c1f30969a7e57';
$secret = 'f2d31b4cfe7662e7573276086f207be8';
#$online_arr = file_get_contents('https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=' . WxApi::getAccessToken($appid, $secret));
echo $online_arr;
