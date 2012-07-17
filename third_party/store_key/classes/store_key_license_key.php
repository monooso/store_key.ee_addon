<?php

/**
 * Store Key License Key datatype.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Store_key
 */

require_once dirname(__FILE__) .'/EI_datatype.php';

class Store_key_license_key extends EI_datatype
{

  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   array    $props    Associative array of property names and values.
   * @return  void
   */
  public function __construct(Array $props = array())
  {
    parent::__construct($props);
  }


  /**
   * Magic 'setter' method.
   *
   * @access  public
   * @param   string    $prop_name    The property to set.
   * @param   mixed     $prop_value   The new property value.
   * @return  void
   */
  public function __set($prop_name, $prop_value)
  {
    switch ($prop_name)
    {
      case 'entry_id':
      case 'order_id':
      case 'order_item_id':
        $this->_set_int_property($prop_name, $prop_value, 1);
        break;

      case 'license_key':
      case 'sku':
      case 'title':
        $this->_set_string_property($prop_name, $prop_value);
        break;
    }
  }


  /**
   * Resets the instance properties.
   *
   * @access  public
   * @return  Store_key_license_key
   */
  public function reset()
  {
    $this->_props = array(
      'entry_id'      => 0,
      'license_key'   => '',
      'order_id'      => 0,
      'order_item_id' => 0,
      'sku'           => '',
      'title'         => ''
    );

    return $this;
  }


}


/* End of file      : store_key_license_key.php */
/* File location    : third_party/store_key/classes/store_key_license_key.php */
