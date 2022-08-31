/**
 * A library of tools for dealing with table filtering and tables in general
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.13.0
 * @since   LBF 0.1.1-beta
 */

/**
 * Object class for performing table filtering.
 * 
 * @since   3.13.0
 * @since   3.15.5  Reworked for updated tables.
 */

export default class Table_Filter {

    /**
     * Object class for performing table filtering.
     * 
     * @param {string} table_id Parse the table id when the object is constructed
     * 
     * @since   3.15.5
     */

    constructor(table_id = null) {

        /**
         * The search string or term
         * 
         * @var {string} search
         * 
         * @since   3.13.0
         */

        this.search = null;


        /**
         * The id of the table, used as a prefext id for each row
         * 
         * @var {string} table_id
         * 
         * @since   3.13.0
         * @since   3.15.5  Renamed from row_id to table_id
         */

        this.table_id = table_id;


        /**
         * The array of sorted list data
         * 
         * @var {array} sorted_list
         * 
         * @since   3.15.5
         */

        this.sorted_list = [];
    }


    /**
     * Perform a filter of the table. This method is called when filtering from a text field or drop list etc.
     * 
     * @param {object} properties The set data on which the filter is based
     * 
     * ### Possible properties of the parsed properties object
     * @property {string}  search - default: ''
     * @property {array}   key - default: []
     * @property {boolean} include_multiselect - default: true
     * @property {string}  empty - default: ''
     * @property {boolean} search_not_empty - default: false
     * 
     * @since   3.15.5
     */

    perform_table_filter(properties = {}) {

        /**
         * Required properties are all the default properties that may be parsed during a table filter
         * 
         * @property {string}  search               What the user is searching for, often from this.value.
         *                                          Default: ''
         * @property {array}   key                  The columns to search, listed in an array.
         *                                          Default: []
         * @property {boolean} include_multiselect  Whether or not to include the multiselect call.
         *                                          Default: true
         * @property {string}  empty                The default 'empty' value. What the value is when there is no search.
         *                                          Default: ''
         * @property {boolean} search_not_empty     Search if the column is not empty rather than a specific value
         *                                          Default: false
         */

        const required_properties = {
            search: '',
            key: [],
            include_multiselect: true,
            empty: '',
            search_not_empty: false
        }

        // Check each of the properties of properties object. Fill any missing.
        for (const [key, value] of Object.entries(required_properties)) {
            if (!(key in properties)) {
                properties[key] = value;
            }
        }

        this.search = properties.search;
        if (this.search == properties.empty) {
            this.clear_filter();
            return;
        }

        if (properties.search_not_empty) {
            this.filter_not_empty(properties.key).draw_filtered_list();
        } else {
            this.filter_table(properties.key).draw_filtered_list();
        }

        if (properties.include_multiselect) {
            this.shift_multiselect();
        }
    }


    /**
     * Perform filtering checking if the value is blank or not
     * 
     * @param {array} search_columns The columns to search
     * 
     * @returns {object}
     * 
     * @since   3.15.5
     */

    filter_not_empty(search_columns = []) {
        if (this.table_id == null) {
            throw "Table ID has not been set";
        }

        this.show_filtered_checkbox();

        const filter_data = this.get_filter_data(search_columns);

        const sorted_list = [];

        for (let i = 0; i < filter_data.length; i++) {
            const hold = filter_data[i].filter(function (item) {
                // Uppercase conversion happens here so as to not compromise the origonal data
                return item !== '';
            });
            if (hold.length > 0) {
                const line = document.getElementById(`${this.table_id}--row_${i}`);
                sorted_list.push(line.cloneNode(true));
            }
        }
        this.sorted_list = sorted_list;
        return this;
    }


    /**
     * Perform the basic filtering of the table
     * 
     * @param {array} search_columns The columns to search
     * 
     * @returns {object}
     * 
     * @since   3.15.5
     */


    filter_table(search_columns = []) {
        if (this.search == null) {
            throw "Search has not been set";
        }
        if (this.table_id == null) {
            throw "Table ID has not been set";
        }

        this.show_filtered_checkbox();

        const filter_data = this.get_filter_data(search_columns);

        const search = this.search;
        const sorted_list = [];

        for (let i = 0; i < filter_data.length; i++) {
            const hold = filter_data[i].filter(function (item) {
                // Uppercase conversion happens here so as to not compromise the origonal data
                return item.toUpperCase().indexOf(search.toUpperCase()) !== -1;
            });
            if (hold.length > 0) {
                const line = document.getElementById(`${this.table_id}--row_${i}`);
                sorted_list.push(line.cloneNode(true));
            }
        }
        this.sorted_list = sorted_list;
        return this;
    }


