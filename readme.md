# Pattern match with JME editor question type [![Build Status](https://travis-ci.com/moodleou/moodle-qtype_pmatchjme.svg?branch=master)](https://travis-ci.com/moodleou/moodle-qtype_pmatchjme)

Students can draw a molecule using the JME editor. This is then graded by matching
it against various model answers that are expressed using a sophisticated patten
matching algorithm. See http://docs.moodle.org/dev/The_OU_PMatch_algorithm


## Acknowledgements

This question type was created by Jamie Pratt, working for the Open University
(http://www.open.ac.uk/) and has since had many enhancements by OU staff.


## Installation and set-up

This question type requires that the pmatch question type
https://github.com/moodleou/moodle-qtype_pmatch/
to be installed in order to work.

### Install from the plugins database

Install from the Moodle plugins database
* https://moodle.org/plugins/qtype_pmatchjme
* https://moodle.org/plugins/qtype_pmatch
* https://moodle.org/plugins/editor_ousupsub

### Install using git

Or you can install using git. Type this commands in the root of your Moodle install

    git clone https://github.com/moodleou/moodle-qtype_pmatchjme.git question/type/pmatchjme
    echo '/question/type/pmatchjme/' >> .git/info/exclude
    git clone https://github.com/moodleou/moodle-qtype_pmatch.git question/type/pmatch
    echo /question/type/pmatch/ >> .git/info/exclude
    git clone https://github.com/moodleou/moodle-editor_ousupsub.git lib/editor/ousupsub
    echo /lib/editor/ousupsub/ >> .git/info/exclude

Then run the moodle update process
Site administration > Notifications
