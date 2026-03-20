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

class View_Phptal extends \View
{
    protected static $_parser;
    protected function process_file($file_override = false)
    {
        $file = $file_override ?: $this->file_name;
        try {
            $parser = static::parser();
            $data = $this->get_data();
            foreach ($data as $key => $value) {
                $parser->set($key, $value);
            }
            $parser->set_template($file);
            $result = $parser->execute();
        } catch (\Exception $e) {
            // Delete the output buffer & re-throw the exception
            ob_end_clean();
            throw $e;
        }
        $this->unsanitize($data);
        return $result;
    }
    public $extension = 'phptal';
    public static function parser()
    {
        if (!empty(static::$_parser)) {
            return static::$_parser;
        }
        static::$_parser = new \PHPTAL();
        static::$_parser->set_encoding(\Config::get('parser.View_Phptal.encoding', 'UTF-8'));
        $allowed_output_modes = ['PHPTAL::XHTML', 'PHPTAL::HTML5', 'PHPTAL::XML'];
        $output_mode = \Config::get('parser.View_Phptal.output_mode', 'PHPTAL::XHTML');
        if (!in_array($output_mode, $allowed_output_modes, true)) {
            throw new \Fuel_Exception('Invalid PHPTAL output_mode: ' . $output_mode);
        }
        static::$_parser->set_output_mode(constant('\\' . $output_mode));
        static::$_parser->set_template_repository(\Config::get('parser.View_Phptal.template_repository', ''));
        static::$_parser->set_php_code_destination(\Config::get('parser.View_Phptal.cache_dir', APPPATH . 'cache' . DS . 'PHPTAL' . DS));
        static::$_parser->set_cache_lifetime(\Config::get('parser.View_Phptal.cache_lifetime', 0));
        static::$_parser->set_force_reparse(\Config::get('parser.View_Phptal.force_reparse', false));
        return static::$_parser;
    }
}
// end of file phptal.php