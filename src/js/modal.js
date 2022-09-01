/**
 * This script creates and handles all the functions in creating a modal box on the page
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.12.8
 * LBF 0.1.1-beta
 */

/**
 * Object class for creating and handling modal boxes
 * 
 * @property {string} body_id
 * 
 * @since   3.13.0
 * LBF 0.1.1-beta
 */

export default class Modal {
    constructor() {

        /**
         * The body id of the modal box
         * 
         * @var {string} body_id
         * 
         * @since   3.13.0
         */

        this.body_id = 'modal_content'
    }


    /**
     * Draw a modal box with all the trimmings, including faded background and x to close
     *
     * @since   3.12.8
     */

    create_modal_box() {
        const block = document.createDocumentFragment();

        const overlay = this.create_overlay();
        block.appendChild(overlay);

        const box = document.createElement('div');
        box.classList.add('modal_box');

        const header = this.create_header_area();
        box.appendChild(header);

        const main = this.create_main_area();
        box.appendChild(main);

        block.appendChild(box);

        document.getElementById('modal_overlay_fill').appendChild(block);
    }


    /**
     * Remove the modal overlay.
     * 
     * @since   3.12.8
     */

    remove_modal() {
        document.getElementById('modal_overlay_fill').innerHTML = '';
    }


    /**
     * Create the main block area. In here the app's contextual content will go
     * 
     * @var {string} 
     * 
     * @returns {DocumentFragment} 
     * 
     * @since   3.12.8
     */

    create_main_area() {
        const main = document.createElement('div');
        main.id = this.body_id;
        main.classList.add('modal_main_body');
        return main;
    }

    /**
     * Create the overlay which dims the background and sits behind the modal block
     * 
     * @returns {DocumentFragment}
     * 
     * @since   3.12.8
     */

    create_overlay() {
        const overlay = document.createElement('div');
        overlay.classList.add('modal_overlay');
        overlay.onclick = function () {
            this.remove_modal();
        }.bind(this);
        return overlay;
    }


    /**
     * Create the header area inside the modal block
     * 
     * @returns {DocumentFragment} 
     * 
     * @since   3.12.8
     */

    create_header_area() {
        const header = document.createElement('div');
        header.classList.add('modal_heading_area');
        header.classList.add('text_align_right');
        /**
         * Create the close box 'x' inside the header area
         */
        const close_box = document.createElement('span');
        close_box.innerHTML = '&times;';
        close_box.classList.add('modal_close');
        close_box.onclick = function () {
            this.remove_modal();
        }.bind(this);
        header.appendChild(close_box);
        return header;
    }
}
