<?php

declare (strict_types=1);
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.8.2
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */
namespace Parser;

use Everzet\Jade as Everzet;
use Tale\Jade as Tale;
class View_Jade extends \View
{
    /**
     * @InheritDoc
     */
    public $extension = 'jade';
    /**
     * Returns the Parser lib object
     *
     * @return  Everzet\Jade
     */
    protected function everzet_parser($cachepath)
    {
        // create a parser object
        $parser = new Everzet\Parser(new Everzet\Lexer\Lexer());
        // create a dumper object
        $dumper = new Everzet\Dumper\Php_Dumper();
        $dumper->register_visitor('tag', new Everzet\Visitor\Autotags_Visitor());
        $dumper->register_filter('javascript', new Everzet\Filter\Java_Script_Filter());
        $dumper->register_filter('cdata', new Everzet\Filter\Cdata_Filter());
        $dumper->register_filter('php', new Everzet\Filter\Php_Filter());
        $dumper->register_filter('style', new Everzet\Filter\Css_Filter());
        // return the Jade parser
        return new Everzet\Jade($parser, $dumper, $cachepath);
    }
    /**
     * Returns the Parser lib object
     *
     * @return  Tale\Jade
     */
    protected function tale_parser($cachepath)
    {
        // get the config
        $config = \Config::get('parser.View_Jade', []);
        // add the cache path for this template
        $config['cachePath'] = $cachepath;
        // create a renderer instance
        return new Tale\Renderer($config);
    }
    /**
     * @InheritDoc
     */
    protected function process_file($file_override = false)
    {
        // determine the filename
        $file = $file_override ?: $this->file_name;
        // render the template using the Everzet implementation
        if (class_exists('Everzet\Jade\Jade')) {
            // render the template
            $file = $this->everzet_parser($this->cache_init($file))->cache($file);
            $result = parent::process_file($file);
        } elseif (class_exists('Tale\Jade\Renderer')) {
            // render the template
            $result = $this->tale_parser($this->cache_init($file))->render($file, $data = $this->get_data());
            // disable sanitization on objects that support it
            $this->unsanitize($data);
        } else {
            throw new \Fuel_Exception('No supported Jade renderer found. Please check the documentation');
        }
        return $result;
    }
    // Jade stores cached templates as the filename in plain text,
    // so there is a high chance of name collisions (ex: index.jade).
    // This function attempts to create a unique directory for each
    // compiled template.
    protected function cache_init($file_path)
    {
        $cache_dir = \Config::get('parser.View_Jade.cache_dir', null);
        if ($cache_dir === null) {
            throw new \Fuel_Exception('parser.View_Jade.cache_dir must be configured');
        }
        $cache_key = md5((string) $file_path);
        $cache_path = $cache_dir . substr($cache_key, 0, 2) . DS . substr($cache_key, 2, 2);
        if (!is_dir($cache_path)) {
            mkdir($cache_path, 0755, true);
        }
        return $cache_path;
    }
}