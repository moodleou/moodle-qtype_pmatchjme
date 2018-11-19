@ou @ou_vle @qtype @qtype_pmatchjme
Feature: Test creating a pattern match with JME question
  As a teacher
  In order to test my student's chemistry knowledge
  I need to be able to create pattern match with JME questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript
  Scenario: Create a pattern match with JME question
    When I add a "Pattern match with molecular editor" question filling the form with:
      | Question name      | My first molecular editor question |
      | Question text      | Draw ethanol                       |
      | Answer 1           | match (CCO)                        |
      | id_fraction_0      | 100%                               |
      | id_feedback_0      | Well done!                         |
      | Answer 2           | match (CO)                         |
      | id_fraction_1      | 100%                               |
      | id_feedback_1      | Don't confuse meths with alcohol!  |
      | id_atomcount_1     | 1                                  |
      | id_otherfeedback   | Sorry, no.                         |
      | id_atomcount_other | 1                                  |
      | Hint 1             | Please try again.                  |
      | Hint 2             | Haven't you drunk enough alcohol?  |
    Then I should see "My first molecular editor question"
