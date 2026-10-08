<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010-2026 Fuel Development Team
 * @link       https://fuelphp.com
 */

namespace Parser;

/**
 * Lex parser wrapper for PHP 8.2+ dynamic property compatibility.
 */
#[\AllowDynamicProperties]
class Lex_Parser extends \Lex\Parser
{
}
