/**
 * A library of loading animations
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @see src\styles\lib\loaders.css
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Draw the page elements to create a loading animation on the screen
 * 
 * @param   {boolean}   dark    Load for dark mode. Default: false
 * 
 * @returns {string}
 * 
 * @since   LRS 2.27.2
 * @since   LBF 0.1.1-beta
 */

export default function loading_animation(dark = false) {
    if (dark) {
        return '<div class="lds-ellipsis lds-ellipsis-white"><div></div><div></div><div></div><div></div></div>';    
    }
    return '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
}


/**
 * Draw out the page loader animation.
 * 
 * @returns {string}
 * 
 * @since   LRS 3.21.0
 * @since   LBF 0.1.1-beta
 */

export function page_loading_animation() {
    return '<div class="lds-roller">Loading...<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
}


/**
 * Draw a grey bar animation animation
 * 
 * @returns {string} HTML
 * 
 * @since   LRS 3.25.4
 * @since   LBF 0.1.1-beta
 */

export function loading_placeholder() {
    return `
    <div class="loading_placeholder">
        <div class="loading_placeholder--animated-background"></div>
    </div>
    `;
}


/**
 * Draw a pair of cogs spinning as a placeholder, place some text under the spinner
 * 
 * @link https://codepen.io/XfuuX/pen/vGaOwR
 * 
 * @param {string} text 
 * @returns {string} HTML snippet
 * 
 * @since   LRS 3.25.4
 * @since   LBF 0.1.1-beta
 */

export function loading_cogs(text = '') {
    return `<div class="loading__cog">
    <div class="loading__cog-animation">
        <svg xmlns="http://www.w3.org/2000/svg" class="loading__cog_entry loading__cog-big" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="loading__cog_entry loading__cog-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
    </div>
    <p class="loading__cog-text">${text}</p>
  </div>`;
}