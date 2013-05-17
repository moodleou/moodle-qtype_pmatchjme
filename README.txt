Pattern match with JME editor question type

This question type was created by Jamie Pratt, working for the Open University
(http://www.open.ac.uk/).

Students can draw a molecule using the JME editor. This is then graded by matching
it against vaious model answers that are expressed using a sophisticated patten
matching algorithm. See http://docs.moodle.org/dev/The_OU_PMatch_algorithm

This question type is has been available since Moodle 2.1+. This version is
compatible with Moodle 2.5+.

This question type requires that the pmatch question type
https://github.com/moodleou/moodle-qtype_pmatch/
to be installed in order to work. It also requires the JME molecule editor.
http://www.molinspiration.com/jme/getjme.html explains how to get a copy.



This question type should be installed like any other Moodle add-on. See
http://docs.moodle.org/25/en/Installing_add-ons.

After you have installed the code, you need to manually copy JME.jar (see above)
to question/type/pmatchjme/jme/ folder.

To install the question type using git, type this command in the root of your
Moodle install

    git clone git://github.com/moodleou/moodle-qtype_pmatchjme.git question/type/pmatchjme
    echo '/question/type/pmatchjme/' >> .git/info/exclude
    git clone git://github.com/moodleou/moodle-qtype_pmatch.git question/type/pmatch
    echo /question/type/pmatch/ >> .git/info/exclude
    git clone git://github.com/moodleou/moodle-editor_supsub.git lib/editor/supsub
    echo /lib/editor/supsub/ >> .git/info/exclude
