<?php

/**
 * @file
 * Contains page callbacks and theme functions for the frontend UI.
 */

/**
 * Page callback for getting autocomplete suggestions.
 */
function search_api_autocomplete_autocomplete(SearchApiAutocompleteSearch $search, $keys = '') {
  $ret = array();
  if ($search->supportsAutocompletion()) {
    $server = $search->server();
    list($complete, $incomplete) = $search->splitKeys($keys);
    $keys = preg_replace('/\s+/', ' ', trim($keys));
    $query = $search->getQuery($complete, $incomplete);
    if ($query) {
      // @todo Maybe make this configurable?
      $query->range(0, 10);
      $query->setOption('search id', 'search_api_autocomplete');
      $suggestions = $server->getAutocompleteSuggestions($query, $search, $incomplete, $keys);
      if ($suggestions) {
        foreach ($suggestions as $suggestion) {
          // Convert suggestion strings into an array.
          if (is_string($suggestion)) {
            $pos = strpos($suggestion, $keys);
            if ($pos === FALSE) {
              $suggestion = array('suggestion_suffix' => $suggestion);
            }
            else {
              $suggestion = array(
                'suggestion_prefix' => substr($suggestion, 0, $pos),
                'user_input' => $keys,
                'suggestion_suffix' => substr($suggestion, $pos + strlen($keys)),
              );
            }
          }
          // Add defaults.
          $suggestion += array(
            'prefix' => NULL,
            'suggestion_prefix' => '',
            'user_input' => '',
            'suggestion_suffix' => '',
            'results' => NULL,
          );
          $key = $suggestion['suggestion_prefix'] . $suggestion['user_input'] . $suggestion['suggestion_suffix'];
          $ret[$key] = theme('search_api_autocomplete_suggestion', $suggestion);
        }
      }
    }
  }
  drupal_json_output($ret);
}

/**
 *
 *
 * @param array $variables
 *   An associative array containing:
 *   - prefix: For special suggestions, some kind of prefix describing them.
 *   - suggestion_prefix: A suggested prefix for the entered input.
 *   - user_input: The input entered by the user.
 *   - suggestion_suffix: A suggested suffix for the entered input.
 *   - results: If available, the estimated number of results for these keys.
 */
function theme_search_api_autocomplete_suggestion(array $variables) {
  // @todo
  extract($variables);
  return "$prefix $suggestion_prefix<strong>$user_input</strong>$suggestion_suffix ($results)";
}
