<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * Store Key module installer and updater.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

class Store_key_upd {

  private $EE;
  private $_model;

  public $version;


  /* --------------------------------------------------------------
  * PUBLIC METHODS
  * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function __construct()
  {
    $this->EE =& get_instance();

    $this->EE->load->add_package_path(PATH_THIRD .'store_key/');

    $this->EE->load->model('store_key_model');
    $this->_model = $this->EE->store_key_model;

    $this->version = $this->_model->get_package_version();
  }


  /**
   * Installs the module.
   *
   * @access  public
   * @return  bool
   */
  public function install()
  {
    return $this->_model->install_module($this->version);
  }


  /**
   * Uninstalls the module.
   *
   * @access  public
   * @return  bool
   */
  public function uninstall()
  {
    return $this->_model->uninstall_module();
  }


  /**
   * Updates the module.
   *
   * @access  public
   * @param   string      $installed_version      The installed version.
   * @return  bool
   */
  public function update($installed_version = '')
  {
    return $this->_model->update_package($installed_version);
  }


}


/* End of file      : upd.store_key.php */
/* File location    : third_party/store_key/upd.store_key.php */
