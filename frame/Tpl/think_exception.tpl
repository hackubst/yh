<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>系统发生错误</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<meta name="robots" content="index,follow" />
<meta name="googlebot" content="index,follow" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link href="/Public/Css/base.css" rel="stylesheet" type="text/css">
<style>
body{background-color:#f7f7f7;text-align:center;}
</style>
</head>
<body>
<div class="blank150"></div>

<table align="center" bgcolor="#FFFFFF" style="border-bottom:4px solid #CCC; border-top:4px solid #CCC;" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td style="padding:20px;" align="center">
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
      <td align="center" valign="top">
<div class="blank20"></div>
<font style="font-size: 25px; font-weight: bold;"><?php echo strip_tags($e['message']);?></font>
<div class="blank20"></div>
      <font style="font-size: 14px;">很抱歉，您访问的页面不存在或已被删除！<a href="/" style="color:#00F">请点这里访问我们的网站</a>。</font>
<div class="blank20"></div>
      错误网址：<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>
<div class="blank20"></div>
	  </td>
  </tr>
</table>
	  </td>
    </tr>
</table>

</body>
</html>