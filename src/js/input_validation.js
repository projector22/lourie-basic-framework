/**
 * Class to perform standard input field validation tasks
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.15.10
 * @since   LBF 0.1.1-beta
 */

import Ajax from './ajax.js';
import observe from './mutations.js';
import {
    check_valid_email,
    validate_date_input,
    validate_ZA_ID_number,
} from './validation.js';

/**
 * Perform some basic validation operations on input type fields. Specific logic is worked outside the
 * class and then the class does the standard 'decoration' according to that validation.
 * 
 * @since   LRS 3.15.10
 * @since   LBF 0.1.1-beta
 */

export default class Input_Validation {

    /**
     * Object class for performing input field validation
     * 
     * @param {string} element_id   The id of the element being validated.
     * @param {string} response_id  The id of the feedback response element. Default: null.
     * @param {string} nil_value    The value which with which the required check is compared against. Useful for selectboxes.
     * 
     * @since   LRS 3.15.10
     * @since   LBF 0.1.1-beta
     */

    constructor(element_id, response_id = null, nil_value = '') {

        /**
         * The element being validated.
         * 
         * @var {DOM} input_field
         * 
         * @since   LRS 3.15.10
         */

        this.input_field = document.getElementById(element_id);

        /**
         * The id of the feedback response element.
         * 
         * @var {string} response_id
         * 
         * @since   LRS 3.15.10
         */

        this.response_id = response_id;

        /**
         * The value against which the input field should be compared.
         * By default '', but can be different if using a select box, for example.
         * 
         * @var {string} nil_value
         * 
         * @since   LRS 3.21.0
         */

        this.nil_value = nil_value;

        /**
         * Flag to indicate a state of not being set.
         * 
         * @var {integer} FLAG_UNSET
         * 
         * @since   LRS 3.15.10
         */

        this.FLAG_UNSET = FLAG_UNSET;

        /**
         * Flag to indicate a state of passing.
         * 
         * @var {integer} FLAG_PASSED
         * 
         * @since   LRS 3.15.10
         */

        this.FLAG_PASSED = FLAG_PASSED;

        /**
         * Flag to indicate a state of failing.
         * 
         * @var {integer} FLAG_FAILED
         * 
         * @since   LRS 3.15.10
         */

        this.FLAG_FAILED = FLAG_FAILED;

        /**
         * Flag for when password strength being tested
         * 
         * @var {integer} FLAG_PASSWORD_STRENGTH
         * 
         * @since   LRS 3.16.0
         */

        this.FLAG_PASSWORD_STRENGTH = '3';

        /**
         * Validate variable for holding a variable state when passing the object around
         * 
         * @var {boolean} validated
         * 
         * @since   LRS 3.16.0
         */

        this.validated = undefined;
    }


    /**
     * Perform the 'decoration' according to whichever flag is parsed.
     * 
     * @param {integer} flag The choice of 'decoration'. Unset, Passed, or Failed.
     * 
     * @since   LRS 3.15.10
     */

    set_field_validation_feedback(flag) {
        if (!this.input_field) {
            return;
        }
        switch (flag) {
            case this.FLAG_UNSET:
                this.set_response_message('');
                this.input_field.classList.remove('hide_focus');
                this.input_field.classList.remove('border--valid');
                this.input_field.classList.remove('border--invalid');
                this.input_field.dataset.validated = this.FLAG_UNSET;
                break;
            case this.FLAG_PASSED:
                this.set_response_message('');
                this.input_field.classList.remove('border--invalid');
                this.input_field.classList.add('border--valid');
                this.input_field.classList.add('hide_focus');
                this.input_field.dataset.validated = this.FLAG_PASSED;
                break;
            case this.FLAG_FAILED:
                this.input_field.classList.remove('border--valid');
                this.input_field.classList.add('border--invalid');
                this.input_field.classList.add('hide_focus');
                this.input_field.dataset.validated = this.FLAG_FAILED;
                break;
            case this.FLAG_PASSWORD_STRENGTH:
                this.input_field.classList.remove('border--invalid');
                this.input_field.classList.add('border--valid');
                this.input_field.classList.add('hide_focus');
                break;
            default:
                throw `Invalid flag ${flag} parsed`;
        }
    }


