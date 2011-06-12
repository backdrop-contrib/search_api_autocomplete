<?php

/**
 * @file
 * Contains the SearchApiAutocompleteInterface.
 */


/**
 * Interface describing the method a service class has to add to support autocompletion.
 *
 * Please note that this interface is purely documentational. You shouldn't, and
 * can't, implement it explicitly.
 */
interface SearchApiAutocompleteInterface extends SearchApiServiceInterface {

  public function getAutocompleteSuggestions(SearchApiQueryInterface $query, SearchApiAutocompleteSearch $search, $incomplete_key);

}
