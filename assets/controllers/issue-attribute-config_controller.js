import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller handles the adding and deleting of child forms for the issue attributes (status, priority) configuration 
 */
export default class extends Controller {
    // Deletes the child form
    delete(event) {
        event.target.closest('.card').remove();
    }
    // Adds a child form from the prototype attribute of the form collection
    add(event) {
        var count = this.element.querySelector('#form-collection').querySelectorAll('.card').length;
        // replace the first __name__ and then the rest, as the count is different
        var prototype = this.element.querySelector('#form-collection').getAttribute('data-prototype').replace('__name__', count + 1).replaceAll('__name__', count);
        this.element.querySelector('#form-collection').insertAdjacentHTML('beforeend', prototype);
    }
}