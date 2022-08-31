/**
 * A library of misc tools.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.17.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Perform a pause or delay for a desired length of time.
 * 
 * @param {integer} delay Measured in ms. 1000 = 1 second.
 * 
 * @since   3.17.0
 * @since   LBF 0.1.1-beta
 */

export const sleep = (delay) => new Promise((resolve) => setTimeout(resolve, delay));


/**
 * Count the number of characters in a string.
 * 
 * @param {string} haystack The string to search though.
 * @param {char} needle The character to look for.
 * @returns integer
 * 
 * @since   3.17.0
 * @since   LBF 0.1.1-beta
 */

export function character_count(haystack, needle) {
    let count = 0;
    for (let i = 0; i < haystack.length; i++) {
        if (haystack.charAt(i) == needle) {
            count++;
        }
    }
    return count;
}


/**
 * Returns the inner text of the selected entry of a select box.
 * 
 * @param {object} element The DOM element from document.getElementById('a_select_box');
 * @returns {string}
 * 
 * @since   3.25.0
 * @since   LBF 0.1.1-beta
 */

export function get_selectbox_selected_innerText(element) {
    return element.options[element.selectedIndex].innerText;
}


/**
 * Convert bytes to the appropriate size.
 * 
 * @see https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
 * 
 * @param {integer} bytes Bytes to compare
 * @param {ingeger} decimals Decimal places to draw to. Default: 2
 * 
 * @returns {string}
 * 
 * @since   3.25.1
 * @since   LBF 0.1.1-beta
 */

export function bytes_to_size(bytes, decimals = 2) {
    if (bytes === 0) {
        return '0 Bytes';
    }
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}