<?php 
if(!defined('ARTISTICPHOENIX_DIR')) define('ARTISTICPHOENIX_DIR', str_replace('\\','/', realpath(__DIR__.'/../..')));
return array(
    'output'        => ARTISTICPHOENIX_DIR.'/css/style-override.css',
    'import'        => ARTISTICPHOENIX_DIR.'/scss',
    'cache'         => __DIR__,
    'format'        => 'expanded',//compact|compressed|crunched|expanded|nested
    'bootstrap'     => 'bootstrap', 
);