    /**
     * Get all the values of the cells that is to be searched
     * 
     * @param {array} search_columns    The columns to search.
     * 
     * @returns {array}
     * 
     * @since   3.15.5
     */

    get_filter_data(search_columns) {
        let i = 0;
        const filter_data = [];
        while (document.getElementById(`${this.table_id}--row_${i}`)) {
            const row = `${this.table_id}--row_${i}`;
            const row_data = [];
            search_columns.forEach(entry => {
                const cell = document.getElementById(`${row}--cell_${entry}`);
                switch (cell.nodeName) {
                    case 'TD':
                        row_data.push(cell.innerHTML);
                        break;
                    case 'INPUT':
                        row_data.push(cell.value);
                        break;
                }
            });
            filter_data.push(row_data);
            i++;
        }
        return filter_data;
    }


    /**
     * Draw the filtered table to the screen.
     * 
     * @since   3.15.5
     */

    draw_filtered_list() {
        document.getElementById(`${this.table_id}_unfiltered_body`).classList.add('hidden');
        const filtered_body = document.getElementById(`${this.table_id}_filtered_body`);
        filtered_body.innerHTML = '';
        const table_id = this.table_id;
        this.sorted_list.forEach(function (entry, index) {
            entry.setAttribute('data-origonalId', entry.id);
            const id = `${table_id}--row_${index}`;
            entry.id = `${id}--filtered`;
            const cells = entry.children;
            for (let i = 0; i < cells.length; i++) {
                cells[i].id = `${id}--cell_${i}--filtered`;
            }
            filtered_body.appendChild(entry);
        });
    }


    /**
     * Clear the filter if the filter has been removed or is empty.
     * 
     * @since   3.15.5
     */

    clear_filter() {
        document.getElementById(`${this.table_id}_unfiltered_body`).classList.remove('hidden');
        document.getElementById(`${this.table_id}_filtered_body`).innerHTML = '';
        if (document.getElementById(`${this.table_id}_select_all`)) {
            document.getElementById(`${this.table_id}_select_all`).classList.remove('hidden');
            document.getElementById(`${this.table_id}_select_all_filtered`).classList.add('hidden');
        }
    }


    /**
     * If there is a selectall checkbox, swap it with the filtered selectall checkbox
     * 
     * @since   3.15.5
     */

    show_filtered_checkbox() {
        if (document.getElementById(`${this.table_id}_select_all`)) {
            document.getElementById(`${this.table_id}_select_all`).classList.add('hidden');
            document.getElementById(`${this.table_id}_select_all_filtered`).classList.remove('hidden');
        }
    }


    /**
     * Select multiple checkboxes using the shift key
     * 
     * @since 3.6.4
     */

    shift_multiselect() {
        // Run through all the checkboxes and add an "onClick" event to them to handle SHIFT-CLICK
        const checkboxes = document.querySelectorAll(".selectable_checkbox");
        const allCheckboxes = [];
        let lastChecked;
        for (let i = 0; i < checkboxes.length; i++) {
            allCheckboxes.push(checkboxes[i]);
            checkboxes[i].onclick = function (e) {
                if (!lastChecked) {
                    lastChecked = this;
                    return;
                }
                if (e.shiftKey) {
                    const startTemp = allCheckboxes.indexOf(this);
                    const endTemp = allCheckboxes.indexOf(lastChecked);
                    const start = Math.min(startTemp, endTemp);
                    const end = Math.max(startTemp, endTemp);
                    for (let i = start; i <= end; i++) {
                        allCheckboxes[i].checked = lastChecked.checked;
                    }
                }
                lastChecked = this;
            }; // end the custom "onclick" function for this check box
        } // run through all the check boxes on the page
    }


    /**
     * 
     * @param {string}       action   The action to perform
     * @param {string|array} id       The id of the element to be changed,
     *                                Can be parsed as a string - the id directly
     *                                Can be parsed as an array - the row and column.
     * @param {boolean} filtered      Whether the entry being changed is in the fildered list.
     * 
     * @since   3.15.5
     */

