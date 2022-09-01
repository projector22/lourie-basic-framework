/**
 * A library of tools for dealing with forms
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.13.0
 * @since   LBF 0.1.1-beta
 */


/**
 * Draw a text counter underneath a text area
 * 
 * @param {string} textarea_id  The id of the text area being examined
 * @param {string} text_id      The id of the div being drawn to
 * 
 * @since   3.8.0
 * @since   LBF 0.1.1-beta
 */

export function text_area_text_counter(textarea_id, text_id) {
    const text_area = document.getElementById(textarea_id);
    const draw_text = document.getElementById(text_id);
    if (text_area.value == '') {
        draw_text.innerHTML = '';
        return;
    }
    draw_text.innerHTML = text_area.value.length;
    if (text_area.maxLength !== -1) {
        draw_text.innerHTML += `/${text_area.maxLength}`;
    }
}


/**
 * Handle the changes of a multi column include / exclude table
 * 
 * @param {string} id The id of the element being handled
 * 
 * @since   3.14.3
 * @since   LBF 0.1.1-beta
 */

export function handle_column_changes(id) {
    const left_arrow = document.getElementById(`${id}_left_arrow`);
    left_arrow.onclick = function () {
        move_option_left(`${id}_included_list`, `${id}_excluded_list`);
    };
    const right_arrow = document.getElementById(`${id}_right_arrow`);
    right_arrow.onclick = function () {
        move_option_right(`${id}_included_list`, `${id}_excluded_list`);
    };

}


/**
 * Move unset registration class members into the register class
 * 
 * @param   string  left_id     The id of the left hand column
 * @param   string  right_id    The id of the right hand column
 * 
 * @since   3.14.3
 * @since   LBF 0.1.1-beta
 */

function move_option_left(left_id, right_id) {
    const left_members = document.getElementById(left_id);
    const right_members = document.getElementById(right_id);
    const hold = [];
    /**
     * If performing actions on the DOM while doing the initial iteration, unexpected
     * results occure, with some elements being left off.
     */
    for (const option of right_members) {
        if (option.selected) {
            hold.push(option);
        }
    }
    hold.forEach(option => {
        // Adding to one, automatically removes from the other
        left_members.add(option);
        option.selected = false;
    });
}


/**
 * Move registration class members out of the register class
 * 
 * @param   string  left_id     The id of the left hand column
 * @param   string  right_id    The id of the right hand column
 * 
 * @since   3.14.3
 * @since   LBF 0.1.1-beta
 */

function move_option_right(left_id, right_id) {
    const left_members = document.getElementById(left_id);
    const right_members = document.getElementById(right_id);
    const hold = [];
    /**
     * If performing actions on the DOM while doing the initial iteration, unexpected
     * results occure, with some elements being left off.
     */
    for (const option of left_members) {
        if (option.selected) {
            hold.push(option);
        }
    }
    hold.forEach(option => {
        // Adding to one, automatically removes from the other
        right_members.add(option);
        option.selected = false;
    });
}