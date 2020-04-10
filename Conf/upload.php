<?php

return array(

    'DEFAULT_IMAGE_UPLOAD_DRIVER' => 'qiniu',
    'DEFAULT_IMAGE_UPLOAD_DIR' => 'default',

    'LOCAL_UPLOAD_DRIVER_CONFIG' => array(
        'IMAGE_DOMAIN' => 'http://' . $_SERVER['HTTP_HOST'] . '/',
    ),

    'QINIU_UPLOAD_DRIVER_CONFIG' => array(
        // 七牛 AccessKey
        'QINIU_ACCESS_KEY'      => '8Gbs4UEkpSd-oO6atBEDPT1XnXit5rWoDKL4JtsZ',
        // 七牛 SecretKey
        'QINIU_SECRET_KEY'      => 'xFuDQI2bp4buFl5Kkkn0jx0nWsc3OsrD4RdtoSMr',

        // 七牛图片空间 bucket
        'QINIU_IMAGES_BUCKET'   => 'kkl',
        // 七牛图片空间对应的自定义域名
        'QINIU_IMAGES_DOMAIN'   => 'http://img.jinlong28.com/',
    ),
);
