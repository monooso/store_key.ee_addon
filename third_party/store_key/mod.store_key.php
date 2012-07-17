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
    // Retrieve the order ID and order item ID (optional).
    $order_id      = $this->EE->TMPL->fetch_param('order_id', 0);
    $order_item_id = $this->EE->TMPL->fetch_param('order_item_id', 0);

    // The name of our 'no_results' tag, and the content to process.
    $no_results_tag = 'no_license_keys';
    $tagdata        = $this->EE->TMPL->tagdata;

    // Get out early.
    if (trim($tagdata) == '')
    {
      return '';
    }

    /**
     * We *must* have a valid order_id parameter.
     *
     * There's a strong argument to be made for throwing a fatal error here. 
     * For the time being we'll be nice, log a template message, and return 'no 
     * results'.
     */

    if ( ! valid_int($order_id, 1))
    {
      $this->EE->TMPL->log_item(
        $this->EE->lang->line('error__licenses_tag_missing_order_id'));

      return $this->_get_no_results($tagdata, $no_results_tag);
    }

    /**
     * The order_item_id parameter is optional, but if one has been provided, it 
     * must at least appear valid. In this case, that means any non-negative 
     * integer (0 is ignored, but that's dealt with later).
     */

    if ( ! valid_int($order_item_id))
    {
      $this->EE->TMPL->log_item(
        $this->EE->lang->line('error__licenses_tag_invalid_order_item_id'));

      return $this->_get_no_results($tagdata, $no_results_tag);
    }

    // Retrieve the applicable license keys.
    try
    {
      $keys = $this->_model->get_license_keys_by_order_id($order_id);
    }
    catch (Exception $e)
    {
      $this->EE->TMPL->log_item($e->getMessage());
      return $this->_get_no_results($tagdata, $no_results_tag);
    }

    // Filter by order item ID, if required.
    if ($order_item_id)
    {
      $keys = array_filter($keys, function($key) use ($order_item_id) {
        return $key->order_item_id == $order_item_id;
      });
    }

    // No keys? Return no_results.
    if ( ! $keys)
    {
      return $this->_get_no_results($tagdata, $no_results_tag);
    }

    // Prep. the data.
    $prepped_data = array();

    foreach ($keys AS $key)
    {
      $prepped_data[] = $key->to_array('store_key:');
    }

    // Return the parsed tag data.
    return $this->return_data = $this->EE->TMPL->parse_variables(
      $this->EE->TMPL->tagdata, $prepped_data);
  }



  /* --------------------------------------------------------------
   * PRIVATE METHOD
   * ------------------------------------------------------------ */

  /**
   * Turns out there's a big fat horrendous bug in the ExpressionEngine Template 
   * parser, when it comes to nested template tags. Sorry, did I say "bug"? I 
   * meant feature, clearly.
   *
   * I may get around to reporting it in the bug tra...oh dear me. Oh. Just 
   * give me a moment.... ohhh. Fuck me, that's funny.
   *
   * Anyway, this is a workaround. It accepts some tagdata, and the name of the
   * 'no results' tag pair, and returns the 'no results' content.
   *
   * @access  private
   * @param   string    $tagdata    The tagdata to parse.
   * @param   string    $tag_name   The 'no results' tag name.
   * @return  string
   */
  private function _get_no_results($tagdata, $tag_name)
  {
    // Looking for 'if no results' block...
    $pattern = '#' .LD .'if ' .$tag_name .RD .'(.*?)' .LD .'/if' .RD .'#s';

    if (is_string($tagdata) && is_string($tag_name)
      && preg_match($pattern, $tagdata, $matches)
    )
    {
      return $matches[1];
    }

    return '';
  }

  
}


/* End of file      : mod.store_key.php */
/* File location    : third_party/store_key/mod.store_key.php */
