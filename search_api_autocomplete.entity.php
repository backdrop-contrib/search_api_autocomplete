<?php

/**
 * @file
 * Contains the SearchApiAutocompleteSearch class.
 */


/**
 * Class describing the settings for a certain search for which autocompletion
 * is available.
 */
class SearchApiAutocompleteSearch extends Entity {

  // Entity properties, loaded from the database:

  /**
   * @var integer
   */
  public $id;

  /**
   * @var string
   */
  public $machine_name;

  /**
   * @var string
   */
  public $name;

  /**
   * @var integer
   */
  public $index_id;

  /**
   * @var string
   */
  public $type;

  /**
   * @var boolean
   */
  public $enabled;

  /**
   * @var array
   */
  public $options = array();

  // Inferred properties, for caching:

  /**
   * @var SearchApiIndex
   */
  protected $index;

  /**
   * @var SearchApiServer
   */
  protected $server;

  /**
   * Constructor.
   *
   * @param array $values
   *   The entity properties.
   */
  public function __construct(array $values = array()) {
    parent::__construct($values, 'search_api_autocomplete_search');
  }

  /**
   * @return SearchApiIndex
   *   The index this search belongs to.
   */
  public function index() {
    if (!isset($this->index)) {
      $this->index = search_api_index_load($this->index_id);
    }
    return $this->index;
  }

  /**
   * @return SearchApiServer
   *   The server this search would at the moment be executed on.
   */
  public function server() {
    if (!isset($this->server)) {
      if (!$this->index() || !$this->index()->server) {
        $this->server = FALSE;
      }
      else {
        $this->server = $this->index()->server();
      }
    }
    return $this->server;
  }

  /**
   * @return boolean
   *   TRUE if the server this search is currently associated with supports the
   *   autocompletion feature; FALSE otherwise.
   */
  public function supportsAutocompletion() {
    return $this->server() && $this->server()->supportsFeature('search_api_autocomplete');
  }

  /**
   * Helper method for altering a textfield form element to use autocompletion.
   */
  public function alterElement(array &$element) {
    if (user_access('use search_api_autocomplete') && $this->supportsAutocompletion()) {
      $element['#autocomplete_path'] = 'search_api_autocomplete/' . $this->machine_name;
    }
  }

  /**
   * Split a string with search keywords into two parts.
   *
   * The first part consists of all words the user has typed completely, the
   * second one contains the beginning of the last, possibly incomplete word.
   *
   * @return array
   *   An array with $keys split into exactly two parts, both of which may be
   *   empty.
   */
  public function splitKeys($keys) {
    $keys = ltrim($keys);
    // If there is whitespace or a quote on the right, all words have been
    // completed.
    if (rtrim($keys, " \t\n\r\0\x0B\"") != $keys) {
      return array(rtrim($keys), '');
    }
    if (preg_match('/^(.*?)\s*"?([\S]*)$/', $keys, $m)) {
      return array($m[1], $m[2]);
    }
    return array('', $keys);
  }

}
