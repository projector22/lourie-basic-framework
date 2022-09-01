/**
 * This script is a model observer
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.12.8
 * @since   LBF 0.1.1-beta
 */

/**
 * Create a mutation observe and run a custom function on it
 * 
 * @param {object} element_to_observe The element to observe. From document.getElementById('id)
 * @param {function} callback_function The function to execute if an observation is made
 * 
 * @since   3.13.0
 * @since   LBF 0.1.1-beta
 */

export default function observe(element_to_observe, callback_function) {
    const MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
    const config = {
        attributes: true,
        childList: true,
        subtree: true
    };

    // Create an observer instance linked to the callback function
    const observer = new MutationObserver(callback_function);

    // Start observing the target node for configured mutations
    observer.observe(element_to_observe, config);
}


/**
 * Create an intersection observer running on the screen
 * 
 * @param {function} callback_function The function to apply within the IntersectionObserver
 * 
 * @since   3.13.0
 * @since   LBF 0.1.1-beta
 */

export function observe_intersect(element_to_observe, callback_function) {
    const config = {};
    const observer = new IntersectionObserver(callback_function, config);
    observer.observe(element_to_observe);

    /**
     * @example of a callback_function
     * 
     * const callback = function (entries, observer) {
     *     entries.forEach(entry => {
     *         // Do stuff that is observed
     * });
     */
}