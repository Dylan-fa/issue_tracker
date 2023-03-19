import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller handles rendering the preview of markdown text when the user clicks on the preview button and opens the preview modal
 */
export default class extends Controller {
    static targets = [ "field", "modal" ];
    render(event) {
        fetch('/api/parse', {method: 'POST', body: JSON.stringify({text: this.fieldTarget.value}), headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }})
            .then((response) => response.json())
            .then((data) => this.modalTarget.innerHTML = data['parsed']);
    }
}