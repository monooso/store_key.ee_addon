<?php if ( ! defined('BASEPATH')) exit('Direct script access not allowed');

/**
 * Store Key module.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

class Store_key {

  private $EE;
  private $_model;

  public $return_data = '';


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
  }


  /* --------------------------------------------------------------
   * TEMPLATE TAGS
   * ------------------------------------------------------------ */

  /**
   * Outputs the licenses associated with the given order.
   *
   * @access  public
   * @return  string
   */
  public function licenses()
  {
    return $this->return_data = 'exp:store_key:licenses output';
  }


}


/* End of file      : mod.store_key.php */
/* File location    : third_party/store_key/mod.store_key.php */
