/**
 * A library of defined constants used in sending responses back to the app.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Short string to indicate that the response text should be sent to the top bar.
 * 
 * @const {string} TOPBAR_STRING_THROWER
 * 
 * @since   LRS 3.9.0
 * @since   LBF 0.1.1-beta
 */

export const TOPBAR_STRING_THROWER = '+>';

/**
 * Short string to indicate that the response text should be sent to an browser alert.
 * 
 * @const {string} ALERT_STRING_THROWER
 * 
 * @since   LRS 3.9.0
 * @since   LBF 0.1.1-beta
 */

export const ALERT_STRING_THROWER = '==>';


/**
 * Short string to indicate that an error has occured and that any redirect should be blocked
 * 
 * @const {string} ERROR_STRING_THROWER
 * 
 * @since   LRS 3.15.0
 * @since   LBF 0.1.1-beta
 */

export const ERROR_STRING_THROWER = '<-x->';

/**
 * The id of the topbar response area.
 * 
 * @const {string} TOPBAR_RESPONSE
 * 
 * @since   LRS 3.9.0
 * @since   LBF 0.1.1-beta
 */

export const TOPBAR_RESPONSE = 'topbar_general_response';