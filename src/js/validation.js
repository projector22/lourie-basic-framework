/**
 * A library of tools for dealing with the validation of fields
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Tests an email address for validity
 * 
 * @param {string} addr An email address to test
 * 
 * @return {boolean} Whether the string is an email address or not
 * 
 * @since   2.28.0
 * @since   LBF 0.1.1-beta
 */

export function check_valid_email(addr) {
    const regex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    const addrs = addr.split(',');
    if (addrs.constructor === Array) {
        for (let i = 0; i < addrs.length; i++) {
            if (!regex.test(String(addrs[i].trim()).toLowerCase())) {
                return false;
            }
        }
        return true;
    } else {
        return regex.test(String(addr).toLowerCase());
    }
}



/**
 * Test if a string input is a valid time
 * 
 * @param {string} time_test String to test if a valid test
 * 
 * @returns {boolean}
 * 
 * @since   3.6.3
 * @since   LBF 0.1.1-beta
 */

export function validate_time_input(time_test) {
    return time_test = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(time_test);
}


/**
 * Test if the input ID is a valid South African ID number
 * 
 * @param {string} id Tested ID.
 * 
 * @returns {boolean}
 * 
 * @see https://answers.acrobatusers.com/validate-south-african-id-q214753.aspx
 * 
 * @since   3.16.0
 * @since   LBF 0.1.1-beta
 */

export function validate_ZA_ID_number(id) {
    try {
        if (id.length != 13) {
            return false;
        }
        var y1o = id.substring(0, 1)
        var y2e = id.substring(1, 2)
        var m1o = id.substring(2, 3)
        var m2e = id.substring(3, 4)
        var d1o = id.substring(4, 5)
        var d2e = id.substring(5, 6)
        var go = id.substring(6, 7)
        var s1e = id.substring(7, 8)
        var s2o = id.substring(8, 9)
        var s3e = id.substring(9, 10)
        var co = id.substring(10, 11)
        var ae = id.substring(11, 12)
        var z = id.substring(12, 13)

        var A = 0;
        A = parseInt(A) + parseInt(y1o) + parseInt(m1o) + parseInt(d1o) + parseInt(go) + parseInt(s2o) + parseInt(co);
        var B1 = y2e + m2e + d2e + s1e + s3e + ae;
        var B2 = B1 * 2;
        var i = 0;
        var B3 = 0;
        var B2string = B2.toString();
        while (B2string.length > i) {
            var x = B2string.substring(i, i + 1);
            B3 = B3 + parseInt(x);
            i = i + 1;
        }

        var C = A + B3;
        var Cstring = C.toString();
        var secNo = Cstring.substring(1, 2);
        var D = 10 - parseInt(secNo);
        while (D >= 10) D = D - 10;
        if (D == z) {
            // test for valid date in 1900
            var valid = true;
            var bd = new Date('19' + y1o + y2e, parseInt(m1o + m2e) - 1, d1o + d2e);
            if ((bd == 'NaN') | (bd == 'Invalid Date')) valid = false;
            else if (bd.getFullYear() != parseInt('19' + y1o + y2e)) valid = false;
            else if (bd.getMonth() + 1 != parseInt(m1o + m2e)) valid = false;
            else if (bd.getDate() != parseInt(d1o + d2e)) valid = false;
            // if invalid test again in 2000
            valid = true;
            bd = new Date('20' + y1o + y2e, parseInt(m1o + m2e) - 1, d1o + d2e);
            if ((bd == 'NaN') | (bd == 'Invalid Date')) valid = false;
            else if (bd.getFullYear() != parseInt('20' + y1o + y2e)) valid = false;
            else if (bd.getMonth() + 1 != parseInt(m1o + m2e)) valid = false;
            else if (bd.getDate() != parseInt(d1o + d2e)) valid = false;
            // return result
            return valid;
        } else {
            return false;
        }
    } catch (e) {
        return false;
    }
}


/**
 * Test if the date input is valid
 * 
 * @param {string} date_test Date to test
 * 
 * @returns {boolean}
 * 
 * @since   3.26.2
 * @since   LBF 0.1.1-beta
 */

export function validate_date_input(date_test) {
    return !isNaN(Date.parse(date_test))
}