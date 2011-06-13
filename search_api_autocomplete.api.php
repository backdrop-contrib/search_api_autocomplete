<?php

/**
 * @file
 * Hooks provided by the Search API autocomplete module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 *
 *
 * @return array
 *   An array with search types as the keys, mapped to arrays containing the
 *   following entries:
 *   - name: The category name for searches of this type.
 *   - description: A short description of this type (may contain HTML).
 *   - list searches: Callback function that returns a list of all known
 *     searches of this type for a given index. See
 *     example_list_autocomplete_searches() for the expected function signature.
 *   - create query: Callback function to create a search query for a search of
 *     this type and some user input. See example_create_autocomplete_query()
 *     for the expected function signature.
 *   - config form: (optional) Callback function for adding a form for
 *     type-specific options to a search's autocomplete settings form. See
 *     example_autocomplete_config_form() for the expected function signature.
 *     This function name will also be the base for custom validation and submit
 *     callbacks, with "_validate" or "_submit" appended, respectively.
 *
 * @see example_list_autocomplete_searches()
 * @see example_create_autocomplete_query()
 * @see example_autocomplete_config_form()
 * @see example_autocomplete_config_form_validate()
 * @see example_autocomplete_config_form_submit()
 */
function hook_search_api_autocomplete_types() {
  $types['example'] = array(
    'name' => t('Example searches'),
    'description' => t('Searches provided by the <em>Example</em> module.'),
    'list searches' => 'example_list_autocomplete_searches',
    'create query' => 'example_create_autocomplete_query',
    'config form' => 'example_autocomplete_config_form',
  );

  return $types;
}

/**
 * @} End of "addtogroup hooks".
 */

/**
 * Returns a list of searches for the given index.
 *
 * All searches returned must have a unique and well-defined machine name. The
 * implementing module for this type is responsible for being able to map a
 * specific search always to the same distinct machine name.
 *
 * Also, name and machine name have to respect the length constraints from
 * search_api_autocomplete_schema().
 *
 * @param SearchApiIndex $index
 *   The index whose searches should be returned.
 *
 * @return array
 *   An array of searches, keyed by their machine name. The values are arrays
 *   with the following keys:
 *   - name: A human-readable name for this search.
 *   - options: (optional) An array of options to use for this search.
 *     Type-specific options should go into the "custom" nested key in these
 *     options.
 */
function example_list_autocomplete_searches(SearchApiIndex $index) {
  $ret = array();
  $result = db_query('SELECT name, machine_name, extra FROM {example_searches} WHERE index_id = :id', array($index->machine_name));
  foreach ($result as $row) {
    $id = 'example_' . $row->machine_name;
    $ret[$id] = array(
      'name' => $row->name,
    );
    if ($row->extra) {
      $ret[$id]['options']['custom']['extra'] = $row->extra;
    }
  }
  return $ret;
}