    change_entry_status(action, id, filtered = false) {
        let cell_id;
        if (Array.isArray(id)) {
            // id is array
            cell_id = `${this.table_id}--row_${id[0]}--cell_${id[1]}`;
            if (filtered) {
                cell_id += '--filtered';
            }
        } else {
            // id is string
            cell_id = id;
            if (filtered && id.indexOf('--filtered') !== -1) {
                cell_id += '--filtered';
            }
        }
        const cell = document.getElementById(cell_id);
        switch (action) {
            case 'a':
                // Set Archived
                cell.innerHTML = 'Archived';
                break;
            case 'c':
                // Set Current
                cell.innerHTML = 'Current';
                break;
            case 'h':
                // Set hidden
                cell.innerHTML = 'Yes';
                break;
            case 'u':
                // Set unhidden
                cell.innerHTML = 'No';
                break;
        }
    }


    /**
     * Get whether the table is in a state of being filtered or not
     * 
     * @returns {boolean}
     * 
     * @since   3.15.5
     */

    table_is_filtered() {
        return document.getElementById(`${this.table_id}_filtered_body`).innerHTML !== '';
    }


    /**
     * Detect if the row indicated by the index exists. Used in iterating through rows.
     * 
     * @param {integer} index       The row number.
     * @param {boolean} filtered    Whether the table is filtered or not.
     * 
     * @returns {boolean}
     * 
     * @since   3.15.5
     */

    is_row(index, filtered = false) {
        let id = `${this.table_id}--row_${index}`;
        if (filtered) {
            id += '--filtered';
        }
        return document.getElementById(id);
    }


    /**
     * Whether the row's checkbox is checked
     * 
     * @param {integer} row         The row number.
     * @param {boolean} filtered    Whether the table is filtered or not.
     * 
     * @returns {boolean}
     * 
     * @since   3.15.5
     */

    checkbox_is_checked(row, filtered = false) {
        const set_filtered = filtered ? '--filtered' : '';
        return document.getElementById(`${this.table_id}--row_${row}--cell_0${set_filtered}`).firstChild.checked;
    }


    /**
     * Get the id of the cell indicated.
     * 
     * @param {array}   cell        Row = cell[0] & Column = cell[1].
     * @param {boolean} filtered    Whether the table is filtered or not.
     * 
     * @returns {string}
     * 
     * @since   3.15.5
     */

    cell_id(cell, filtered = false) {
        if (cell[0] === undefined) {
            throw 'Cell row not defined';
        }
        if (cell[1] === undefined) {
            throw 'Cell column not defined';
        }
        const set_filtered = filtered ? '--filtered' : '';
        return `${this.table_id}--row_${cell[0]}--cell_${cell[1]}${set_filtered}`;
    }


    /**
     * Get the value of the cell indicated.
     * 
     * @param {array}   cell        Row = cell[0] & Column = cell[1].
     * @param {boolean} filtered    Whether the table is filtered or not.
     * 
     * @returns {string}
     * 
     * @since   3.15.5
     */

    cell_value(cell, filtered = false) {
        const cell_block = document.getElementById(this.cell_id(cell, filtered));
        return cell_block.nodeName == 'INPUT' ? cell_block.value : cell_block.innerHTML === undefined ? '' : cell_block.innerHTML;
    }


    /**
     * Get the dataset attribute object (data-example) from the <tr> row element.
     * 
     * @param {integer} row         The row number.
     * @param {boolean} filtered    Whether the table is filtered or not.
     * 
     * @returns {object}
     * 
     * @since   3.15.5
     */

    row_dataset(row, filtered = false) {
        const set_filtered = filtered ? '--filtered' : '';
        return document.getElementById(`${this.table_id}--row_${row}${set_filtered}`).dataset;
    }


    /**
     * Create a cell within the table
     * 
     * @param {array}   cell        Row = cell[0] & Column = cell[1]
     * @param {string}  content     The contents of the newly created cell.
     * @param {boolean} filtered    Whether the table is filtered or not.
     * 
     * @since   3.15.5
     */

    create_cell(cell, content, filtered = false) {
        const element = document.createElement('td');
        element.innerHTML = content;
        element.id = this.cell_id(cell, filtered);
        const row = document.getElementById(`${this.table_id}--row_${cell[0]}`);
        row.appendChild(element);
    }
}


/**
 * Automate the select all boxes
 * 
 * @param {object}  checkbox        Details of the checkbox, derived from parsing 'this'
 * @param {string}  table_id        The id of the table being controlled.
 * 
 * @since   3.6.4
 * @since   3.15.5  Entirely rewritten
 */

export function select_all_checkboxes(checkbox, table_id) {
    const rows = document.getElementById(table_id).children;
    const set_checked = checkbox.checked ? true : false;
    for (let i = 0; i < rows.length; i++) {
        const checkbox = rows[i].firstChild.firstChild;
        if (checkbox.disabled) {
            continue;
        }
        checkbox.checked = set_checked;
    }
}