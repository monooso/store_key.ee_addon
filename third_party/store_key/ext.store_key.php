<?php if ( ! defined('BASEPATH')) exit('Direct script access not allowed');

/**
 * Store Key extension.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

class Store_key_ext {

  private $EE;
  private $_model;

  public $description;
  public $docs_url;
  public $name;
  public $settings;
  public $settings_exist;
  public $version;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   mixed     $settings     Extension settings.
   * @return  void
   */
  public function __construct($settings = '')
  {
    $this->EE =& get_instance();

    $this->EE->load->add_package_path(PATH_THIRD .'store_key/');

    // Still need to specify the package...
    $this->EE->lang->loadfile('store_key_ext', 'store_key');

    $this->EE->load->model('store_key_model');
    $this->_model = $this->EE->store_key_model;

    // Set the public properties.
    $this->description = $this->EE->lang->line(
      'store_key_extension_description');

    $this->docs_url = 'http://experienceinternet.co.uk/';
    $this->name     = $this->EE->lang->line('store_key_extension_name');
    $this->settings = $settings;
    $this->settings_exist = 'n';
    $this->version  = $this->_model->get_package_version();
  }


  /**
   * Activates the extension.
   *
   * @access  public
   * @return  void
   */
  public function activate_extension()
  {
    $hooks = array('store_order_complete_end');
    $this->_model->install_extension($this->version, $hooks);
  }


  /**
   * Disables the extension.
   *
   * @access  public
   * @return  void
   */
  public function disable_extension()
  {
    $this->_model->uninstall_extension();
  }


  /**
   * Handles the store_order_complete_end extension hook.
   *
   * @access  public
   * @param   ??      $order    Order details.
   * @return  void
   */
  public function on_store_order_complete_end($order)
  {
    if (($last_call = $this->EE->extensions->last_call) !== FALSE)
    {
      // Retrieve last call data.
    }

    error_log('Handling the store_order_complete_end extension hook.');
    error_log('Order information: ' .print_r($order, TRUE));
  }


  /**
   * Updates the extension.
   *
   * @access  public
   * @param   string    $installed_version    The installed version.
   * @return  mixed
   */
  public function update_extension($installed_version = '')
  {
    return $this->_model->update_package($installed_version);
  }


}


/* End of file      : ext.store_key.php */
/* File location    : third_party/store_key/ext.store_key.php */
