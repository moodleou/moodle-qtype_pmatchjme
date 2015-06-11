YUI.add('moodle-qtype_pmatchjme-jsme', function (Y, NAME) {

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
 * JavaScript code for the pmatchjme question type.
 *
 * @package    qtype_pmatchjme
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.qtype_pmatchjme = M.qtype_pmatchjme || {};
M.qtype_pmatchjme.jsme = {
    MAX_LOADING_WAITS: 10,
    jsapplets: [],

    insert_applet: function(containerid, topselector, feedback, readonly, nostereo, autoez) {
        var jmeoptions = [];
        if (nostereo) {
            jmeoptions.push("nostereo");
        }
        if (autoez) {
            jmeoptions.push("autoez");
        }
        if (readonly) {
            jmeoptions.push("depict");
        }

        this.show_jsme(topselector, containerid, jmeoptions, this.MAX_LOADING_WAITS);
    },

    show_jsme: function (topselector, containerid, jmeoptions, remainingloadingwaits) {
        var topnode = Y.one(topselector);

        // Ensure the JSME code is loaded properly. IE 8 struggles.
        if (typeof JSApplet !== 'object' && remainingloadingwaits > 0) {
            setTimeout(function() {
                        M.qtype_pmatchjme.jsme.show_jsme(topselector, containerid,
                                jmeoptions, remainingloadingwaits - 1);
                    }, 100);
            return;
        }

        // Hide the JS noot working message.
        Y.one('#' + containerid).setHTML('');
        Y.one('#' + containerid).removeClass('qtype_pmatchjme-applet-warning');

        // Instantiate a new JSME.
        jsmeApplet = new JSApplet.JSME(containerid, '368px', '312px', {"options": jmeoptions.join(',')});
        jsmeApplet.name = containerid;
        this.jsapplets[containerid] = jsmeApplet;

        // If molecule data is supplied display it.
        var jme = topnode.one('input.jme').get('value');
        if (jme) {
            jsmeApplet.readMolecule(jme);
        }

        // Add event handler to save the vales on form submit.
        topnode.ancestor('form').on('submit', function (){
            topnode.one('input.answer').set('value', this.jsapplets[containerid].smiles());
            topnode.one('input.jme').set('value', this.jsapplets[containerid].jmeFile());
            topnode.one('input.mol').set('value', this.jsapplets[containerid].molFile());
        }, this);
    }
};


}, '@VERSION@', {"requires": ["node", "event"]});
