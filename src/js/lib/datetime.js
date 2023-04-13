/**
 * A library of date time functions
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Return the month name when given a number input
 * 
 * @param {integer} m numer value of a month
 * 
 * @returns {string} month
 * 
 * @since   LRS 3.3.2
 * @since   LBF 0.1.1-beta
 */

export function return_month(m) {
    switch (m.toString()) {
        case '1':
            return 'January';
        case '2':
            return 'February';
        case '3':
            return 'March';
        case '4':
            return 'April';
        case '5':
            return 'May';
        case '6':
            return 'June';
        case '7':
            return 'July';
        case '8':
            return 'August';
        case '9':
            return 'September';
        case '10':
            return 'October';
        case '11':
            return 'November';
        case '12':
            return 'December';
    }
}


/**
 * Which day is being represented by a number value
 * 
 * @param {integer} dn numer representing the day of the week
 * 
 * @returns {string} weekDay
 * 
 * @since   LRS 3.3.2
 * @since   LBF 0.1.1-beta
 */

export function return_day(dn) {
    switch (dn) {
        case 0:
            return 'Sunday';
        case 1:
            return 'Monday';
        case 2:
            return 'Tuesday';
        case 3:
            return 'Wednesday';
        case 4:
            return 'Thursday';
        case 5:
            return 'Friday';
        case 6:
            return 'Saturday';
    }
}