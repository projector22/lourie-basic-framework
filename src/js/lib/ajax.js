/**
 * Class to perform AJAX instructions.
 * 
 * DEV NOTE - Don't use class private #method as this is not supported on iOS Safari
 * @see https://stackoverflow.com/questions/67402212/javascript-private-class-fields-or-methods-not-working-in-ios-chrome-or-safari
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */

import loading_animation from './loading.js';
import {
    TOPBAR_RESPONSE,
    TOPBAR_STRING_THROWER,
    ALERT_STRING_THROWER,
    ERROR_STRING_THROWER
} from './responses.js';
import { character_count } from './tools.js';

/**
 * Object class for performing an AJAX execution.
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */

export default class Ajax {

    /**
     * Class constructor
     * 
     * @since   LRS 3.13.0
     * @since   LBF 0.1.1-beta
     */

    constructor() {

        /**
         * For sending post data.
         * 
         * @var {string} post
         * 
         * @since   LRS 3.13.0
         */

        this.post = null;


        /**
         * The id of the element on the page to feed back app response.
         * 
         * @var {string} response
         * 
         * @since   LRS 3.13.0
         */

        this.response = null;


        /**
         * The url to redirect the page after the ajax is complete.
         * 
         * @var {string} redirect
         * 
         * @since   LRS 3.13.0
         */

        this.redirect = null;


        /**
         * An overwriting response message if desired.
         * 
         * @var {string} response_message
         * 
         * @since   LRS 3.13.0
         */

        this.response_message = null;


        /**
         * The actions page to direct ajax requests to, by default '/actions'.
         * 
         * @var {string} action_page
         * 
         * @since   LRS 3.13.0
         */

        this.action_page = '/actions';


        /**
         * Whether or not the ajax request should include $_FILES being uploaded.
         * 
         * @var {boolean} post_file
         * 
         * @since   LRS 3.13.0
         */

        this.post_file = false;


        /**
         * The functions which should be executed in a recursive manor required for executing `this.synchronous_execute()`
         * Contents should be as following:
         * ```js
         * const ajax = new Ajax;
         * const funct_one = function () {
         *   ajax.post = 'example';
         *   // ... etc
         * };
         * const funct_two = function () {
         *   ajax.post = 'another_example';
         *   // ... etc
         * };
         * ajax.recursive_functions = [funct_one, funct_two];
         * ```
         * 
         * @var {array|null} recursive_functions    Array of functions. Default: null
         * 
         * @since   LRS 3.13.4
         */

        this.recursive_functions = null;


        /**
         * Record the feedback from the ajax call. This allows to set break points based
         * on the results as well as to set choices based on results.
         * 
         * @var {string} text_feedback
         * 
         * @since   LRS 3.13.4
         */

        this.text_feedback = '';

        /**
         * Whether or not to termitate execution when doing recursive AJAX functions.
         * 
         * @var {string} terminate_execution    Default: false
         * 
         * @since   LRS 3.13.4
         */

        this.terminate_execution = false;

        /**
         * Termination feedback text to send to the user
         * 
         * @var {string} termination_text
         * 
         * @since   LRS 3.13.4
         */

        this.termination_text = '';

        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            this.xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        /**
         * How long to wait before timing out the execution.
         * Set to null to bypass.
         * 
         * @var {string} timeout_duration. Default: 15 seconds.
         * 
         * @since   LRS 3.17.0
         */

        this.timeout_duration = 15000;


        /**
         * Whether or not the output goes to the terminal.
         * 
         * @var {boolean} output_to_terminal Default: false
         * 
         * @since   LRS 3.27.0
         */

        this.output_to_terminal = false;


        /**
         * The id of the element that is the terminal.
         * 
         * @var {string} terminal_id    Default: 'feedback_console'
         * 
         * @since   LRS 3.27.0
         */

        this.terminal_id = 'feedback_console';
    }


    /**
     * Gets constructed post data, performs an AJAX request and displays the responding generated text and
     * if desired, performs a redirect
     * 
     * @since   LRS 2.27.0
     * @since   LRS 2.27.2  Added support for AJAXing file uploading
     * @since   LRS 3.13.0  Amalgamated into a class
     */

