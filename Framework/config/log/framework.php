<?php
$log_root_path = '/data1/www/logs/my_app';
return array(
    'root_path'          => $log_root_path . '/framework',
    'file_name'          => '',
    'suffix_date_format' => 'YmdH',
    'lock_wait'          => 0.3,  //文件写锁获取等待时间，单位秒
    'buffer_line_num'    => 20,   //内存缓冲行数。进程异常退出会丢失数据
    'is_use_buffer'      => true // 是否启用缓冲模式
);