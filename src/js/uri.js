/**
 * A library of tools for dealing with the URI
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.13.0
 * @since   3.21.1  Converted to a class.
 */

/**
 * Tool for handling various URI operations.
 * 
 * @since   3.21.1
 * @since   LBF 0.1.1-beta
 */

export default class URITools {
    constructor() {
        this.uri = new URLSearchParams(location.search)

        this.uri.forEach((element, index) => {
            this[index] = element;
        });
    }


    /**
     * Return the value of the linked to the parsed key.
     * 
     * @param {string} key The key to search by.
     * 
     * @returns {string}
     * 
     * @since   3.22.1
     * @since   LBF 0.1.1-beta
     */

    get_param(key) {
        return this[key];
    }


    /**
     * Get the URL of the page we're on
     * 
     * @since   2.27.0
     * @since   3.13.0  Revamped
     * @since   3.21.2  Moved into class
     * @since   LBF 0.1.1-beta
     */

    static get_url() {
        const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
        return url + window.location.search.substring(0);
    }


    /**
     * Get the GET data on a URL and add your own defined value to it, 
     * for example: http://example.com?p=sample -> http://example.com?p=sample&customitem=customvalue
     * 
     * @param {string|array} query    Custom id heading to be used - string or array
     * @param {string|array} value    Custom value to be used - string or array
     * 
     * @returns     The string with the custom item added - ?p=sample&customitem=customvalue
     * 
     * @version     1.0
     * @version     1.1     Added support for arrays.
     * @version     1.2     Modified for modern standards, Fixed a bug when handling arrays.
     * @version     1.3     Moved into class and renamed uri_change from uri_handle.
     * 
     * @since   2.27.0
     * @since   3.9.0   Updated to the modern standard and a few minor bugs ironed out
     * @since   3.22.1  Moved into class
     * @since   LBF 0.1.1-beta
     */

