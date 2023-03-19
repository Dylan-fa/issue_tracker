import { Controller } from '@hotwired/stimulus';
/**
 * This JavaScript Stimulus controller handles initializing the bootstrap tooltips
 */
export default class extends Controller {
    static targets = [ "tooltip" ];
    connect() {
        const bootstrap = require('bootstrap')
        this.tooltipTargets.forEach(element => new bootstrap.Tooltip(element))
    }
}