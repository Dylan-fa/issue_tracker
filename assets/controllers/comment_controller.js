import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller adds the id and text of comment to the edit form when a user clicks the edit button
 */
export default class extends Controller {
    updateForm(event) {
        var card = event.target.closest('.card');
        var id = card.getAttribute('data-id');
        var text = card.getAttribute('data-text');
        this.element.querySelector('#edit_comment_form_text').value = text
        this.element.querySelector('#edit_comment_form_id').value = id
    }
    updateDeleteForm(event) {
        var card = event.target.closest('.card');
        var id = card.getAttribute('data-id');
        this.element.querySelector('#delete_comment_form_id').value = id
    }
}