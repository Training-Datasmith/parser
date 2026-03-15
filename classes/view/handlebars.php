<?php

declare(strict_types=1);
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

use LightnCandy\LightnCandy;

class View_Handlebars extends \View
{
    // default extension used by this template engine
    public $extension = 'hbs';

    protected function process_file($file_override = false)
    {
        // get the template filename
        $file = $file_override ?: $this->file_name;

        // compiled template path
        $path = rtrim(\Config::get('parser.View_Handlebars.compile_dir', APPPATH.'tmp'.DS.'handlebars'), DS).DS;

        // construct the compiled filename
        $compiled = md5($file);
        $compiled = $path.substr($compiled, 0, 1).DS.substr($compiled, 1, 1).DS.substr($compiled, 2).'.'.$this->extension;

        // do we need to compile?
        if (! is_file($compiled) or filemtime($file) > filemtime($compiled) or \Config::get('parser.View_Handlebars.force_compile', true)) {
            // make sure the directory exists
            if (! is_dir($compiled_path = dirname($compiled))) {
                \File::create_dir(APPPATH, substr($compiled_path, strlen(APPPATH)));
            }

            // write the compiled code atomically to prevent race conditions on concurrent requests
            $ext = $this->extension;
            file_put_contents($compiled, '<?php ' . LightnCandy::compile(
                file_get_contents($file),
                [
                    'partialresolver' => function ($cx, $name) use ($ext): string|false {
                        $partial = \Finder::search('views', $name, '.'.$ext, false, false);
                        return empty($partial) ? "[ PARTIAL $name NOT FOUND!]" : file_get_contents($partial);
                    },
                ] + \Config::get('parser.View_Handlebars.environment', [])
            ), LOCK_EX);
        }

        // fetch the compiled template and render it
        try {
            $data = $this->get_data();
            $renderer = include($compiled);
            if (! is_callable($renderer)) {
                throw new \FuelException('Compiled Handlebars template did not return a callable: '.$compiled);
            }
            $result = $renderer($data);
        } catch (\Exception $e) {
            // Delete the output buffer & re-throw the exception
            ob_end_clean();
            throw $e;
        }

        $this->unsanitize($data);
        return $result;
    }
}
