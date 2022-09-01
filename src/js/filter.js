/**
 * A filtering tool
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Restricts input for the given textbox to the given inputFilter.
 * 
 * @link    https://stackoverflow.com/questions/469357/html-text-input-allow-only-numeric-input
 * @link    https://jsfiddle.net/emkey08/zgvtjc51
 * 
 * @examples
 * 
 * Numbers only
 * ```js
 * setInputFilter(document.getElementById("intTextBox"), function(value) {
 *   return /^-?\d*$/.test(value); });
 * ```
 * 
 * Integer >= 0
 * ```js
 * setInputFilter(document.getElementById("uintTextBox"), function(value) {
 *   return /^\d*$/.test(value); });
 * ```
 * 
 * Integer >= 0 and <= 500
 * ```js
 * setInputFilter(document.getElementById("intLimitTextBox"), function(value) {
 *   return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 500); });
 * ```
 * 
 * Float (use . or , as decimal separator)
 * ```js
 * setInputFilter(document.getElementById("floatTextBox"), function(value) {
 *   return /^-?\d*[.,]?\d*$/.test(value); });
 * ```
 * 
 * Currency (at most two decimal places)
 * ```js
 * setInputFilter(document.getElementById("currencyTextBox"), function(value) {
 *   return /^-?\d*[.,]?\d{0,2}$/.test(value); });
 * ```
 * 
 * A-Z only
 * ```js
 * setInputFilter(document.getElementById("latinTextBox"), function(value) {
 *   return /^[a-z]*$/i.test(value); });
 * ```
 * 
 * Hexadecimal
 * ```js
 * setInputFilter(document.getElementById("hexTextBox"), function(value) {
 *   return /^[0-9a-f]*$/i.test(value); });
 * ```
 * 
 * @param {string}   textbox        The id of the element being tested
 * @param {boolean}  inputFilter    Whether or not the item matches the regex fed to it
 * 
 * @since   3.6.3
 * @since   LBF 0.1.1-beta
 */

export function setInputFilter(textbox, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function (event) {
        textbox.addEventListener(event, function () {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    });
}