<?php

$defaults = include dirname(__DIR__, 3).'/config/parser.php';

$defaults['View_Jade']['include'] = array();
$defaults['View_Dwoo']['include'] = array();
$defaults['View_Phptal']['include'] = array();
$defaults['View_Haml']['include'] = array();

$defaults['View_Twig']['environment']['cache'] = APPPATH.'cache'.DS.'twig'.DS;
$defaults['View_Mustache']['environment']['cache_dir'] = APPPATH.'cache'.DS.'mustache'.DS;
$defaults['View_Smarty']['environment']['compile_dir'] = APPPATH.'tmp'.DS.'Smarty'.DS.'templates_c'.DS;
$defaults['View_Smarty']['environment']['config_dir'] = APPPATH.'tmp'.DS.'Smarty'.DS.'configs'.DS;
$defaults['View_Smarty']['environment']['cache_dir'] = APPPATH.'cache'.DS.'Smarty'.DS;
$defaults['View_Handlebars']['compile_dir'] = APPPATH.'tmp'.DS.'handlebars'.DS;
$defaults['View_Jade']['cache_dir'] = APPPATH.'cache'.DS.'jade'.DS;
$defaults['View_Jade']['paths'] = array(APPPATH.'views');
$defaults['View_Jade']['compiler_options'] = array('paths' => array(APPPATH.'views'));
$defaults['View_Phptal']['cache_dir'] = APPPATH.'cache'.DS.'PHPTAL'.DS;

return $defaults;
