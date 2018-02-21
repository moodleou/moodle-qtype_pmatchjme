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
 * JavaScript for handling JSME initialisation in pmatchjme forms.
 *
 * @package    qtype
 * @subpackage pmatchjme
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*global JSApplet*/

define(['jquery'], function($) {

    "use strict";

    /**
     * @alias qtype_pmatchjme/jsme
     */
    var t = {
        max_loading_waits: 10,

        insert_applet: function (containerid, topselector, feedback, readonly, nostereo, autoez) {
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
            t.show_jsme(topselector, containerid, jmeoptions, t.max_loading_waits);
        },

        show_jsme: function (topselector, containerid, jmeoptions, remainingloadingwaits) {
            var topnode = $(topselector),
                jme = false,
                jsmeApplet = false;

            // Ensure the JSME code is loaded properly. IE 8 struggles.
            if (typeof JSApplet !== 'object' && remainingloadingwaits > 0) {
                setTimeout(function () {
                    t.show_jsme(topselector, containerid, jmeoptions, remainingloadingwaits - 1);
                }, 100);
                return;
            }

            // Hide the loading message.
            $('#' + containerid).html('');
            $('#' + containerid).removeClass('qtype_pmatchjme-applet-warning');

            // Instantiate a new JSME.
            jsmeApplet = new JSApplet.JSME(containerid, '368px', '312px', {"options": jmeoptions.join(',')});
            jsmeApplet.name = containerid;

            // If molecule data is supplied display it.
            jme = topnode.find('input.jme').val();
            if (jme) {
                jsmeApplet.readMolecule(jme);
            }

            // Add event handler to save the values on form submit.
            topnode.parents('form').on('submit', function () {
                topnode.find('input.answer').val(jsmeApplet.smiles());
                topnode.find('input.jme').val(jsmeApplet.jmeFile());
                topnode.find('input.mol').val(jsmeApplet.molFile());
            });
        }
    };
    return t;
});
