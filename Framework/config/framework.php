<?php
/**
 * 每个项目必须有项目名命名的配置文件
 */
return array(
    /**
     * 需要用命名空间的文件夹按驼峰规则命名
     */
    'path'           => array(
        'root'       => ROOT_PATH,
        'library'    => 'Libraries',
        'config'     => FRAMEWORK_CONFIG_PATH,
        'test'       => 'Tests',
        'controller' => 'Controllers',
        'view'       => 'Views',
        'plugin'     => 'Plugins',
        'cache'      => 'Cache',
        'data'       => 'Data',
        'entity'     => 'Entities',
        'model'      => 'Models'
    ),
    'template'       => array(
        'suffix' => 'phtml'
    ),
    //定义module，并控制是否开启
    'modules'        => array(
        'Demo' => true
    ),
    'default_module' => 'Demo'
);