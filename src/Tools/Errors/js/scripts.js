/**
 * Once the page has loaded, load up the feedback bar.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.2.0-beta
 */

window.addEventListener("load", function () {
    const body = document.getElementsByTagName('body');
    const element = document.createElement('div');
    element.id = 'error_feedback_bar';
    element.classList.add('error-feedback-bar');

    const errors = document.querySelectorAll('.error-bar-data');
    errors.forEach(error => {
        element.innerHTML += error.innerHTML;
        error.remove();
    });

    const button = document.createElement('div');
    button.id = 'error_feedback_button';
    button.classList.add('error-feedback-button');
    button.dataset.status = 'closed';

    const left = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"/></svg>';
    const right = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"/></svg>';

    button.innerHTML = left;
    button.onclick = function () {
        if (this.dataset.status == 'closed') {
            element.style.display = 'block';
            this.dataset.status = 'open';
            this.innerHTML = right;
        } else {
            element.style.display = 'none';
            this.dataset.status = 'closed';
            this.innerHTML = left;
        }
    };

    body[0].appendChild(button);
    body[0].appendChild(element);
});
