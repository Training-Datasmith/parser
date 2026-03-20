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

use Haml_Parser;
class View_Haml extends \View
{
    protected static $_parser;
    protected static $_cache;
    protected function process_file($file_override = false)
    {
        $file = $file_override ?: $this->file_name;
        $this->cache_init($file);
        $file = static::parser()->parse($file, static::$_cache);
        return parent::process_file($file);
    }
    public $extension = 'haml';
    /**
     * Returns the Parser lib object
     *
     * @return  HamlParser
     */
    public static function parser()
    {
        if (!empty(static::$_parser)) {
            return static::$_parser;
        }
        static::$_parser = new Haml_Parser();
        return static::$_parser;
    }
    // Haml stores cached templates as the filename in plain text,
    // so there is a high chance of name collisions (ex: index.haml).
    // This function attempts to create a unique directory for each
    // compiled template.
    public function cache_init($file_path): void
    {
        $cache_dir = \Config::get('parser.View_Haml.cache_dir', null);
        if ($cache_dir === null) {
            throw new \Fuel_Exception('parser.View_Haml.cache_dir must be configured');
        }
        $cache_key = md5((string) $file_path);
        $cache_path = $cache_dir . substr($cache_key, 0, 2) . DS . substr($cache_key, 2, 2);
        if (!is_dir($cache_path)) {
            mkdir($cache_path, 0755, true);
        }
        static::$_cache = $cache_path;
    }
}
/* end of file haml.php */