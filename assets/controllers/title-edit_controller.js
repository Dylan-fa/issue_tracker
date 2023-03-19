import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller handles changing the title preview when a user changes the title in a form
 */
export default class extends Controller {
    static targets = [ "title" ];
    render(event) {
        this.titleTarget.innerText = event.target.value;
    }
}