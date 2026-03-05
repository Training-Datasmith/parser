<?php
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

use Asset;
use Auth;
use Config;
use Form;
use Html;
use Input;
use Lang;
use Markdown;
use Router;
use Session;
use Uri;

/**
 * Provides Smarty support for commonly used FuelPHP classes and methods.
 */
class Smarty_Fuel_Extension
{
	/**
	 * Sets up all of the functions this extension makes available.
	 */
	public function __construct(\Smarty $smarty)
	{
		$smarty->registerPlugin('function', 'fuel_version', $this->fuel_version(...));
		$smarty->registerPlugin('function', 'url', $this->url(...));
		$smarty->registerPlugin('function', 'base_url', ['Uri', 'base']);
		$smarty->registerPlugin('function', 'current_url', ['Uri', 'current']);
		$smarty->registerPlugin('function', 'uri_segment', $this->uri_segment(...));
		$smarty->registerPlugin('function', 'uri_segments', ['Uri', 'segments']);
		$smarty->registerPlugin('function', 'config', $this->config_get(...));
		$smarty->registerPlugin('function', 'lang', $this->lang_get(...));
		$smarty->registerPlugin('block', 'form', $this->form(...));
		$smarty->registerPlugin('function', 'form_input', $this->form_input(...));
		$smarty->registerPlugin('function', 'form_password', $this->form_password(...));
		$smarty->registerPlugin('function', 'form_hidden', $this->form_hidden(...));
		$smarty->registerPlugin('function', 'form_button', $this->form_button(...));
		$smarty->registerPlugin('function', 'form_reset', $this->form_reset(...));
		$smarty->registerPlugin('function', 'form_submit', $this->form_submit(...));
		$smarty->registerPlugin('function', 'form_textarea', $this->form_textarea(...));
		$smarty->registerPlugin('block', 'form_fieldset', $this->form_fieldset(...));
		$smarty->registerPlugin('function', 'form_label', $this->form_label(...));
		$smarty->registerPlugin('function', 'form_checkbox', $this->form_checkbox(...));
		$smarty->registerPlugin('function', 'form_radio', $this->form_radio(...));
		$smarty->registerPlugin('function', 'form_file', $this->form_file(...));
		$smarty->registerPlugin('function', 'form_select', $this->form_select(...));
		$smarty->registerPlugin('function', 'form_val', $this->form_val(...));
		$smarty->registerPlugin('function', 'input_get', $this->input_get(...));
		$smarty->registerPlugin('function', 'input_post', $this->input_post(...));
		$smarty->registerPlugin('function', 'asset_add_path', $this->asset_add_path(...));
		$smarty->registerPlugin('function', 'asset_css', $this->asset_css(...));
		$smarty->registerPlugin('function', 'asset_js', $this->asset_js(...));
		$smarty->registerPlugin('function', 'asset_img', $this->asset_img(...));
		$smarty->registerPlugin('function', 'asset_render', $this->asset_render(...));
		$smarty->registerPlugin('function', 'asset_find_file', $this->asset_find_file(...));
		$smarty->registerPlugin('function', 'html_anchor', $this->html_anchor(...));
		$smarty->registerPlugin('function', 'session_get_flash', $this->session_get_flash(...));
		$smarty->registerPlugin('block', 'markdown', $this->markdown_parse(...));
		$smarty->registerPlugin('function', 'auth_has_access', $this->auth_has_access(...));
		$smarty->registerPlugin('function', 'auth_check', $this->auth_check(...));
	}

	/**
	 * Return the current Fuel version
	 */
	public function fuel_version()
	{
		return \Fuel::VERSION;
	}

	/**
	 * Provides the url() functionality.  Generates a full url (including
	 * domain and index.php).
	 *
	 * Usage: {url uri='' params=[name=>$value]}
	 *
	 * @return  string
	 */
	public function url(array $params)
	{
		$uri = $params['uri'] ?? '';
		$named_params = $params['params'] ?? [];
		if ($named_uri = \Router::get($uri, $named_params))
		{
			$uri = $named_uri;
		}
		return \Uri::create($uri);
	}

	/**
	 * Usage: {uri_segment segment=''}
	 * Required: segment
	 *
	 * @return  mixed segment string or false
	 */
	public function uri_segment(array $params)
	{
		if (isset($params['segment']))
		{
			return \Uri::segment($params['segment']);
		}
		return false;
	}

	/**
	 * Usage: {config item='' default=''}
	 * Required: item
	 *
	 * @return  mixed string or array
	 */
	public function config_get(array $params)
	{
		if (isset($params['item']))
		{
			$default = $params['default'] ? null : $params['default'];
			return \Config::get($params['item'], $default);
		}
		return '';
	}

