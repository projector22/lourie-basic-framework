/**
 * Calculates and draws the daily routine element on the screen, and keeps it up to date.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.19.0
 * @since   LBF 0.1.1-beta
 * 
 * @version 1.0
 */


/**
 * Handle drawn spreadsheets as and when required.
 * 
 * @param {string} id   The id of the table being worked on.
 * 
 * @since   LRS 3.19.0
 * @since   LBF 0.1.1-beta
 */
export default class SpreadsheetTool {
    constructor(id) {

        /**
         * The id of the spreadsheet being worked on.
         * 
         * @var {string} id
         * 
         * @since   LRS 3.19.0
         */
        this.id = id;

        /**
         * A dom list of all of the cells within the spreadsheet.
         * Created from calling document.getElementsByName.
         * 
         * @var {object} cells
         * 
         * @since   LRS 3.19.0
         */
        this.cells = document.getElementsByName(`${this.id}__spreadsheet_cell`);

        /**
         * Disable if the contents are empty, something common if on a an AJAX load, for example
         */
        if (this.cells.length == 0) {
            return;
        }

        /**
         * The last column in the current spreadsheet.
         * 
         * @var {integer} last_column
         * 
         * @since   LRS 3.19.0
         */
        this.last_column = this.cells[this.cells.length - 1].dataset.column;

        /**
         * The last row in the current spreadsheet.
         * 
         * @var {integer} last_row
         * 
         * @since   LRS 3.19.0
         */
        this.last_row = this.cells[this.cells.length - 1].dataset.row;

        /**
         * The canvas element.
         * 
         * @var {DOM} canvas
         * 
         * @since   LRS 3.23.2
         */
        this.canvas = document.getElementById(`${this.id}__canvas`);

        /**
         * The current cell being focused on. It has 2 properties -> column & row.
         * 
         * @var {object} focused
         * 
         * @since   LRS 3.19.0
         */
        this.focus = {
            column: null,
            row: null,
        };

        this.listener();
        this.selector();

    }


    /**
     * Perform all of the onExample actions required by the spreadsheet.
     * 
     * @since   LRS 3.19.0
     */

    listener() {
        if (this.cells.length == 0) {
            return;
        }
        const current_object = this;
        this.cells.forEach(cell => {
            cell.onfocus = function () {
                // Does not appear to have a purpose yet
                current_object.focus = {
                    column: this.dataset.column,
                    row: this.dataset.row,
                };
            };
            cell.onkeydown = function (action) {
                let id;
                switch (action.key) {
                    case 'ArrowUp': // Move up
                        id = `${current_object.id}__row_${eval(this.dataset.row) - 1}__column_${this.dataset.column}`;
                        if (document.getElementById(id)) {
                            document.getElementById(id).focus();
                        }
                        break;
                    case 'ArrowDown': // Move down
                        id = `${current_object.id}__row_${eval(this.dataset.row) + 1}__column_${this.dataset.column}`;
                        if (document.getElementById(id)) {
                            document.getElementById(id).focus();
                        }
                        break;
                    case 'ArrowRight': // Move right
                        id = `${current_object.id}__row_${this.dataset.row}__column_${eval(this.dataset.column) + 1}`;
                        if (document.getElementById(id)) {
                            document.getElementById(id).focus();
                        }
                        break;
                    case 'ArrowLeft': // Move left
                        id = `${current_object.id}__row_${this.dataset.row}__column_${eval(this.dataset.column) - 1}`;
                        if (document.getElementById(id)) {
                            document.getElementById(id).focus();
                        }
                        break;
                    case 'Enter': // confirm and down one
                        id = `${current_object.id}__row_${eval(this.dataset.row) + 1}__column_${this.dataset.column}`;
                        if (document.getElementById(id)) {
                            document.getElementById(id).focus();
                        }
                        break;
                    case 'Escape': // Cancel action
                        this.value = ''; // @todo Must functionally go back to the origonal text if there was one
                        break;
                }
            };
        });
    }


    /**
     * Observe if a cell is being selected and highlight as needed.
     * 
     * @since   LRS 3.23.2
     */

