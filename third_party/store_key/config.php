<?php

/**
 * Store Key NSM Add-on Updater information.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 * @version         0.1.0
 */

if ( ! defined('STORE_KEY_NAME'))
{
  define('STORE_KEY_NAME', 'Store_key');
  define('STORE_KEY_TITLE', 'Store Key');
  define('STORE_KEY_VERSION', '0.1.0');
}

$config['name']     = STORE_KEY_NAME;
$config['version']  = STORE_KEY_VERSION;
$config['nsm_addon_updater']['versions_xml']
  = 'http://experienceinternet.co.uk/software/feeds/store-key';

/* End of file      : config.php */
/* File location    : third_party/store_key/config.php */