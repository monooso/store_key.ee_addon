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
  public $required_by;
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

    // Load the model.
    $this->EE->load->model('store_key_model');
    $this->_model = $this->EE->store_key_model;

    // Still need to specify the package.
    $this->EE->lang->loadfile(
      strtolower($this->_model->get_sanitized_extension_class()),
      strtolower($this->_model->get_package_name()));

    // Set the public properties.
    $this->description = $this->EE->lang->line(
      'store_key_extension_description');

    $this->docs_url       = 'http://experienceinternet.co.uk/';
    $this->name           = $this->EE->lang->line('store_key_extension_name');
    $this->required_by    = array('module');
    $this->settings       = $settings;
    $this->settings_exist = 'n';
    $this->version        = $this->_model->get_package_version();
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
   * @param   array     $order    Order details.
   * @return  void
   */
  public function on_store_order_complete_end(Array $order)
  {
    if (($last_call = $this->EE->extensions->last_call) !== FALSE)
    {
      // Retrieve last call data.
    }

    /**
     * Don't worry about whether the order has actually been paid for. The only 
     * time this could happen on my site is when processing a "Manual" 
     * transaction, used for SuperAdmin testing.
     */

    // What use is an order with no items?
    if ( ! array_key_exists('items', $order)
      OR ! is_array($order['items'])
      OR count($order['items']) == 0
    )
    {
      $this->_model->log_message(
        $this->EE->lang->line('error__no_order_items'), 3);
      return;
    }

    // Loop through all the order items, generating a license key for each.
    foreach ($order['items'] AS $item)
    {
      // Each order item must have an order item ID, and a quantity.
      if ( ! array_key_exists('item_qty', $item)
        OR ! array_key_exists('order_item_id', $item)
        OR ! valid_int($item['item_qty'], 1)
        OR ! valid_int($item['order_item_id'], 1)
      )
      {
        $this->_model->log_message(
          $this->EE->lang->line('error__missing_order_item_details'), 3);

        continue;
      }

      // Generate the license keys.
      $item_id = $item['order_item_id'];

      // NOTE: license key generator expects 1-based indices.
      for ($count = 1; $count <= intval($item['item_qty']); $count++)
      {
        try
        {
          $this->_model->save_license_key($item_id,
            $this->_model->generate_license_key($item_id, $count));
        }
        catch (Exception $exception)
        {
          $this->_model->log_message($exception->getMessage(), 3);
        }
      }
    }
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
