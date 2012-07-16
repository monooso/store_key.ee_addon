<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Store Key module 'update' tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

require_once PATH_THIRD .'store_key/upd.store_key.php';
require_once PATH_THIRD .'store_key/models/store_key_model.php';

class Test_store_key_upd extends Testee_unit_test_case {

  private $_model;
  private $_pkg_name;
  private $_pkg_version;
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
    $this->_model = $this->EE->store_key_model;

    // Common return value.
    $this->_pkg_version = '1.0.0';
    $this->_model->setReturnValue('get_package_version', $this->_pkg_version);

    // Create the test subject.
    $this->_subject = new Store_key_upd();
  }


  public function test__install__calls_model_method_and_honors_return_value()
  {
    $return = 'wibble';     // Can be anything.

    $this->_model->expectOnce('install_module', array($this->_pkg_version));
    $this->_model->setReturnValue('install_module', $return);
    $this->assertIdentical($return, $this->_subject->install());
  }


  public function test__uninstall__calls_model_method_and_honors_return_value()
  {
    $return = 'Diolch';     // You're welcome.

    $this->_model->expectOnce('uninstall_module');
    $this->_model->setReturnValue('uninstall_module', $return);

    $this->assertIdentical($return, $this->_subject->uninstall());
  }


  public function test__update__calls_model_method_and_honors_return_value()
  {
    $installed  = '0.8.5';
    $return     = 'Shw mae?';   // Not bad.

    $this->_model->expectOnce('update_package', array($installed));
    $this->_model->setReturnValue('update_package', $return);
    $this->assertIdentical($return, $this->_subject->update($installed));
  }


}


/* End of file      : test.upd_store_key.php */
/* File location    : third_party/store_key/tests/test.upd_store_key.php */