    execute() {
        if (this.post == null) {
            throw "this.post not defined";
        }

        const response_id = this.output_to_terminal ? this.terminal_id : this.response;
        const static_response_message = this.response_message;
        let redirect = this.redirect;
        const current_object = this;

        let xmlHttpTimeout;
        if (this.timeout_duration !== null) {
            xmlHttpTimeout = setTimeout(function () {
                if (current_object.xmlhttp.readyState !== 4 || current_object.xmlhttp.status !== 200) {
                    current_object.xmlhttp.abort();
                    current_object.clear_loading();
                    alert("Requested action has timed out.");
                }
            }, current_object.timeout_duration);
        }

        this.xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 500) {
                if (this.responseText != '') {
                    console.warn(this.responseText);
                }
                if (xmlHttpTimeout) {
                    clearTimeout(xmlHttpTimeout);
                }
                this.abort();
                current_object.clear_loading();
            }

            if (this.readyState == 4 && this.status == 200) {
                current_object.clear_loading();
                let returned_response = this.responseText;
                if (returned_response.indexOf(ERROR_STRING_THROWER) == 0) {
                    redirect = null;
                    returned_response = returned_response.replace(ERROR_STRING_THROWER, '');
                }
                if (static_response_message == null) {
                    handle_ajax_response(response_id, returned_response);
                } else {
                    handle_ajax_response(response_id, static_response_message);
                }
                check_redirect(redirect);
            }
        };

        if (this.post_file) {
            this.file_execute();
        } else {
            this.standard_execute();
        }
    }


    /**
     * Perform multiple synchronous recursive AJAX executions. Each execution waits for the
     * previous AJAX call to finish before doing the next one.
     * 
     * @since   LRS 3.13.4
     */

    synchronous_execute() {
        /**
         * Check recursive_functions are set
         */
        if (this.recursive_functions == null) {
            throw "ajax.recursive_functions not set";
        }
        /**
         * Break the recursion if all functions have been executed.
         */
        if (this.recursive_functions.length == 0) {
            this.clear_loading();
            return;
        }
        /**
         * A custom terminator, based on the results of the previous execution
         */
        if (this.terminate_execution) {
            return;
        }
        /**
         * Execute the first function in the array
         */
        this.recursive_functions[0]();
        this.xmlhttp.onreadystatechange = synchronous_callback(this);
        if (!this.terminate_execution) {
            if (this.post_file) {
                this.file_execute();
            } else {
                this.standard_execute();
            }
        } else {
            this.set_message(this.termination_text, true, true);
        }
    }


    /**
     * Asyncronously resolve a promise with the reponse from an AJAX execution.
     * 
     * @param {function} resolve The Promise object resolver function
     * 
     * @since   LRS 3.16.0
     */


    execute_promise_resolve(resolve) {
        if (this.post == null) {
            throw "this.post not defined";
        }

        const origonal_class = this;
        this.xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                try {
                    resolve(JSON.parse(this.response));
                } catch {
                    const response_id = this.output_to_terminal ? this.terminal_id : origonal_class.response_id;
                    handle_ajax_response(response_id, this.response);
                }
            }
        };

        if (this.post_file) {
            this.file_execute();
        } else {
            this.standard_execute();
        }
    }


    /**
     * Execute a standard AJAX send request.
     * 
     * @since   LRS 3.13.4
     */

    standard_execute() {
        if (location.pathname == '/') {
            this.post += `&route_token=index`;
        } else {
            this.post += `&route_token=${location.pathname.split('/')[1]}`;
        }
        this.check_padding();
        this.xmlhttp.open("POST", this.action_page, true);
        this.xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        this.xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        this.xmlhttp.send(this.post);
    }


    /**
     * Execute an AJAX request with a file upload
     * 
     * @since   LRS 3.13.4
     */

    file_execute() {
        if (location.pathname == '/') {
            this.post.append('route_token', 'index');
        } else {
            this.post.append('route_token', location.pathname.split('/')[1]);
        }
        this.check_padding();
        this.xmlhttp.open("POST", this.action_page, true);
        this.xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        this.xmlhttp.send(this.post);
    }

    /**
     * Check if any padding (../) is required on this.action_page.
     * 
     * @since   LRS 3.17.0
     */

    check_padding() {
        const slash_count = character_count(window.location.href, '/')
        if (slash_count > 1) {
            let padding = '';
            for (let i = 0; i < slash_count; i++) {
                padding += '../';
            }
            this.action_page = padding + this.action_page;
        }
    }


    /**
     * Add a field=value param to this.post
     * 
     * @param {string} field The field of the param to be added to this.post
     * @param {string} value The value of the param to be added to this.post
     * 
     * @since   LRS 3.17.0
     */

    add_param(field, value) {
        if (this.post_file) {
            if (this.post == null) {
                this.post = new FormData();
            }
            this.post.append(field, value);
        } else {
            if (this.post == null) {
                this.post = `${field}=${value}`;
            } else {
                this.post += `&${field}=${value}`;
            }
        }
    }


    /**
     * Add a token to this.post.
     * 
     * @param {string} value The token you wish to add
     * 
     * @since   LRS 3.18.0
     */

    add_token(value) {
        this.add_param('token', value);
    }


    /**
     * Clear all posting params.
     * 
     * @since   LRS 3.28.0
     */

    clear_params() {
        if (this.post_file) {
            this.post = new FormData();
        } else {
            this.post = null;
        }
    }


    /**
     * Draw the loading animation in the response element
     * 
     * @param {boolean} load_in_topbar  Whether or not to force the loading animation in the topbar.
     *                                  Default: false
     * 
     * @since   LRS 3.13.0
     */

    loading(load_in_topbar = false) {
        if (load_in_topbar) {
            document.getElementById(TOPBAR_RESPONSE).innerHTML = loading_animation();
        } else if (this.output_to_terminal) {
            document.getElementById(this.terminal_id).innerHTML += loading_animation(true);
        } else {
            if (this.response == null) {
                return;
            }
            document.getElementById(this.response).innerHTML = loading_animation();
        }
    }


    /**
     * Set a message in the response window
     * 
     * @param {string}  message       The message to draw into a response window. Default: ''
     * @param {boolean} append        Whether or not to append the text onto the existing content or overwrite the content.
     * @param {boolean} clear_loading Whether or not to clear the loading animation
     * 
     * @since   LRS 3.13.0
     * @since   LRS 3.25.0  Added handling for sending to alert and sending to topbar.
     * 
     * @todo    See if this can be merged with `handle_ajax_response`.  3.27.0
     */

    set_message(message = '', append = false, clear_loading = false) {
        if (clear_loading) {
            this.clear_loading();
        }
        if (message.indexOf(TOPBAR_STRING_THROWER) == 0) {
            document.getElementById(TOPBAR_RESPONSE).innerHTML = message.replace(TOPBAR_STRING_THROWER, '');
        } else if (message.indexOf(ALERT_STRING_THROWER) == 0) {
            alert(message.replace(ALERT_STRING_THROWER, ''));
        } else {
            let response;
            if (this.output_to_terminal) {
                append = true;
                response = this.terminal_id;
            } else {
                response = this.response;
            }
            if (response == null) {
                return;
            }
            if (append) {
                document.getElementById(response).innerHTML += message;
            } else {
                document.getElementById(response).innerHTML = message;
            }
        }
    }


    /**
     * Set an alert message
     * 
     * @param {string} message        The message to draw into the alert box. Default: ''
     * @param {boolean} clear_loading Whether or not to clear the loading animation
     * 
     * @since   LRS 3.17.0
     */

    set_alert(message = '', clear_loading = false) {
        if (clear_loading) {
            this.clear_loading();
        }
        alert(message);
    }


    /**
     * Empty the response area's content
     * 
     * @since   LRS 3.13.4
     */

    clear_response() {
        if (this.output_to_terminal) {
            document.getElementById(this.terminal_id).innerHTML = '';
        } else {
            document.getElementById(this.response).innerHTML = '';
        }
    }


    /**
     * Clear the loading animation from response
     * 
     * @since   LRS 3.13.4
     */

    clear_loading() {
        if (this.output_to_terminal) {
            document.getElementById(this.terminal_id).innerHTML = document.getElementById(this.terminal_id).innerHTML.replace(loading_animation(true), '');
        } else if (document.getElementById(this.response)) {
            document.getElementById(this.response).innerHTML = document.getElementById(this.response).innerHTML.replace(loading_animation(), '');
        }
    }


    /**
     * Clear out the terminal window.
     * 
     * @since   LRS 3.27.0
     */

    clear_terminal() {
        const terminal = document.getElementById(this.terminal_id);
        if (!terminal) {
            return;
        }
        if (terminal.dataset.defaultHeading !== undefined) {
            terminal.innerHTML = terminal.dataset.defaultHeading;
        } else {
            terminal.innerHTML = '';
        }
    }


    /**
     * Search the response text to see if it contains a string.
     * 
     * @param {string} search 
     * 
     * @returns {boolean}
     * 
     * @since   LRS 3.13.4
     */

    search_feedback(search) {
        return this.text_feedback.indexOf(search) > -1;
    }


    /**
     * Handle the drawing of a progress bar when uploading files.
     * 
     * @see https://codepen.io/PerfectIsShit/pen/zogMXP
     * @see http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
     * 
     * @param {string} progress_bar_id ID of the progress bar.
     * 
     * @since   LRS 3.25.1
     */

    set_progress_bar(progress_bar_id) {
        this.xmlhttp.upload.addEventListener("progress", progress_handler, false);

        /**
         * Change the progress bar.
         * 
         * @param {object} event The change event.
         * 
         * @since   LRS 3.25.1
         */

        function progress_handler(event) {
            const percent = (event.loaded / event.total) * 100;
            const progress_bar = document.getElementById(progress_bar_id);
            progress_bar.value = Math.round(percent)

        }
    }


    /**
     * Set the output to or from the feedback terminal as desired.
     * 
     * @param {boolean} set Parse true to output to the terminal.
     * @param {string} terminal_id  Default: 'feedback_console'
     * 
     * @since   LRS 3.27.0
     */

    set_output_to_terminal(set, terminal_id = 'feedback_console') {
        this.output_to_terminal = set;
        this.terminal_id = terminal_id;
    }

}


