/**
 * A library of tools for dealing with the UI
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Replace one element with another by id, by hiding and unhiding
 * @param {string} id_hide      The element to hide
 * @param {string} id_unhide    The element to show
 * 
 * @since   LRS 3.7.6
 * @since   LRS 3.22.1  Revamped to use css class 'hidden'. Removed param inline.
 * @since   LBF 0.1.1-beta
 */

export function replace_element(id_hide, id_unhide) {
    document.getElementById(id_hide).classList.add('hidden');
    document.getElementById(id_unhide).classList.remove('hidden');
}


/** 
 * Revamp of the above function
 * 
 * @param {string}  id Which id to apply to
 * 
 * @since   LRS 3.9.0
 * @since   LBF 0.1.1-beta
 */

export function show_hide(id) {
    const item = document.getElementById(id);
    if (item.classList.contains('hidden')) {
        item.classList.remove('hidden');
    } else {
        item.classList.add('hidden');
    }
}


/**
 * Show or hide an element depending on if a check box or toggle is clicked
 * 
 * @param {object} element          The object which is being observed, parsed as 'this'
 * @param {string} show_hide_id     The id of the element to hide or unhide
 * @param {boolean} show_when_on    Whether to hide when ticked or show when ticked
 * 
 * @since   LRS 3.8.0
 * @since   LBF 0.1.1-beta
 */

export function toggle_hidden(element, show_hide_id, show_when_on = true) {
    const item = document.getElementById(show_hide_id);
    if (element.checked) {
        if (show_when_on) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    } else {
        if (show_when_on) {
            item.classList.add('hidden');
        } else {
            item.classList.remove('hidden');
        }
    }
}


/**
 * Scroll the page from the bottom to the top and vice verse
 * 
 * @param {string} direction derived from this.id
 * 
 * @since   LRS 3.5.2
 * @since   LBF 0.1.1-beta
 */

export function zoom_updown(direction) {
    const top = 0;
    const bottom = 50000; // Would like to find a better way of detecting the bottom
    const element = document.getElementById('content_wrapper');
    switch (direction) {
        case 'ftb_up':
            element.scrollTop = top;
            element.scrollTop = top;
            break;
        case 'ftb_down':
            element.scrollTop = bottom;
            element.scrollTop = bottom;
            break;
    }
}