    static uri_handle(query, value) {
        const origonal_uri = window.location.search.substring(0);
        let new_uri = origonal_uri;

        /**
         * Check if the data inputed is an array, handle differently if so
         */

        if (Array.isArray(query) && Array.isArray(value)) {
            // If the @params are arrays
            for (let i = 0; i < query.length; i++) {
                let search_query = query[i];

                /**
                 * Check to see if the query is the first in the URI string
                 * If so, prefex with an '?' otherwise use a '&'
                 * 
                 * @since   3.9.0
                 */

                let sep = '&';
                if (origonal_uri.indexOf(`?${search_query}=`) > -1 || origonal_uri.length == 0 && i == 0) {
                    sep = '?'
                }

                /**
                 * The new URI string fragment to be replaced in the URI, if the conditions are met
                 * 
                 * @since   3.9.0
                 */

                let new_value = `${sep}${search_query}=${value[i]}`;

                /**
                 * The position within the URI of the of the origonal search query
                 * 
                 * @since   3.9.0
                 */

                let query_position = origonal_uri.indexOf(search_query);

                /**
                 * If the query already exists on the URI ( > -1 ) do the following:
                 * 
                 * 1. Find the origonal value of the query
                 * 2. Replace the full origonal query (&query=value) with the new value
                 * 
                 * @since   3.9.0
                 */

                if (query_position > -1) {

                    /**
                     * Start position of origonal value
                     * location of search text + length of search text + 1 for the =
                     * 
                     * @since   3.9.0
                     */

                    let start = query_position + search_query.length + 1;

                    /**
                     * End position of origonal value
                     * start position of the origonal value + length of origonal value
                     * 
                     * if @var end returns -1, this means that the query value is at the end, 
                     * and should then be returned as the length of the origonal search URI
                     * 
                     * @since   3.9.0
                     */

                    let end = origonal_uri.indexOf('&', start);
                    if (end == -1) {
                        end = origonal_uri.length;
                    }

                    /**
                     * The origonal value to be replaced
                     * in the form of: &query=value
                     * 
                     * @since   3.9.0
                     */

                    let old_value = `${sep}${search_query}=${origonal_uri.substring(start, end)}`;

                    /**
                     * If the origonal search query and the newly formed one don't match, make the replacement
                     * 
                     * @since   3.9.0
                     */

                    if (old_value !== new_value) {
                        new_uri = new_uri.replace(old_value, new_value);
                    }
                } else {

                    /**
                     * If the query does not already exist on the URI, add it
                     * 
                     * @since   3.9.0
                     */

                    new_uri += `${sep}${query[i]}=${value[i]}`;
                }
            }
        } else {
            // If the arrays are strings
            let search_query = query;

            /**
             * Check to see if the query is the first in the URI string
             * If so, prefex with an '?' otherwise use a '&'
             * 
             * @since   3.?
             */

            if (origonal_uri.indexOf(`?${query}=`) > -1) {
                search_query = `?${query}=`;
            } else if (origonal_uri.indexOf(`&${query}=`) > -1) {
                search_query = `&${query}=`;
            }

            /**
             * The position within the URI of the of the origonal search query
             * 
             * @since   3.?
             */

            let query_position = origonal_uri.indexOf(search_query);

            /**
             * If the query already exists on the URI ( > -1 ) do the following:
             * 
             * 1. Find the origonal value of the query
             * 2. Replace the full origonal query (&query=value) with the new value
             * 
             * @since   3.?
             */

            if (query_position > -1) {

                /**
                 * Find where the next '&' appears after the search string
                 * 
                 * @since   3.?
                 */

                let end = origonal_uri.indexOf('&', query_position + 1); // end is meant to be where the next &

                /**
                 * The text to be replaced
                 * 
                 * @since   3.?
                 */

                let del;

                if (end === -1) {

                    /**
                     * If the replaced string is at the end of the URI
                     * 
                     * @since   3.?
                     */

                    del = origonal_uri.substr(query_position, origonal_uri.length);
                } else {

                    /**
                     * If the replaced string is not at the end of the URI
                     * 
                     * @since   3.?
                     */

                    del = origonal_uri.substr(query_position, end - query_position);
                }

                /**
                 * Perform the subsitution of the old string with the new string
                 * 
                 * @since   3.9.0
                 */

                new_uri = `${new_uri.replace(del, '')}&${query}=${value}`;
            } else {

                /**
                 * If the query does not already exist on the URI, add it
                 * 
                 * @since   3.9
                 */

                new_uri += `&${query}=${value}`;
            }
        }
        if (new_uri.charAt(0) == '&') {
            new_uri = new_uri.substring(1);
        }
        return new_uri;
    }


    /**
     * Load the desired page by the window.location.search method using the `uri_handle` tool.
     * 
     * @param {string|array} query    Custom id heading to be used - string or array
     * @param {string|array} value    Custom value to be used - string or array
     * 
     * @since   3.22.1
     * @since   LBF 0.1.1-beta
     */

    static uri_change(query, value) {
        window.location.search = this.uri_handle(query, value);
    }


    /**
     * Load the desired page by the window.open in a new tab, method using the `uri_handle` tool.
     * 
     * @param {string|array} query    Custom id heading to be used - string or array
     * @param {string|array} value    Custom value to be used - string or array
     * 
     * @since   3.22.1
     * @since   LBF 0.1.1-beta
     */

    static open_uri_new_tab(query, value) {
        window.open(this.uri_handle(query, value), '_blank');
    }


    /**
     * Create a fully formed URI string.
     * 
     * @param {object} list Key: Value pairs to add to the URI.
     * 
     * @returns {string} Something like `?page=x&a=y&b=z`
     * 
     * @since   3.27.1
     * @since   LBF 0.1.1-beta
     */

    static generate_url_from_list(list) {
        let i = 0;
        let uri = '';
        for (const key in list) {
            if (i == 0) {
                uri += '?';
                i++;
            } else {
                uri += '&'
            }
            uri += `${key}=${list[key]}`;
        }
        return uri;
    }
}