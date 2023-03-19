import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller handles changing the form action and placeholder when a user uses the dropdown to change what they would like to search for 
 */
export default class extends Controller {
    update(event) {
        this.element.setAttribute('action', '/search/' + event.target.text.toLowerCase())
        this.element.querySelector('#search-bar').setAttribute('placeholder', 'Search for ' + event.target.text)
        this.element.querySelector('#search-dropdown').innerText = event.target.text
    }
}