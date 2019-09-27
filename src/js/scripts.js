/**
 * Load most of the used Javascript functions used by the site.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

/**
 * Replace string inbetween two defined indexes
 * 
 * @link    https://stackoverflow.com/questions/14880229/how-to-replace-a-substring-between-two-indices
 */

String.prototype.replaceBetween = function (start, end, what) {
    return this.substring(0, start) + what + this.substring(end);
};


/**
 * Gets constructed post data, performs an AJAX request and displays the responding generated text and
 * if desired, performs a redirect
 * 
 * @param {string} post         Formed string in the form of a POST request, something like 'p=page.php&token=example'
 * @param {string} responseId   The id of the page in which the AJAX can display responses
 * @param {string} redirect     Which page to redirect to. If blank, won't redirect - Default: ''
 * @param {string} responseMsg  A defined message on success, if not taken from the results of the AJAXed script - Default: ''
 * @param {string} page         Which page to send the AJAX request to - Default: 'src/actions
 * 
 * @version     1.0
 */

function execute_ajax_post(post, responseId = '', redirect = '', responseMsg = '', page = 'src/actions.php') {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (responseId != '') {
                if (responseMsg === '') {
                    document.getElementById(responseId).innerHTML = this.responseText;
                } else {
                    document.getElementById(responseId).innerHTML = responseMsg;
                } //if
            }
            //  else {
            //     if (responseMsg === '') {
            //         alert(this.responseText);
            //     } else {
            //         alert(responseMsg);
            //     }
            // }// Incase you need feedback

            if (redirect != '') {
                var hash = (redirect.split("#")[1] || '');
                window.location.href = redirect;
                if (hash != '') {
                    location.reload();
                    window.scrollTop = document.getElementsByClassName(hash).offsetTop;
                }
            }
        } //if
    };
    xmlhttp.open("POST", page, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(post);
}


/**
 * Gets constructed post data, performs an AJAX request specifically handling a file handling request
 * 
 * @param {string} post         Formed string in the form of a POST request, something like 'p=page.php&token=example'
 * @param {string} responseId   The id of the page in which the AJAX can display responses
 * @param {string} responseMsg  A defined message on success, if not taken from the results of the AJAXed script - Default: ''
 * @param {string} page         Which page to send the AJAX request to - Default: 'src/actions
 * 
 * @version     1.0
 */

function execute_ajax_file(post, responseId, responseMsg = '', page = 'src/actions.php') {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (responseMsg === '') {
                document.getElementById(responseId).innerHTML = this.responseText;
            } else {
                document.getElementById(responseId).innerHTML = responseMsg;
            } //if
        } //if
    };
    xmlhttp.open("POST", page, true);
    xmlhttp.send(post);
}


/**
 * Tests an email address for validity
 * 
 * @param {string} addr An email address to test
 * 
 * @return {boolean} Whether the string is an email address or not
 */

function check_valid_email(addr) {
    var regex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    return regex.test(String(addr).toLowerCase());
}




/**
 * Return the month name when given a number input
 * 
 * @param {integer} m numer value of a month
 * 
 * @returns {string} month
 */

function return_month(m) {
    switch (m) {
        case '1':
            month = 'January';
            break;
        case '2':
            month = 'February';
            break;
        case '3':
            month = 'March';
            break;
        case '4':
            month = 'April';
            break;
        case '5':
            month = 'May';
            break;
        case '6':
            month = 'June';
            break;
        case '7':
            month = 'July';
            break;
        case '8':
            month = 'August';
            break;
        case '9':
            month = 'September';
            break;
        case '10':
            month = 'October';
            break;
        case '11':
            month = 'November';
            break;
        case '12':
            month = 'December';
            break;
    } //switch
    return month;
}


/**
 * Which day is being represented by a number value
 * 
 * @param {integer} dn numer representing the day of the week
 * 
 * @returns {string} weekDay
 */

function return_day(dn) {
    switch (dn) {
        case 0:
            weekDay = 'Sunday';
            break;
        case 1:
            weekDay = 'Monday';
            break;
        case 2:
            weekDay = 'Tuesday';
            break;
        case 3:
            weekDay = 'Wednesday';
            break;
        case 4:
            weekDay = 'Thursday';
            break;
        case 5:
            weekDay = 'Friday';
            break;
        case 6:
            weekDay = 'Saturday';
            break;
    } //switch
    return weekDay;
}



/**
 * Get the GET data on a URL and add your own defined value to it, for example: http://example.com?p=sample -> http://example.com?p=sample&customitem=customvalue
 * @param {string} id       Custom id heading to be used - string or array
 * @param {string} order    Custom value to be used - string or array
 * 
 * @returns     The string with the custom item added - ?p=sample&customitem=customvalue
 * 
 * @version     1.0
 * @version     1.1     Added support for arrays
 */

/**
 * Usage example:
 * var date = document.getElementById('selected_date').value;
 * var paste = url_sub_search('selected_date', date);
 * window.location.search = paste;
 */

function url_sub_search(id, order) {
    var addon, obl, end, del, paste, i;
    addon = window.location.search.substr(0);
    paste = addon;
    if (Array.isArray(id) && Array.isArray(order)) {
        for (i = 0; i < id.length; i++) {
            obl = addon.indexOf(id[i]);
            if (obl > -1) {
                end = addon.indexOf('&', obl); //end is meant to be where the next & 
                if (end === -1) {
                    del = addon.substr(obl - 1, addon.length);
                } else {
                    del = addon.substr(obl - 1, end - obl + 1);
                }
                paste = paste.replace(del, '') + '&' + id[i] + '=' + order[i];
            } else {
                paste += '&' + id[i] + '=' + order[i];
            } //if
        } //for
    } else {
        obl = addon.indexOf(id);
        if (obl > -1) {
            end = addon.indexOf('&', obl); //end is meant to be where the next & 
            if (end === -1) {
                del = addon.substr(obl - 1, addon.length);
            } else {
                del = addon.substr(obl - 1, end - obl + 1);
            }
            paste = paste.replace(del, '') + '&' + id + '=' + order;
        } else {
            paste += '&' + id + '=' + order;
        }
    }
    return paste;
}