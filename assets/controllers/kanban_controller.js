import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller handles all the Kanban operations.
 */
export default class extends Controller {
    // Save a dropzone to the class instance when the controller is initialized for later use
    connect() {
        this.dropzone = this.element.querySelector('.dropzone');
    }
    // Add the ID of a column to the add issue form when a user clicks the button to add an issue
    updateForm(event) {
        var card = event.target.closest('.card');
        var id = card.getAttribute('data-id');
        this.element.querySelector('#add_issue_to_column_form_id').value = id;
    }
    // Save the issue ID and column ID to an instance variable for use if the user confirms the removal in the modal
    removeIssueDetails(event) {
        this.removeIssueID = event.target.closest('.kanban-issue').getAttribute('data-id');
        this.removeColumnID = event.target.closest('.kanban-column').getAttribute('data-id');
    }
    // Remove an issue from a column when a user confirms the modal
    removeIssue(event) {
        // API POST request to remove the issue
        fetch('/api/kanban/removeissue', {method: 'POST', body: JSON.stringify({issueID: this.removeIssueID, columnID: this.removeColumnID}), headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }})
        // Find the issue card and dropzone and remove it from the HTML document
        var draggable = this.element.querySelector(`.kanban-column[data-id="${this.removeColumnID}"] .kanban-issue[data-id="${this.removeIssueID}"]`).closest('.kanban-draggable')
        draggable.remove();
    }
    // When a user first picks up and starts to drag an issue, use the DataTransfer API in order to set the data (issueID and columnID) being dragged
    dragged(event) {
        event.dataTransfer.setData('text/json', JSON.stringify({issueID: event.target.getAttribute('data-id'), columnID: event.target.closest('.kanban-column').getAttribute('data-id')}));
    }
    // When a user drops a card to a new column
    dropped(event) {
        // Get the DataTransfer data
        var dt = JSON.parse(event.dataTransfer.getData('text/json'));
        var issue = this.element.querySelector(`.kanban-column[data-id="${dt.columnID}"] .kanban-issue[data-id="${dt.issueID}"]`)
        var dropzone = event.target;
        dropzone.classList.remove('dragged');
        var el = dropzone.closest('.kanban-draggable')
        // This column is empty if there are no kanban-draggable elements
        if (el == null) {
            dropzone.after(issue.closest('.kanban-draggable'));
        // This column is not empty, insert after kanban-draggable element closest to the dropzone
        } else if (el !== null) {
            el.after(issue.closest('.kanban-draggable'));
        }
        // API POST request to move the issue
        fetch('/api/kanban/moveissue', {method: 'POST', body: JSON.stringify({issueID: dt.issueID, oldColumnID: dt.columnID, newColumnID: event.target.closest('.kanban-column').getAttribute('data-id')}), headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }})

    }
    // Add this class to indicate that the user is hovering over this dropzone
    draggedOver(event) {
        event.preventDefault();
        event.target.classList.add('dragged');
    }
    // Remove this class to indicate that the user is no longer hovering over this dropzone
    draggedOut(event) {
        event.preventDefault();
        event.target.classList.remove('dragged');
    }
}