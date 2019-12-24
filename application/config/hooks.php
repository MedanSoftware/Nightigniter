<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/**
 * -------------------------------------------------------------------------
 * Pre System
 * -------------------------------------------------------------------------
 *
 * Called very early during system execution.
 * Only the benchmark and hooks class have been loaded at this point.
 * No routing or other processes have happened.
 */
$hook['pre_system'] = array(
	array(
		'class'    => 'Application',
		'function' => 'htaccess',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'config_files',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'site_path',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'assets_path',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'ssl_cert_path',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'site_data_folder',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'site_url',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'environment',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	)
);

/**
 * -------------------------------------------------------------------------
 * Pre Controller
 * -------------------------------------------------------------------------
 *
 * Called immediately prior to any of your controllers being called.
 * All base classes, routing, and security checks have been done.
 */
$hook['pre_controller'] = array(
	array(
		'class'    => 'Application',
		'function' => 'language',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	),
	array(
		'class'    => 'Application',
		'function' => 'themes',
		'filename' => 'booting/Application.php',
		'filepath' => 'hooks'
	)
);

/**
 * -------------------------------------------------------------------------
 * Post Controller Constructor
 * -------------------------------------------------------------------------
 *
 * Called immediately after your controller is instantiated, but prior to any method calls happening.
 */
$hook['post_controller_constructor'] = NULL;

/**
 * -------------------------------------------------------------------------
 * Post Controller
 * -------------------------------------------------------------------------
 *
 * Called immediately after your controller is fully executed.
 */
$hook['post_controller'] = NULL;

/**
 * -------------------------------------------------------------------------
 * Display Override
 * -------------------------------------------------------------------------
 *
 * Overrides the _display() method, used to send the finalized page to the web browser at the end of system execution.
 * This permits you to use your own display methodology.
 * Note that you will need to reference the CI superobject with $this->CI =& get_instance() and then the finalized data will be available by calling
 * $this->CI->output->get_output()
 */
$hook['display_override'] = NULL;

/**
 * -------------------------------------------------------------------------
 * Cache Override
 * -------------------------------------------------------------------------
 *
 * Enables you to call your own method instead of the _display_cache() method in the Output Library.
 * This permits you to use your own cache display mechanism.
 */
$hook['cache_override'] = NULL;

/**
 * -------------------------------------------------------------------------
 * Post System
 * -------------------------------------------------------------------------
 *
 * Called after the final rendered page is sent to the browser, at the end of system execution after the finalized data is sent to the browser.
 */
$hook['post_system'] = NULL;