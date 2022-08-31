/**
 * Class to perform instructions on the uploader form element.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   3.16.1
 * @since   LBF 0.1.1-beta
 */

/**
 * Perform the various actions around the <input type='file'> element,
 * to animate and decorate the element and to handle changing events.
 * 
 * @since   3.16.1
 * @since   LBF 0.1.1-beta
 */

export default class UploaderElement {

    /**
     * Class constructor. Peforms the onchange listener
     * 
     * @param {string} id The upload element id
     * 
     * @since   3.16.1
     * @since   LBF 0.1.1-beta
     */
    constructor(id) {

        /**
         * The id of the upload element.
         * 
         * @var {string} id
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        this.id = id;

        /**
         * The DOM element identified by this.id
         * 
         * @var {DOM} upload_element
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        this.upload_element = document.getElementById(this.id);

        /**
         * The wrapping div associated with this.id
         * 
         * @var {DOM} wrapper
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        this.wrapper = document.getElementById(`${this.id}__wrapper`);

        /**
         * The button icon associated with this.id
         * 
         * @var {DOM} button
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        this.button = document.getElementById(`${this.id}__button`);

        /**
         * The feedback text box associated with this.id
         * 
         * @var {DOM} feedback_text
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        this.feedback_text = document.getElementById(`${this.id}__text_feedback`);

        /**
         * The complete container for the element.
         * 
         * @var {DOM} button_container
         * 
         * @since   3.21.2
         * @since   LBF 0.1.1-beta
         */

        this.button_container = document.getElementById(`${this.id}__container`);

        const wrapper = this.wrapper;
        const button = this.button;
        const feedback_text = this.feedback_text;

        /**
         * This is an event listener waiting for a file or files to be selected.
         * 
         * It performs actions based on that choice
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        this.upload_element.addEventListener("change", function () {
            switch (this.files.length) {
                case 0:
                    feedback_text.innerText = 'No file selected';
                    wrapper.classList.remove('upload_button_wrapper__selected');
                    button.classList.remove('upload_button_text__selected');
                    break;
                case 1:
                    feedback_text.innerText = this.files[0].name;
                    wrapper.classList.add('upload_button_wrapper__selected');
                    button.classList.add('upload_button_text__selected');
                    break;
                default:
                    feedback_text.innerText = `${this.files.length} files selected`;
                    wrapper.classList.add('upload_button_wrapper__selected');
                    button.classList.add('upload_button_text__selected');
            }
        });

        /**
         * Handle drag and drop events.
         * 
         * Currently disabled till this method can be made to handle drag, drop and wait
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */

        // this.handle_drag_and_drop()

        this.observe_size();
    }


    /**
     * Reset an upload button to an empty vanilla state
     * 
     * @since   3.16.1
     * @since   LBF 0.1.1-beta
     */

    reset_upload_element() {
        this.upload_element.value = '';
        this.feedback_text.innerText = 'No file selected';
        this.wrapper.classList.remove('upload_button_wrapper__selected');
        this.button.classList.remove('upload_button_text__selected');
    }


    /**
     * Handle drag and drop events for uploading files.
     * 
     * @status  WIP - Currently cannot drop on upload without requiring an upload immediately
     *                This needs to be done before it is useful and functional.
     * 
     * @since   3.16.1
     * @since   LBF 0.1.1-beta
     */

    handle_drag_and_drop() {

        /**
         * This prevents the wholesale redownloading of files if poorly dropped
         * 
         * @since   3.16.1
         * @since   LBF 0.1.1-beta
         */
        const all_elements = document.querySelectorAll('.upload_button_wrapper');
        window.ondragover = function (event) {
            event.preventDefault();
            event.stopPropagation();
            all_elements.forEach(element => {
                element.classList.add('drag_over_highlight_all')
            });
        }
        window.ondragleave = function (event) {
            event.preventDefault();
            event.stopPropagation();
            all_elements.forEach(element => {
                element.classList.remove('drag_over_highlight_all');
            });
        }
        window.ondrop = function (event) {
            event.preventDefault();
            event.stopPropagation();
            all_elements.forEach(element => {
                element.classList.remove('drag_over_highlight_all');
            });
        }

        this.wrapper.ondrop = function (event) {
            event.preventDefault();
            event.stopPropagation();
            //  console.log(event.dataTransfer.items);
            for (let i = 0; i < event.dataTransfer.files.length; i++) {
                const file = event.dataTransfer.files[i];
                // upload_element.value = file.name;
            }
            this.classList.remove('drag_over_element');
        }
        this.wrapper.ondragover = function (event) {
            event.preventDefault();
            event.stopPropagation();
            this.classList.add('drag_over_element');
        }

        this.wrapper.ondragleave = function (event) {
            event.preventDefault();
            event.stopPropagation();
            this.classList.remove('drag_over_element');
        }
    }


    /**
     * JS to handle the resizing of the element.
     * 
     * @since   3.21.0
     * @since   LBF 0.1.1-beta
     */

    observe_size() {
        const resizeObserver = new ResizeObserver(entries => {
            for (let entry of entries) {
                if (entry.contentRect.width < 400) {
                    this.wrapper.style.gridTemplateColumns = "100%";
                    this.wrapper.style.gridTemplateRows = "1fr 1fr";
                } else {
                    this.wrapper.style.gridTemplateColumns = "160px 1fr";
                    this.wrapper.style.gridTemplateRows = "auto";
                }
            }
        });
        resizeObserver.observe(this.button_container);
    }
}