    /**
     * Puts an optional response message in the dedicated response element.
     * 
     * @param {string} $msg The message that feeds back to the user
     * 
     * @since   LRS 3.15.10
     */

    set_response_message($msg) {
        if (this.response_id == null) {
            return;
        }
        document.getElementById(this.response_id).innerHTML = $msg;
    }


    /**
     * Validate the user's input automatically.
     * 
     * ### Available for Validation
     * - type
     * - required
     * - zaID
     * - username_unique
     * - password_strength
     * - password_match
     * - date_not_before
     * - date_not_after
     * 
     * @param {object} validations The validations to be parsed
     * 
     * @since   LRS 3.16.0
     */

    general_validator(validations = {}) {
        const current_object = this;
        const validators = {};
        this.input_field.addEventListener("input", function () {
            const promises = [];
            for (const [property, value] of Object.entries(validations)) {
                switch (property) {
                    case 'required':
                        if (value == false) {
                            break;
                        }
                        const required_promise = new Promise((resolve, reject) => {
                            resolve(current_object.is_required());
                        });
                        promises.push(required_promise);
                        break;
                    case 'date_not_before':
                        const date_not_before_promise = new Promise((resolve, reject) => {
                            resolve(current_object.check_date_not_before(
                                resolve,
                                document.getElementById(value).value, // start date
                                current_object.input_field.value, // end date
                            ));
                        });
                        promises.push(date_not_before_promise);
                        break;
                    case 'date_not_after':
                        const date_not_after_promise = new Promise((resolve, reject) => {
                            resolve(current_object.check_date_not_after(
                                resolve,
                                current_object.input_field.value, // start date
                                document.getElementById(value).value, // end date
                            ));
                        });
                        promises.push(date_not_after_promise);
                        break;
                }
                if (validators[property] == false) {
                    break;
                }
            }
            Promise.all(promises).then((validations) => {
                let is_valid = null;
                for (let i = 0; i < validations.length; i++) {
                    is_valid = current_object.set_async_messages(validations[i]);
                }
                if (validations.length > 0) {
                    switch (is_valid) {
                        case true:
                            current_object.set_field_validation_feedback(current_object.FLAG_PASSED);
                            break;
                        case false:
                            current_object.set_field_validation_feedback(current_object.FLAG_FAILED);
                            break;
                        case null:
                            current_object.set_field_validation_feedback(current_object.FLAG_UNSET);
                            break;
                    }
                }
            });
        });
        this.input_field.addEventListener("keyup", function () {
            const promises = [];
            for (const [property, value] of Object.entries(validations)) {
                switch (property) {
                    case 'type':
                        const type_promise = new Promise((resolve, reject) => {
                            resolve(current_object.type_validation(value));
                        });
                        promises.push(type_promise);
                        break;
                    case 'zaID': // South African ID number
                        if (value == false) {
                            break;
                        }
                        const zaID_promise = new Promise((resolve, reject) => {
                            resolve(current_object.zaID_validation());
                        });
                        promises.push(zaID_promise);
                        break;
                    case 'username_unique':
                        if (value == false) {
                            break;
                        }
                        const username_unique_promise = new Promise((resolve, reject) => {
                            if (current_object.input_field.value == '') {
                                resolve(null);
                            }
                            current_object.username_unique(resolve);
                        });
                        promises.push(username_unique_promise);
                        break;
                    case 'password_strength':
                        if (value == false) {
                            break;
                        }
                        const pw_strength = new Promise((resolve, reject) => {
                            current_object.check_password_strength(resolve, current_object.input_field.value);
                        });
                        promises.push(pw_strength);
                        break;
                    case 'password_match':
                        if (value['perform_match'] == false) {
                            break;
                        }
                        const match = document.getElementById(value['match_id']).value;
                        const pw_match_promise = new Promise((resolve, reject) => {
                            current_object.check_password_match(resolve, current_object.input_field.value, match);
                        });
                        promises.push(pw_match_promise);
                        break;
                }
                if (validators[property] == false) {
                    break;
                }
            }
            Promise.all(promises).then((validations) => {
                let is_valid = null;
                for (let i = 0; i < validations.length; i++) {
                    const entry = current_object.set_async_messages(validations[i]);
                    if (entry == false) {
                        is_valid = false;
                        break;
                    }
                    if (entry == 'password_strength') {
                        is_valid = entry;
                        break;
                    }
                    if (entry == true) {
                        is_valid = true;
                    }
                }
                if (validations.length > 0) {
                    switch (is_valid) {
                        case true:
                            current_object.set_field_validation_feedback(current_object.FLAG_PASSED);
                            break;
                        case false:
                            current_object.set_field_validation_feedback(current_object.FLAG_FAILED);
                            break;
                        case 'password_strength':
                            current_object.set_field_validation_feedback(current_object.FLAG_PASSWORD_STRENGTH);
                            break;
                        case null:
                            current_object.set_field_validation_feedback(current_object.FLAG_UNSET);
                            break;
                    }
                }
            });
        });
    }