/**
 * Handle any response text coming back from an AJAX request.
 * 
 * If a response message starts with a TOPBAR_STRING_THROWER This indicates the response should go to
 * the topbar response area rather than on the page
 * 
 * @param   {string}    response_id         The id of the page in which the AJAX can display responses.
 * @param   {string}    text                The text to draw into the response_id element.
 * 
 * @since   LRS 3.9.0
 * @since   LRS 3.13.0    Moved to ajax.js, removed param responseMsg and revamped
 * @since   LBF 0.1.1-beta
 */

function handle_ajax_response(response_id, text) {
    if (text.indexOf(TOPBAR_STRING_THROWER) == 0) {
        document.getElementById(TOPBAR_RESPONSE).innerHTML = text.replace(TOPBAR_STRING_THROWER, '');
        if (document.getElementById(response_id)) {
            document.getElementById(response_id).innerHTML = '';
        }
    } else if (text.indexOf(ALERT_STRING_THROWER) == 0) {
        alert(text.replace(ALERT_STRING_THROWER, ''));
        if (document.getElementById(response_id)) {
            document.getElementById(response_id).innerHTML = '';
        }
    } else {
        if (response_id == null) {
            return;
        }
        document.getElementById(response_id).innerHTML = text;
    }
}


/**
 * Perform the page reload if a redirect has been commanded
 * 
 * @param {string} loc The location to reload to
 * 
 * @since   LRS 3.6.0
 * @since   LBF 0.1.1-beta
 */

function check_redirect(loc) {
    if (loc == null) {
        return;
    }
    if (loc == 'reload') {
        location.reload();
    } else {
        const hash = (loc.split("f#")[1] || '');
        window.location.href = loc;
        if (hash != '') {
            location.reload();
            window.scrollTop = document.getElementsByClassName(hash).offsetTop;
        }
    }
}


/**
 * Handles the AJAX passing when multiple functions need to be worked on. It waits for the 
 * correct AJAX status and removes the function that has just been executed from the list. Finally
 * it calles the synchronous_execute() method, which is executing recursively.
 * 
 * @param {object} xhttp The Ajax object being worked on
 * 
 * @returns {function}
 * 
 * @since   LRS 3.13.4
 * @since   LBF 0.1.1-beta
 */

function synchronous_callback(xhttp) {
    return function () {
        if (this.readyState == 4 && this.status == 200) {
            xhttp.text_feedback = this.responseText;
            xhttp.set_message(this.responseText, true, true);
            xhttp.recursive_functions.shift();
            xhttp.synchronous_execute();
        }
    }
}