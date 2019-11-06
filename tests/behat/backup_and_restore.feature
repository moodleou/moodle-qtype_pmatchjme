@ou @ou_vle @qtype @qtype_pmatchjme
Feature: Test duplicating a quiz containing a pattern match with JME question
  As a teacher
  In order re-use my courses containing pattern match with JME questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name         | template |
      | Test questions   | pmatchjme | Draw benzene | benzene  |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | Draw benzene | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing a pattern match with JME question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" in current page administration
    When I choose "Edit question" action for "Draw benzene" in the question bank
    Then the following fields match these values:
      | Question name       | Draw benzene                            |
      | Question text       | Please draw a benzene molecule.         |
      | General feedback    | This is the simplest aromatic molecule. |
      | Default mark        | 1                                       |
      | id_allowsubscript   | 0                                       |
      | id_allowsuperscript | 0                                       |
      | id_answer_0         | match (c1ccccc1)                        |
      | id_fraction_0       | 1                                       |
      | id_feedback_0       | Well done!                              |
      | id_atomcount_0      | 0                                       |
      | id_otherfeedback    | That is not right.                      |
      | id_atomcount_other  | 1                                       |
      | Penalty             | 0.3333333                               |
