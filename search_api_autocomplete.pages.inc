<?php

/**
 * @file
 * Contains page callbacks and theme functions for the frontend UI.
 */

/**
 * Page callback for getting autocomplete suggestions.
 */
function search_api_autocomplete_autocomplete(SearchApiAutocompleteSearch $search, $keys = '') {
  $suggestions = array();
  if ($search->supportsAutocompletion()) {
    $server = $search->server();
    $split_keys = $search->splitKeys($keys);
  }
  drupal_json_output($suggestions);
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
  return "$prefix $suggestion_prefix$user_input$suggestion_suffix ($results)";
}