	/**
	 * Usage: {lang line='id' params=[] default='default value' lang='en'}
	 * Required: line
	 *
	 * @return  mixed string or false
	 */
	public function lang_get(array $params)
	{
		if (isset($params['line']))
		{
			$parameters = $params['params'] ?? [];
			$default = $params['default'] ?? null;
			$language = $params['lang'] ?? null;
			return \Lang::get($params['line'], $parameters, $default, $language);
		}
		return false;
	}

	/**
	 * Usage: {form attrs=[] hidden=[]}...{/form}
	 *
	 * @return  string
	 */
	public function form(array $params, string $content, $smarty, &$repeat)
	{
		//$content is null when repeat is true and has block content when repeat is false
		if ($repeat)
		{
			$attributes = $params['attrs'] ?? [];
			$hidden = $params['hidden'] ?? [];
			return \Form::open($attributes, $hidden);
		}
        return $content . \Form::close();
	}

	/**
	 * Usage: {form_fieldset attrs=[] legend=''}...{/form}
	 *
	 * @return  string
	 */
	public function form_fieldset(array $params, string $content, $smarty, &$repeat)
	{
		//$content is null when repeat is true and has block content when repeat is false
		if ($repeat)
		{
			$attributes = $params['attrs'] ?? [];
			$legend = $params['legend'] ?? null;
			return \Form::fieldset_open($attributes, $legend);
		}
        return $content . \Form::fieldset_close();
	}

	/**
	 * Usage: {form_input field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_input(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::input($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_password field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_password(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::password($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_hidden field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_hidden(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::hidden($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_button field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_button(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::button($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_submit field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_submit(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::submit($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_reset field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_reset(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::reset($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_textarea field='' value='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_textarea(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::textarea($params['field'], $value, $attributes);
	}

	/**
	 * Usage: {form_label text='' id='' attrs=[]}
	 * Required: text
	 *
	 * @return  string
	 */
	public function form_label(array $params)
	{
		if ( ! isset($params['text']))
		{
			throw new \UnexpectedValueException("The text parameter is required.");
		}
		$id = $params['id'] ?? null;
		$attributes = $params['attrs'] ?? [];
		return \Form::label($params['text'], $id, $attributes);
	}

	/**
	 * Usage: {form_checkbox field='' value='' checked=false attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_checkbox(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		$checked = $params['checked'] ?? null;
		return \Form::checkbox($params['field'], $value, $checked, $attributes);
	}

	/**
	 * Usage: {form_radio field='' value='' checked=false attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_radio(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$value = $params['value'] ?? null;
		$attributes = $params['attrs'] ?? [];
		$checked = $params['checked'] ?? null;
		return \Form::checkbox($params['field'], $value, $checked, $attributes);
	}

	/**
	 * Usage: {form_select field='' values='' options=[] attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_select(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$values = $params['values'] ?? null;
		$attributes = $params['attrs'] ?? [];
		$options = $params['options'] ?? [];
		return \Form::select($params['field'], $values, $options, $attributes);
	}

	/**
	 * Usage: {form_file field='' attrs=[]}
	 * Required: field
	 *
	 * @return  string
	 */
	public function form_file(array $params)
	{
		if ( ! isset($params['field']))
		{
			throw new \UnexpectedValueException("The field parameter is required.");
		}
		$attributes = $params['attrs'] ?? [];
		return \Form::file($params['field'], $attributes);
	}

	/**
	 * Provide access to Input::param
	 * Usage: {form_val index='' default=''}
	 *
	 * @return  string
	 */
	public function form_val(array $params)
	{
		$index = $params['index'] ?? null;
		$default = $params['default'] ?? null;
		return \Input::param($index, $default);
	}

	/**
	 * Provide access to Input::get
	 * Usage: {input_get index='' default=''}
	 *
	 * @return  string
	 */
	public function input_get(array $params)
	{
		$index = $params['index'] ?? null;
		$default = $params['default'] ?? null;
		return \Input::get($index, $default);
	}

	/**
	 * Provide access to Input::post
	 * Usage: {input_post index='' default=''}
	 *
	 * @return  string
	 */
	public function input_post(array $params)
	{
		$index = $params['index'] ?? null;
		$default = $params['default'] ?? null;
		return \Input::post($index, $default);
	}