    /**
     * Checks the object being parsed, set a message based on the value, if not already set and return the value;
     * 
     * @param {object} data Object to be checked.
     * 
     * @returns {boolean|null}
     * 
     * @since   LRS 3.16.0
     */

    set_async_messages(data) {
        if (data == null) {
            return null;
        }
        for (const [property, value] of Object.entries(data)) {
            switch (property) {
                case 'account_unique':
                    if (!value) {
                        this.set_response_message('New username must be unique');
                    }
                    break;
                case 'password_strength':
                    this.set_response_message(value);
                    return property;
                case 'password_match':
                    this.set_response_message('The passwords do not match');
                    break;
                case 'date_not_before':
                    this.set_response_message('This date must be after the start date.');
                    break;
                case 'date_not_after':
                    this.set_response_message('This date must be before the end date.');
                    break;
            }
            return value;
        }
    }


    /**
     * Validate by data type
     * 
     * ### Available for Validation
     * - number
     * - phone
     * - email
     * 
     * @param {string} type The parsed type for checking.
     * 
     * @returns {boolean|null} Whether or not the validation has passed. Null => unset
     * 
     * @since   LRS 3.16.0
     */

    type_validation(type) {
        switch (type) {
            case 'number':
                if (this.input_field.value == '') {
                    return {
                        is_number: null
                    };
                }
                if (isNaN(this.input_field.value)) {
                    this.set_response_message('Must be a number');
                    return {
                        is_number: false
                    };
                }
                return {
                    is_number: true
                };
            case 'phone':
                if (this.input_field.value == '') {
                    return {
                        valid_phone: null
                    };
                }
                if (isNaN(this.input_field.value.charAt(0)) && this.input_field.value.charAt(0) != '+') {
                    this.set_response_message('Not a valid phone number');
                    return {
                        valid_phone: false
                    };
                }
                if (isNaN(this.input_field.value.substring(1))) {
                    this.set_response_message('Not a valid phone number');
                    return {
                        valid_phone: false
                    };
                }
                return {
                    valid_phone: true
                };
            case 'email':
                if (this.input_field.value == '') {
                    return null;
                }
                return {
                    valid_email: check_valid_email(this.input_field.value)
                };
            case 'date':
                if (this.input_field == '') {
                    return false;
                }
                return {
                    valid_phone: validate_date_input(this.input_field.value)
                };
        }
    }


    /**
     * Get if the input field is blank.
     * 
     * @returns {boolean}
     * 
     * @since   LRS 3.16.0
     */

    is_required() {
        if (this.input_field.value == this.nil_value) {
            if (this.input_field.tagName == 'SELECT') {
                this.set_response_message('Please choose a valid option');
            } else {
                this.set_response_message('This field cannot be blank');
            }
        }
        return {
            is_required: this.input_field.value != this.nil_value,
        };
    }


    /**
     * Get if the input field contains a valid South African ID.
     * 
     * @returns {boolean}
     * 
     * @since   LRS 3.16.0
     */

    zaID_validation() {
        if (this.input_field.value == '') {
            return null;
        }
        const valid = validate_ZA_ID_number(this.input_field.value);
        if (!valid) {
            this.set_response_message('Not a valid South African ID number');
        }
        return {
            is_valid: valid
        };
    }


    /**
     * Check if the username is unique.
     * 
     * @param {function} resolve    The Promise resolve function
     * 
     * @since   LRS 3.7.6
     * @since   LRS 3.16.0 Moved to `input_validation` class and renamed from `check_unique_username` to `username_unique`.
     */

