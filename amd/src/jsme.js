// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript for handling the JSME.
 *
 * @module     qtype_pmatchjme
 * @class      jsme
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

/**
 * Start the load of the JSME code.
 */
const loadJsmeCode = () => {
    // Alternatively, we need to add a script tag to the page.
    // I think that if Moodle was using standard JS modules
    // (instead of require_js, then we could just us import().
    const scriptTag = document.createElement('script');
    scriptTag.src = M.cfg.wwwroot + '/question/type/pmatchjme/jsme/jsme.nocache.js';
    scriptTag.async = true;
    document.body.append(scriptTag);
};

/**
 * Initialise a question by stating the JSME editor.
 *
 * @param {string} containerId Id of the div to put the JSME in.
 * @param {string} questionDivId Id of the outer question div.
 * @param {string} feedbackImageHtml HTML of the feedback icon to show, if any.
 * @param {boolean} readonly boolean, whether the display should be read-only.
 * @param {boolean} nostereo JSME option.
 * @param {boolean} autoez JSME option.
 */
const insertApplet = (containerId, questionDivId, feedbackImageHtml, readonly, nostereo, autoez) => {
    const pendingToken = {};
    M.util.js_pending(pendingToken);

    const jmeoptions = [];
    if (nostereo) {
        jmeoptions.push("nostereo");
    }
    if (autoez) {
        jmeoptions.push("autoez");
    }
    if (readonly) {
        jmeoptions.push("depict");
    }

    // Function to run once JSME library code has loaded.
    const displayJsme = () => {
        // Hide the loading message.
        const containerElement = document.getElementById(containerId);
        containerElement.innerHTML = '';
        containerElement.classList.remove('qtype_pmatchjme-applet-warning');

        // Instantiate a new JSME.
        const jsmeApplet = new window.JSApplet.JSME(containerId, '368px', '312px', {"options": jmeoptions.join(',')});
        jsmeApplet.name = containerId;

        // If molecule data is supplied display it.
        const questionDiv = document.getElementById(questionDivId);
        const initalJmeContent = questionDiv.querySelector('input.jme').value;
        if (initalJmeContent) {
            jsmeApplet.readMolecule(initalJmeContent);
        }

        // Add event handler to save the values on form submit.
        questionDiv.closest('form').addEventListener('submit', () => {
            questionDiv.querySelector('input.answer').value = jsmeApplet.smiles();
            questionDiv.querySelector('input.jme').value = jsmeApplet.jmeFile();
            questionDiv.querySelector('input.mol').value = jsmeApplet.molFile();
        });

        M.util.js_complete(pendingToken);
    };

    if (window.hasOwnProperty('JSApplet')) {
        // Already loaded, e.g. by another question on the same page.
        displayJsme();
    } else {
        if (window.hasOwnProperty('jsmeOnLoad')) {
            const oldJsmeOnLoad = window.jsmeOnLoad;
            window.jsmeOnLoad = () => {
                oldJsmeOnLoad();
                displayJsme();
            };
        } else {
            window.jsmeOnLoad = () => {
                displayJsme();
            };
        }
        loadJsmeCode();
    }
};

export {
    insertApplet
};