	/**
	 * Provide addess to Asset::add_path
	 * Usage: {form_val path='' type=''}
	 * Required: path
	 *
	 */
	public function asset_add_path(array $params): void
	{
		if ( ! isset($params['path']))
		{
			throw new \UnexpectedValueException('Asset path must be specified');
		}
		$type = $params['type'] ?? null;
		\Asset::add_path($params['path'], $type);
	}

	/**
	 * Usage: {asset_css refs='' attrs=[] group='' raw=false}
	 * Required: refs
	 *
	 * @return mixed string or nothing if group is filled
	 */
	public function asset_css(array $params)
	{
		if ( ! isset($params['refs']))
		{
			throw new \UnexpectedValueException("The refs parameter is required.");
		}
		$group = $params['group'] ?? null;
		$attrs = $params['attrs'] ?? [];
		$raw = $params['raw'] ?? false;
		return \Asset::css($params['refs'], $attrs, $group, $raw);
	}

	/**
	 * Usage: {asset_js refs='' attrs=[] group='' raw=false}
	 * Required: refs
	 *
	 * @return mixed string or nothing if group is filled
	 */
	public function asset_js(array $params)
	{
		if ( ! isset($params['refs']))
		{
			throw new \UnexpectedValueException("The refs parameter is required.");
		}
		$group = $params['group'] ?? null;
		$attrs = $params['attrs'] ?? [];
		$raw = $params['raw'] ?? false;
		return \Asset::js($params['refs'], $attrs, $group, $raw);
	}

	/**
	 * Usage: {asset_img refs='' attrs=[] group=''}
	 * Required: refs
	 *
	 * @return mixed string or nothing if group is filled
	 */
	public function asset_img(array $params)
	{
		if ( ! isset($params['refs']))
		{
			throw new \UnexpectedValueException("The refs parameter is required.");
		}
		$group = $params['group'] ?? null;
		$attrs = $params['attrs'] ?? [];
		return \Asset::img($params['refs'], $attrs, $group);
	}

	/**
	 * Render a group of assets
	 * Usage: {asset_render group='' raw=false}
	 *
	 * @return string
	 */
	public function asset_render(array $params)
	{
		$group = $params['group'] ?? null;
		$raw = $params['raw'] ?? false;
		return \Asset::render($group, $raw);
	}

	/**
	 * Usage: {asset_find_file file='' type='' folder=''}
	 * Required: file and type
	 *
	 * @return string
	 */
	public function asset_find_file(array $params)
	{
		if ( ! isset($params['file']))
		{
			throw new \UnexpectedValueException("The file parameter is required.");
		}
		if ( ! isset($params['type']))
		{
			throw new \UnexpectedValueException("The type parameter is required.");
		}
		$folder = $params['folder'] ?? '';
		return \Asset::find_file($params['file'], $params['type'], $folder);
	}

	/**
	 * Usage: {html_anchor href='' text='' attrs='' secure=false}
	 * Required: href and text
	 *
	 * @return string
	 */
	public function html_anchor(array $params)
	{
		if ( ! isset($params['href']))
		{
			throw new \UnexpectedValueException("The href parameter is required.");
		}
		if ( ! isset($params['text']))
		{
			throw new \UnexpectedValueException("The text parameter is required.");
		}
		$attrs = $params['attrs'] ?? [];
		$secure = $params['secure'] ?? null;
		return \Html::anchor($params['href'], $params['text'], $attrs, $secure);
	}

	/**
	 * Usage: {session_get_flash var='' default='' expire=false}
	 * Required: var
	 *
	 * @return mixed
	 */
	public function session_get_flash(array $params)
	{
		if ( ! isset($params['var']))
		{
			throw new \UnexpectedValueException("The var parameter is required.");
		}
		$default = $params['default'] ?? null;
		$expire = $params['expire'] ?? false;
		return \Session::get_flash($params['var'], $default, $expire);
	}

	/**
	 * Usage: {markdown}...{/markdown}
	 *
	 * @return  string
	 */
	public function markdown_parse($params, $content, $smarty, &$repeat)
	{
		//only take action when repeat = false as that is the closing tag
		if (!$repeat)
		{
			return \Markdown::parse($content);
		}
	}

	/**
	 * Usage: {auth_has_access cond=''}
	 * Required: cond
	 *
	 * @return bool
	 */
	public function auth_has_access(array $params)
	{
		if ( ! isset($params['cond']))
		{
			throw new \UnexpectedValueException("The cond parameter is required.");
		}
		return \Auth::has_access($params['cond']);
	}

	/**
	 * Usage: {auth_check}
	 *
	 * @return bool
	 */
	public function auth_check()
	{
		return \Auth::check();
	}
}
