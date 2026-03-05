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

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return [

    // ------------------------------------------------------------------------
    // Register extensions to their parsers, either classname or array config
    // ------------------------------------------------------------------------
    'extensions' => [
        'php'      => 'View',
        'twig'     => 'View_Twig',
        'mthaml'   =>  ['class' => 'View_HamlTwig', 'extension' => 'haml'],
        'mustache' => 'View_Mustache',
        'md'       => 'View_Markdown',
        'dwoo'     => ['class' => 'View_Dwoo', 'extension' => 'tpl'],
        'jade'     => 'View_Jade',
        'haml'     => 'View_Haml',
        'smarty'   => 'View_Smarty',
        'phptal'   => 'View_Phptal',
        'lex'      => 'View_Lex',
    ],

    // ------------------------------------------------------------------------
    // Individual class config by classname
    // ------------------------------------------------------------------------

    // MARKDOWN ( http://michelf.com/projects/php-markdown/ )
    // ------------------------------------------------------------------------
    'View_Markdown' => [
        'auto_encode' => true,
        'allow_php'   => true,
    ],

    // TWIG ( http://www.twig-project.org/documentation )
    // ------------------------------------------------------------------------
    'View_Twig' => [
        'auto_encode' => true,
        'views_paths' => [APPPATH.'views'],
        'delimiters'  => [
            'tag_block'    => ['left' => '{%', 'right' => '%}'],
            'tag_comment'  => ['left' => '{#', 'right' => '#}'],
            'tag_variable' => ['left' => '{{', 'right' => '}}'],
        ],
        'environment' => [
            'debug'               => false,
            'charset'             => 'utf-8',
            'base_template_class' => 'Twig_Template',
            'cache'               => APPPATH.'cache'.DS.'twig'.DS,
            'auto_reload'         => true,
            'strict_variables'    => false,
            'autoescape'          => false,
            'optimizations'       => -1,
        ],
        'extensions' => [
            'Twig_Fuel_Extension',
        ],
    ],

    // HamlTwig with MtHaml https://github.com/arnaud-lb/MtHaml
    // Twig configuration is grabbed from 'View_Twig' config key
    // Packagist url: https://packagist.org/packages/mthaml/mthaml
    // Uses > 1.1.1 (Master branch ATM)
    // ------------------------------------------------------------------------
    'View_HamlTwig' => [
        //'include'   => APPPATH.'vendor'.DS.'MtHaml'.DS.'Autoloader.php',
        'auto_encode' => true,
        'environment' => [
            'auto_escaper' => true,
            'escape_html'  => true,
            'escape_attrs' => true,
            'charset'      => 'UTF-8',
            'format'       => 'html5',
        ],
    ],

    // DWOO ( http://wiki.dwoo.org/ )
    // ------------------------------------------------------------------------
    'View_Dwoo' => [
        'include'     => APPPATH.'vendor'.DS.'Dwoo'.DS.'dwooAutoload.php',
        'auto_encode' => true,
        'delimiters'  => ['left' => '{{', 'right' => '}}'],
        'environment' => [
            'autoescape'      => false,
            'nested_comments' => false,
            'allow_spaces'    => false,
            'cache_dir'       => APPPATH.'cache'.DS.'dwoo'.DS,
            'compile_dir'     => APPPATH.'cache'.DS.'dwoo'.DS.'compiled'.DS,
            'cache_time'      => 0,

            // Set what parser should do with PHP tags
            // 1 - Encode tags | 2 - Remove tags | 3 - Allow tags
            'allow_php_tags' => 2,

            // Which PHP functions should be accessible through Parser
            'allow_php_func' => [],
        ],
    ],

    // MUSTACHE ( https://github.com/bobthecow/mustache.php )
    // ------------------------------------------------------------------------
    'View_Mustache' => [
        'auto_encode' => true,
        'environment' => [
            'cache_dir' => APPPATH.'cache'.DS.'mustache'.DS,
            'partials'  => [],
            'helpers'   => [],
            'charset'   => 'UTF-8',
        ],
    ],

    // JADE PHP ( https://github.com/everzet/jade.php )
    // See notes in /parser/classes/view/jade.php
    // ------------------------------------------------------------------------
    'View_Jade' => [
        // global config
        'cache_dir'   => APPPATH.'cache'.DS.'jade'.DS,

        // Everzet config
        'include'     => APPPATH.'vendor'.DS.'Jade'.DS.'autoload.php.dist',
        'auto_encode' => true,

        // Tale config
        'lifetime'    => 3600,
        'pretty'      => false,
    ],

    // HAML / PHAMLP ( http://code.google.com/p/phamlp/ )
    // ------------------------------------------------------------------------
    'View_Haml' => [
        'include'     => APPPATH.'vendor'.DS.'Phamlp'.DS.'haml'.DS.'HamlParser.php',
        'auto_encode' => true,
        'cache_dir'   => APPPATH.'cache'.DS.'haml'.DS,
    ],

    // SMARTY ( http://www.smarty.net/documentation )
    // ------------------------------------------------------------------------
    'View_Smarty' => [
        'auto_encode' => true,
        'delimiters'  => ['left' => '{', 'right' => '}'],
        'environment' => [
            'compile_dir'       => APPPATH.'tmp'.DS.'Smarty'.DS.'templates_c'.DS,
            'config_dir'        => APPPATH.'tmp'.DS.'Smarty'.DS.'configs'.DS,
            'cache_dir'         => APPPATH.'cache'.DS.'Smarty'.DS,
            'plugins_dir'       => [],
            'caching'           => false,
            'cache_lifetime'    => 0,
            'force_compile'     => false,
            'compile_check'     => true,
            'debugging'         => false,
            'autoload_filters'  => [],
            'default_modifiers' => [],
        ],
        'extensions' => [
            'Smarty_Fuel_Extension',
        ],
    ],

    // PHPTAL ( http://phptal.org/manual/en/ )
    // ------------------------------------------------------------------------
    'View_Phptal' => [
        'include'             => APPPATH.'vendor'.DS.'PHPTAL'.DS.'PHPTAL.php',
        'auto_encode'         => true,
        'cache_dir'           => APPPATH.'cache'.DS.'PHPTAL'.DS,
        'cache_lifetime'      => 0,
        'encoding'            => 'UTF-8',
        'output_mode'         => 'PHPTAL::XHTML',
        'template_repository' => '',
        'force_reparse'       => false,
    ],

    // LEX ( http://github.com/pyrocms/lex/ )
    // Packagist url: https://packagist.org/packages/pyrocms/lex
    // ------------------------------------------------------------------------
    'View_Lex' => [
        'scope_glue' => '.',
        'allow_php'  => false,
    ],

    // HANDLEBARS ( https://github.com/zordius/lightncandy )
    // Packagist url: https://packagist.org/packages/zordius/lightncandy
    // ------------------------------------------------------------------------
    'View_Handlebars' => [
        'force_compile'   => true,
        'compile_dir'     => APPPATH.'tmp'.DS.'handlebars'.DS,
        'environment'     => [
            'flags'           => class_exists('LightnCandy\LightnCandy') ? LightnCandy\LightnCandy::FLAG_ERROR_EXCEPTION | LightnCandy\LightnCandy::FLAG_ELSE | LightnCandy\LightnCandy::FLAG_HBESCAPE | LightnCandy\LightnCandy::FLAG_JS : 0,
            'helpers'         => [],
            'helperresolver'  => function ($cx, $name) {
                return;
            },
        ],
    ],

];
