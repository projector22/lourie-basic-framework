/**
 * A set of tools for generating print and print like tasks.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */

import URITools from './uri.js';


/**
 * Add a print=instruction to the url string allowing the user to generate a pdf printout
 * 
 * @param {string} instruction The value to assign to $_GET['print']
 * 
 * @since   LRS 3.6.0
 * @since   LBF 0.1.1-beta
 */

export function instruct_to_open_pdf(instruction) {
    URITools.uri_change(['print', 'task'], [instruction, 'pdf']);
}