M.qtype_pmatchjme={
    insert_jme_applet : function(Y, inputdivselector, appleturl, feedback, readonly){
        var s= this.jme_applet_html(appleturl, '100%', 400, feedback, readonly);
        var inputdiv = Y.one(inputdivselector);
        inputdiv.append(s);
        var appletnode = Y.one(inputdivselector+' applet');
        this.polltimer = Y.later(1000, this, this.pool_for_applet_load,
                                [Y, inputdivselector], true);
        inputdiv.ancestor('form').on('submit', function (){
            Y.one(inputdivselector+' input.smiles').set('value', document.JME.smiles());
            Y.one(inputdivselector+' input.answer').set('value', document.JME.jmeFile());
            Y.one(inputdivselector+' input.mol').set('value', document.JME.molFile());
        }, this);
    },
    polltimer : null,
    pollcount : 100,
    pool_for_applet_load : function (Y, inputdivselector) {
        this.pollcount--;
        if (typeof (document.JME.readMolecule) === 'function') {
            document.JME.readMolecule(Y.one(inputdivselector+' input.answer').get('value'));
            this.polltimer.cancel();
        } else if (pollcount === 0) {
            this.polltimer.cancel();
        }
    },
    jme_applet_html : function(appleturl, w, h, feedback, readonly) {
        var applethtml = "<applet code=\"JME.class\" name=\"JME\" archive=\""+appleturl+"\"" +
                                                                "width=\""+w+"\" height=\""+h+"\">";
        if (readonly) {
            applethtml += '<param name="options" value="depict">';
        }
        applethtml += M.util.get_string('enablejava', 'qtype_pmatchjme') + "</applet>";
        applethtml += feedback;
        return applethtml;
    }
}