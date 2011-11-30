Pattern match with JME editor question type

This question type was created by Jamie Pratt, working for the Open University
(http://www.open.ac.uk/).

Students can draw a molecule using the JME editor. This is then graded by matching
it against vaious model answers that are expressed using a sophisticated patten
matching algorithm. See http://docs.moodle.org/dev/The_OU_PMatch_algorithm

This question type is compatible with Moodle 2.1+.

This question type requires that the pmatch question type
https://github.com/jamiepratt/moodle-qtype_pmatch/
to be installed in order to work.

To install the question type using git and add question/type/pmatchjme to your git ignore,
type these commands in the root of your Moodle install : 

    git clone git://github.com/jamiepratt/moodle-qtype_pmatchjme.git question/type/pmatchjme
    echo '/question/type/pmatchjme' >> .git/info/exclude

Alternatively, download the zip from

    https://github.com/jamiepratt/moodle-qtype_pmatchjme/zipball/master

Unzip it into the question/type folder, and then rename the new folder to pmatchjme.

You also need a copy of the JME molecule editor. See here for details of how to get a copy :

http://www.molinspiration.com/jme/getjme.html

Copy JME.jar to question/type/pmatchjme/jme/

