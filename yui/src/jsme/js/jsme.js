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
    reload_limit: 10,
    editor_displayed: false,
    topnode: null,
    applet_name: null,

    insert_applet: function(toreplaceid, appletid, name, topnode, appleturl,
            feedback, readonly, nostereo, autoez) {
        var javaparams = ['jme', Y.one(topnode + ' input.jme').get('value')];
        var jmeoptions = [];
        this.applet_name = name;
        this.topnode = topnode;

        if (nostereo) {
            jmeoptions[jmeoptions.length] = "nostereo";
        }
        if (autoez) {
            jmeoptions[jmeoptions.length] = "autoez";
        }
        if (readonly) {
            jmeoptions[jmeoptions.length] = "depict";
        }
        if (jmeoptions.length !== 0) {
            javaparams[javaparams.length] = "options";
            javaparams[javaparams.length] = jmeoptions.join(',');
        }

        this.show_java(toreplaceid, appletid, name, appleturl,
                    288, 312, 'JME.class', javaparams, jmeoptions);
    },

    update_inputs: function() {
        var name = this.applet_name,
        topnode = this.topnode;
        if (!this.editor_displayed) {
            this.show_error(topnode);
        } else {
            var inputdiv = Y.one(topnode);
            inputdiv.ancestor('form').on('submit', function (){
                Y.one(topnode + ' input.answer').set('value', this.find_applet(name).smiles());
                Y.one(topnode + ' input.jme').set('value', this.find_applet(name).jmeFile());
                Y.one(topnode + ' input.mol').set('value', this.find_applet(name).molFile());
            }, this);
        }
    },

    show_error: function (topnode) {
        var errormessage = '<span class ="javascriptwarning">' +
                M.util.get_string('enablejavascript', 'qtype_pmatchjme') +
                '</span>';
        Y.one(topnode + ' .ablock').insert(errormessage, 1);
    },

    /**
     * Gets around problem in ie6 using name
     */
    find_applet: function (appletname) {
        var applets = document.jsapplets;
        for (var appletno = 0; i < applets.length; appletno++) {
            if (applets[appletno].name === appletname) {
                return applets[appletno];
            }
        }
        return null;
    },

    // Note: This method is also called from mod/audiorecorder.
    show_java: function (id, appletid, name, java, width, height, appletclass, javavars, jmeoptions) {

        var warningspan = document.getElementById(id);
        warningspan.innerHTML = '';

        // Ensure the JSME code is loaded properly. IE 8 struggles.
        if (typeof JSApplet !== 'object' && this.reload_limit) {
            setTimeout(function() {
                M.qtype_pmatchjme.jsme.show_java(id, appletid, name,
                        java, width, height, appletclass, javavars, jmeoptions); }, 100);
            this.reload_limit--;
            return false;
        }

        var newApplet = document.createElement("div");
        newApplet.setAttribute('code', appletclass);
        newApplet.setAttribute('archive', java);
        newApplet.setAttribute('name', name);
        newApplet.setAttribute('width', width);
        newApplet.setAttribute('height', height);
        newApplet.setAttribute('tabIndex', -1);
        newApplet.setAttribute('mayScript', true);
        newApplet.setAttribute('id', appletid);
        // In case applet supports the focushack system, we
        // pass in its id as a parameter.
        javavars[javavars.length] = 'focushackid';
        javavars[javavars.length] = newApplet.id;
        for (var i = 0; i < javavars.length; i += 2) {
            var param = document.createElement('param');
            param.name = javavars[i];
            param.value = javavars[i + 1];
            newApplet.appendChild(param);
        }
        warningspan.appendChild(newApplet);

        // Instantiate a new JSME:
        // Arguments: HTML id, width, height (must be string not number!).
        jsmeApplet = new JSApplet.JSME(appletid, (width + 80) + 'px', height + 'px', {
            // Optional parameters.
            "options" : jmeoptions.join(',')
        });
        jsmeApplet.name = name;
        // If molecule data is supplied display it.
        if (javavars[1]) {
            jsmeApplet.readMolecule(javavars[1]);
        }
        // Create document.jsapplets array if it doesn't exist.
        if (!document.jsapplets) {
            document.jsapplets = [];
        }
        document.jsapplets[document.jsapplets.length] = jsmeApplet;

        this.editor_displayed = true;
        this.update_inputs();
        return this.editor_displayed;
    }
};
