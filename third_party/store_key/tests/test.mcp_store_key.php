<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Store Key module control panel tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

require_once PATH_THIRD .'store_key/mcp.store_key.php';
require_once PATH_THIRD .'store_key/models/store_key_model.php';

class Test_store_key_mcp extends Testee_unit_test_case {

  private $_model;
  private $_subject;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function setUp()
  {
    parent::setUp();

    // Generate the mock model.
    Mock::generate('Store_key_model',
      get_class($this) .'_mock_model');

    /**
     * The subject loads the models using $this->EE->load->model().
     * Because the Loader class is mocked, that does nothing, so we
     * can just assign the mock models here.
     */

    $this->EE->store_key_model = $this->_get_mock('model');

    $this->_model   = $this->EE->store_key_model;
    $this->_subject = new Store_key_mcp();
  }


}


/* End of file      : test.mcp_store_key.php */
/* File location    : third_party/store_key/tests/test.mcp_store_key.php */
