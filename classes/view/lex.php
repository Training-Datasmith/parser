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

class View_Lex extends \View
{
    protected static $_parser;
    public static function parser()
    {
        if (!empty(static::$_parser)) {
            return static::$_parser;
        }
        static::$_parser = new \Lex\Parser();
        return static::$_parser;
    }
    public static function inject_noparse($template): void
    {
        \Lex\Parser::inject_noparse($template);
    }
    public $extension = 'lex';
    protected $callback = false;
    public function set_callback($callback = false)
    {
        $this->callback = $callback;
        return $this;
    }
    protected function process_file($file_override = false)
    {
        $file = $file_override ?: $this->file_name;
        try {
            $data = $this->get_data();
            static::parser()->scope_glue(\Config::get('parser.View_Lex.scope_glue', '.'));
            $contents = file_get_contents($file);
            if ($contents === false) {
                throw new \Fuel_Exception('Could not read Lex template file: ' . $file);
            }
            $result = static::parser()->parse($contents, $data, $this->callback, \Config::get('parser.View_Lex.allow_php', false));
        } catch (\Exception $e) {
            // Delete the output buffer & re-throw the exception
            ob_end_clean();
            throw $e;
        }
        $this->unsanitize($data);
        return $result;
    }
}