    username_unique(resolve) {
        if (this.input_field.value == '') {
            resolve(null);
        }
        const ajax = new Ajax;
        ajax.response = this.response_id;
        ajax.add_token('check_unique_username');
        ajax.add_param('username', this.input_field.value);
        ajax.execute_promise_resolve(resolve);
    }


    /**
     * Perform the ajax to check the password strength.
     * 
     * @param {function} resolve    The Promise resolve function.
     * @param {string}   password   The password being tested.
     * 
     * @since   LRS 3.7.6
     * @since   LRS 3.16.0 Moved to `input_validation` class.
     */

    check_password_strength(resolve, password) {
        const ajax = new Ajax;
        ajax.response = this.response_id;
        ajax.add_token('check_password_score');
        ajax.add_param('password', password);
        ajax.execute_promise_resolve(resolve);
    }


    /**
     * Resolve if passwords do match
     * 
     * @param {function} resolve    The Promise resolve function.
     * @param {string}   password   The password being tested.
     * @param {string}   match      The password being compared to the test.
     * 
     * @since   LRS 3.7.6
     * @since   LRS 3.16.0 Moved to `input_validation` class and reworked for the class.
     */
    check_password_match(resolve, password, match) {
        resolve({
            password_match: password == match
        });
    }


    /**
     * Resolve if the input date is before the comparisive date.
     * 
     * @param {function} resolve    The Promise resolve function.
     * @param {string} start_date   The date that should first or older than end_date.
     * @param {string} end_date     The date that should be after, or newer than start_date.
     * 
     * @since   LRS 3.20.0
     */

    check_date_not_before(resolve, start_date, end_date) {
        const st = new Date(start_date);
        const ed = new Date(end_date);
        resolve({
            date_not_before: st < ed
        });
    }


    /**
     * Resolve if the input date is after the comparisive date.
     * 
     * @param {function} resolve    The Promise resolve function.
     * @param {string} start_date   The date that should first or older than end_date.
     * @param {string} end_date     The date that should be after, or newer than start_date.
     * 
     * @since   LRS 3.20.0
     */

    check_date_not_after(resolve, start_date, end_date) {
        const st = new Date(start_date);
        const ed = new Date(end_date);
        resolve({
            date_not_after: st < ed
        });
    }


    /**
     * Observe and check if a validation is complete and execute the set function.
     * 
     * @param {object} element The element being observed.
     * From `document.getElementById(id)`
     * @param {array} condition Passed as an array, the conditions to look out for
     * - 0: unset - usually if the value is blank. Should be passed as FLAG_UNSET.
     * - 1: passed - the validation passed. Should be passed as FLAG_PASSED.
     * - 2: failed - the validation failed. Should be passed as FLAG_FAILED.
     * @param {function} execute_function The function you wish to execute. 
     * Should be passed as `function () {
     *  myFunction();
     * }`
     * 
     * @static
     * 
     * @since   LRS 3.21.0
     */

    static execute_once_validation_complete(element, condition, execute_function) {
        let counter = 0;
        const callback = function (mutationsList, observer) {
            let execute = false;
            for (const mutation of mutationsList) {
                if (mutation.attributeName == 'data-validated') {
                    execute = true;
                    counter++;
                    if (counter != 1) {
                        return;
                    }
                }
            }
            if (!execute || counter != 1) {
                return;
            }
            if (condition.includes(element.dataset.validated)) {
                execute_function();
            }
        };
        observe(element, callback);
    }

}


/**
 * constant to indicate the flag is UNSET
 * 
 * @var {string}    FLAG_UNSET
 * 
 * @since   LRS 3.21.0
 * @since   LBF 0.1.1-beta
 */

export const FLAG_UNSET = '0';

/**
 * constant to indicate the flag is PASSED
 * 
 * @var {string}    FLAG_PASSED
 * 
 * @since   LRS 3.21.0
 * @since   LBF 0.1.1-beta
 */

export const FLAG_PASSED = '1';

/**
 * constant to indicate the flag is FAILED
 * 
 * @var {string}    FLAG_FAILED
 * 
 * @since   LRS 3.21.0
 * @since   LBF 0.1.1-beta
 */

export const FLAG_FAILED = '2';