    selector() {
        return; // WIP for the future.
        let start_cell, end_cell;
        const cells = document.querySelectorAll(`.${this.id}__spreadsheet_cell`);
        const selection_frame = document.querySelector(`.${this.id}__selection_window`);
        const cid = this.id;
        let pageX, pageY;

        let moveX, moveY;
        this.canvas.onmousemove = function (event) {
            // console.log(event)
            if (event.movementY > 0) {
                moveY = 1; // Below Element
            } else if (event.movementY < 0) {
                moveY = -1; // Above Element
            } else {
                moveY = 0;
            }
        };
        cells.forEach(cell => {
            cell.addEventListener("mousemove", function (event) {
                if (event.buttons == 1) { // Mouse is left clicked
                    const positions = this.getBoundingClientRect();
                    console.log(this.id);
                    switch (moveY) {
                        case 1: // down
                            if (pageY > event.pageY) {
                                // Remove cover
                                console.log('Above moving down');
                            } else if (pageY < event.pageY) {
                                // Add cover
                                console.log('Below moving down');
                                selection_frame.style.height = parseInt(selection_frame.dataset.height) + parseInt(this.clientHeight) + 1 + 'px';
                                selection_frame.dataset.height = parseInt(selection_frame.dataset.height) + parseInt(this.clientHeight) + 1;
                            }
                            break;
                        case -1: // up
                            if (pageY > event.pageY) {
                                // Add cover
                                console.log('Above moving up');
                            } else if (pageY < event.pageY) {
                                // Remove cover
                                console.log('Below moving up');
                            }
                            break;
                    }


                    // 
                    // console.table({
                    //     Height: this.clientHeight + 'px',
                    //     Width: this.clientWidth + 'px',
                    //     Top: positions.top + 'px',
                    //     Left: positions.left + 'px',
                    //     Right: positions.right + 'px',
                    //     Bottom: positions.bottom + 'px',
                    // });
                    // console.log("TOP:", positions.top);
                    // console.log("START:", selection_frame.dataset.startHeight);
                    // console.log(event.movementY);
                    // if (event.movementY > 0) {
                    //     // Move down
                    //     // console.log('down')
                    //     selection_frame.style.height = parseInt(selection_frame.dataset.height) + parseInt(this.clientHeight) + 1 + 'px';
                    //     selection_frame.dataset.height = parseInt(selection_frame.dataset.height) + parseInt(this.clientHeight) + 1;
                    // } else if (event.movementY < 0) {
                    //     // Move up
                    //     // console.log('UP')
                    //     if (positions.top < selection_frame.dataset.startHeight) {
                    //         selection_frame.style.top = positions.top + 'px';
                    //     }
                    //     selection_frame.style.height = parseInt(selection_frame.dataset.height) + parseInt(this.clientHeight) + 1 + 'px';
                    //     selection_frame.dataset.height = parseInt(selection_frame.dataset.height) + parseInt(this.clientHeight) + 1;
                    // }


                    // console.log(Math.sign(parseInt(selection_frame.dataset.height) - parseInt(this.clientHeight)));
                    // console.log(selection_frame.style.height);
                    // selection_frame.style.width = this.clientWidth;
                }
            });
            cell.addEventListener("mousedown", function (event) {
                const positions = this.getBoundingClientRect();
                start_cell = this;
                pageY = event.pageY;
                // mouse_is_down = true;
                // console.log('MOUSE DOWN STARTED');
                // clear_all_selected(cid);
                selection_frame.style.display = 'block';
                selection_frame.style.position = 'absolute';
                selection_frame.style.top = positions.top + 'px';
                selection_frame.style.left = positions.left + 'px';
                selection_frame.style.height = this.clientHeight + 'px';
                selection_frame.style.width = this.clientWidth + 'px';
                selection_frame.dataset.startPosition = positions.top;
                selection_frame.dataset.height = this.clientHeight;
                selection_frame.dataset.width = this.clientWidth;
            });
            cell.addEventListener('mouseup', function () {
                // console.log('MOUSE IS UP');
                // end_cell = this;
                // mouse_is_down = false;
                // selection_frame.style.display = 'none';

                // const start_row = start_cell.dataset.row;
                // const start_col = start_cell.dataset.column;
                // const end_row = end_cell.dataset.row;
                // const end_col = end_cell.dataset.column;

                // console.table({
                //     'start_row': start_row,
                //     'end_row': end_row,
                //     'start_col': start_col,
                //     'end_col': end_col,
                // })

                // const row_dif = end_row - start_row;
                // const col_dif = end_col - start_col;

                // for (let col = start_col; col <= end_col; col++) {
                //     for (let row = start_row; row <= end_row; row++) {
                //         const sel_cell = document.getElementById(`${cid}__row_${row}__column_${col}`);
                //         // console.log(sel_cell);
                //         sel_cell.classList.add(`${cid}__cell_selected`);
                //         sel_cell.dataset.selected = 1;
                //     }
                // }


                // start_cell.classList.add(`${cid}__cell_selected`);
                // console.log("Start:", start_cell.id);
                // console.log("End:", end_cell.id);
            });
        });
    }

}


/**
 * Clear all the selected cells on the spreadsheet.
 * 
 * @param {string} id ID of the spreadsheet.
 * 
 * @since   LRS 3.23.2
 * @since   LBF 0.1.1-beta
 */

function clear_all_selected(id) {
    const cells = document.querySelectorAll(`.${id}__cell_selected`);
    console.log(cells.length);
    cells.forEach(cell => {
        cell.classList.remove(`${id}__cell_selected`);
        cell.classList.remove(`${id}__cell_selected-top`);
        cell.classList.remove(`${id}__cell_selected-bottom`);
        cell.classList.remove(`${id}__cell_selected-left`);
        cell.classList.remove(`${id}__cell_selected-right`);
        cell.dataset.selected = 0;
    });
    console.log('cleared');
}