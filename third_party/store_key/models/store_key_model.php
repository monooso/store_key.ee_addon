<?php if ( ! defined('BASEPATH')) exit('Direct script access not allowed');

/**
 * Store Key 'Package' model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

require_once dirname(__FILE__) .'/../config.php';

class Store_key_model extends CI_Model {

  protected $EE;
  protected $_namespace;
  protected $_package_name;
  protected $_package_title;
  protected $_package_version;
  protected $_sanitized_extension_class;
  protected $_sanitized_module_class;
  protected $_site_id;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   string    $package_name       Package name. Used for testing.
   * @param   string    $package_title      Package title. Used for testing.
   * @param   string    $package_version    Package version. Used for testing.
   * @param   string    $namespace          Session namespace. Used for testing.
   * @return  void
   */
  public function __construct($package_name = '', $package_title = '',
    $package_version = '', $namespace = ''
  )
  {
    parent::__construct();

    $this->EE =& get_instance();

    // Load the OmniLogger class.
    if (file_exists(PATH_THIRD .'omnilog/classes/omnilogger.php'))
    {
      include_once PATH_THIRD .'omnilog/classes/omnilogger.php';
    }

    $this->_namespace = $namespace ? strtolower($namespace) : 'experience';

    $this->_package_name = $package_name
      ? $package_name : STORE_KEY_NAME;

    $this->_package_title = $package_title
      ? $package_title : STORE_KEY_TITLE;

    $this->_package_version = $package_version
      ? $package_version : STORE_KEY_VERSION;

    // ExpressionEngine is very picky about capitalisation.
    $this->_sanitized_module_class = ucfirst(strtolower($this->_package_name));
    $this->_sanitized_extension_class = $this->_sanitized_module_class .'_ext';

    // Initialise the add-on cache.
    if ( ! array_key_exists($this->_namespace, $this->EE->session->cache))
    {
      $this->EE->session->cache[$this->_namespace] = array();
    }

    if ( ! array_key_exists($this->_package_name,
      $this->EE->session->cache[$this->_namespace]))
    {
      $this->EE->session->cache[$this->_namespace]
        [$this->_package_name] = array();
    }
  }



  /* --------------------------------------------------------------
   * PUBLIC PACKAGE METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Returns the package name.
   *
   * @access  public
   * @return  string
   */
  public function get_package_name()
  {
    return $this->_package_name;
  }


  /**
   * Returns the package theme URL.
   *
   * @access  public
   * @return  string
   */
  public function get_package_theme_url()
  {
    // Much easier as of EE 2.4.0.
    if (defined('URL_THIRD_THEMES'))
    {
      return URL_THIRD_THEMES .$this->get_package_name() .'/';
    }

    return $this->EE->config->slash_item('theme_folder_url')
      .'third_party/' .$this->get_package_name() .'/';
  }


  /**
   * Returns the package title.
   *
   * @access  public
   * @return  string
   */
  public function get_package_title()
  {
    return $this->_package_title;
  }


  /**
   * Returns the package version.
   *
   * @access  public
   * @return  string
   */
  public function get_package_version()
  {
    return $this->_package_version;
  }


  /**
   * Returns the site ID.
   *
   * @access  public
   * @return  int
   */
  public function get_site_id()
  {
    if ( ! $this->_site_id)
    {
      $this->_site_id = (int) $this->EE->config->item('site_id');
    }

    return $this->_site_id;
  }


  /**
   * Logs a message to OmniLog.
   *
   * @access  public
   * @param   string      $message        The log entry message.
   * @param   int         $severity       The log entry 'level'.
   * @return  void
   */
  public function log_message($message, $severity = 1)
  {
    if (class_exists('Omnilog_entry') && class_exists('Omnilogger'))
    {
      switch ($severity)
      {
        case 3:
          $notify = TRUE;
          $type   = Omnilog_entry::ERROR;
          break;

        case 2:
          $notify = FALSE;
          $type   = Omnilog_entry::WARNING;
          break;

        case 1:
        default:
          $notify = FALSE;
          $type   = Omnilog_entry::NOTICE;
          break;
      }

      $omnilog_entry = new Omnilog_entry(array(
        'addon_name'    => 'Store_key',
        'date'          => time(),
        'message'       => $message,
        'notify_admin'  => $notify,
        'type'          => $type
      ));

      Omnilogger::log($omnilog_entry);
    }
  }


  /**
   * Updates a 'base' array with data contained in an 'update' array. Both
   * arrays are assumed to be associative.
   *
   * - Elements that exist in both the base array and the update array are
   *   updated to use the 'update' data.
   * - Elements that exist in the update array but not the base array are
   *   ignored.
   * - Elements that exist in the base array but not the update array are
   *   preserved.
   *
   * @access public
   * @param  array  $base   The 'base' array.
   * @param  array  $update The 'update' array.
   * @return array
   */
  public function update_array_from_input(Array $base, Array $update)
  {
    return array_merge($base, array_intersect_key($update, $base));
  }


  /**
   * Updates the package. Called from the 'update' methods of any package 
   * add-ons (module, extension, etc.), to ensure that everything gets updated 
   * at the same time.
   *
   * @access  public
   * @param   string    $installed_version    The installed version.
   * @return  bool
   */
  public function update_package($installed_version = '')
  {
    // Can't do anything without valid data.
    if ( ! is_string($installed_version) OR $installed_version == '')
    {
      return FALSE;
    }

    $package_version = $this->get_package_version();

    // Up to date?
    if (version_compare($installed_version, $package_version, '>='))
    {
      return FALSE;
    }

    // Update the extension version number in the database.
    $this->EE->db->update('extensions', array('version' => $package_version),
      array('class' => $this->get_sanitized_extension_class()));

    /**
     * Update the module version number in the database. EE takes care of this 
     * if the module is being updated from the Modules page, but not if this 
     * update has been triggered from the Extensions page. Package updates in EE 
     * are a mess, basically.
     */

    $this->EE->db->update('modules',
      array('module_version' => $package_version),
      array('module_name'    => $this->get_sanitized_module_class()));

    return TRUE;
  }


  /* --------------------------------------------------------------
   * PUBLIC EXTENSION METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Returns the correctly-capitalised 'extension' class.
   *
   * @access  public
   * @return  string
   */
  public function get_sanitized_extension_class()
  {
    return $this->_sanitized_extension_class;
  }


  /**
   * Installs the extension.
   *
   * @access  public
   * @param   string    $version    The extension version.
   * @param   array     $hooks      The extension hooks.
   * @return  void
   */
  public function install_extension($version, Array $hooks)
  {
    // Guard against nonsense.
    if ( ! is_string($version) OR $version == '' OR ! $hooks)
    {
      return;
    }

    $class = $this->get_sanitized_extension_class();

    $default_hook_data = array(
      'class'     => $class,
      'enabled'   => 'y',
      'hook'      => '',
      'method'    => '',
      'priority'  => '5',
      'settings'  => '',
      'version'   => $version
    );

    foreach ($hooks AS $hook)
    {
      if ( ! is_string($hook) OR $hook == '')
      {
        continue;
      }

      $this->EE->db->insert('extensions', array_merge(
        $default_hook_data, array('hook' => $hook, 'method' => 'on_' .$hook)));
    }
  }


  /**
   * Uninstalls the extension.
   *
   * @access    public
   * @return    void
   */
  public function uninstall_extension()
  {
    $this->EE->db->delete('extensions',
      array('class' => $this->get_sanitized_extension_class()));
  }


  /* --------------------------------------------------------------
   * PUBLIC MODULE METHODS
   * ------------------------------------------------------------ */

  /**
   * Returns the correctly-capitalised 'module' class.
   *
   * @access  public
   * @return  string
   */
  public function get_sanitized_module_class()
  {
    return $this->_sanitized_module_class;
  }


  /**
   * Installs the module.
   *
   * @access  public
   * @param   string    $package_version  The package version.
   * @return  bool
   */
  public function install_module($package_version)
  {
    if ( ! is_string($package_version) OR $package_version == '')
    {
      return FALSE;
    }

    $mod_class = $this->get_sanitized_module_class();

    $this->_register_module($mod_class, $package_version);
    $this->_create_module_tables();

    return TRUE;
  }


  /**
   * Uninstalls the module.
   *
   * @access  public
   * @return  bool
   */
  public function uninstall_module()
  {
    $mod_class = $this->get_sanitized_module_class();

    $db_module = $this->EE->db
      ->select('module_id')
      ->get_where('modules', array('module_name' => $mod_class), 1);

    if ($db_module->num_rows() !== 1)
    {
      return FALSE;
    }

    $this->EE->db->delete('module_member_groups',
      array('module_id' => $db_module->row()->module_id));

    $this->EE->db->delete('modules', array('module_name' => $mod_class));
    
    // Drop the module tables.
    $this->EE->load->dbforge();
    $this->EE->dbforge->drop_table('store_key_license_keys');

    return TRUE;
  }

  
  /* --------------------------------------------------------------
   * PUBLIC ADD-ON SPECIFIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Generates a unique license key.
   *
   * @access  public
   * @param   int|string  $order_item_id      The order item ID.
   * @param   int         $order_item_index   If item_qty is 2, the first item 
   *                                          has an index of 1, the second 2.
   * @return  string
   */
  public function generate_license_key($order_item_id, $order_item_index)
  {
    if ( ! valid_int($order_item_id, 1)
      OR ! valid_int($order_item_index, 1)
    )
    {
      return '';
    }

    /**
     * Probably overkill, but better to be safe. First off, we create an MD5 
     * hash of the concatenated Order Item ID, Order Item Index, and time.
     *
     * Then we pad the Order Item ID to 10 characters, and the Order Item Index 
     * to 4 characters, both with random hexadecimal characters.
     *
     * Finally, we concatenate the MD5 hash and the padding strings, resulting 
     * in a unique 46-character string.
     */

    $order_item_id    = (string) $order_item_id;
    $order_item_index = (string) $order_item_index;

    $base_hash = md5($order_item_id .$order_item_index .time());
    $pad_id    = str_pad($order_item_id, 10, $base_hash, STR_PAD_LEFT);
    $pad_index = str_pad($order_item_index, 4, $base_hash, STR_PAD_LEFT);

    return $base_hash .$pad_id .$pad_index;
  }


  /**
   * Saves the given order item license key to the database.
   *
   * @access  public
   * @param   int|string    $order_item_id    The order item ID.
   * @param   string    $license_key    The license key.
   * @return  void
   */
  public function save_license_key($order_item_id, $license_key)
  {
    // Pretty lenient with the license key.
    if ( ! valid_int($order_item_id, 1)
      OR ! is_string($license_key)
      OR $license_key == ''
    )
    {
      return;
    }

    $this->EE->db->insert(
      array(
        'license_key'   => $license_key,
        'order_item_id' => $order_item_id
      ),
      'exp_store_key_license_keys'
    );
  }
  


  /* --------------------------------------------------------------
   * PROTECTED PACKAGE METHODS
   * ------------------------------------------------------------ */

  /**
   * Returns a references to the package cache. Should be called
   * as follows: $cache =& $this->_get_package_cache();
   *
   * @access  protected
   * @return  array
   */
  protected function &_get_package_cache()
  {
    return $this->EE->session->cache[$this->_namespace][$this->_package_name];
  }


  /* --------------------------------------------------------------
   * PROTECTED MODULE METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Creates the module database tables.
   *
   * @access  protected
   * @return  void
   */
  protected function _create_module_tables()
  {
    $this->EE->load->dbforge();

    // License keys table.
    $schema = array(
      'license_key_id' => array(
        'auto_increment'  => TRUE,
        'constraint'      => 10,
        'type'            => 'INT',
        'unsigned'        => TRUE
      ),
      'order_item_id' => array(
        'constraint'  => 10,
        'null'        => FALSE,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'license_key' => array(
        'constraint' => 128,
        'null'       => FALSE,
        'type'       => 'VARCHAR'
      )
    );

    // Should ideally have foreign key for order_item_id.
    $this->EE->dbforge->add_field($schema);
    $this->EE->dbforge->add_key('license_key_id', TRUE);
    $this->EE->dbforge->create_table('store_key_license_keys', TRUE);
  }


  /**
   * Registers the module in the database.
   *
   * @access  protected
   * @param   string    $module_class     The module class.
   * @param   string    $package_version  The package version.
   * @return  void
   */
  protected function _register_module($module_class, $package_version)
  {
    $this->EE->db->insert('modules', array(
      'has_cp_backend'      => 'n',
      'has_publish_fields'  => 'n',
      'module_name'         => $module_class,
      'module_version'      => $package_version
    ));
  }


}


/* End of file      : store_key_model.php */
/* File location    : third_party/store_key/models/store_key_model.php */
