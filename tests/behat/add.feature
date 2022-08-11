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
    When I add a "item_qtype_pmatchjme" question filling the form with:
      | Question name       | My first molecular editor question |
      | Question text       | Draw ethanol                       |
      | id_allowsuperscript | 0                                  |
      | id_allowsubscript   | 1                                  |
      | Answer 1            | match (CCO)                        |
      | id_fraction_0       | 100%                               |
      | id_feedback_0       | Well done!                         |
      | Answer 2            | match (CO)                         |
      | id_fraction_1       | 100%                               |
      | id_feedback_1       | Don't confuse meths with alcohol!  |
      | id_atomcount_1      | 1                                  |
      | id_otherfeedback    | Sorry, no.                         |
      | id_atomcount_other  | 1                                  |
      | Hint 1              | Please try again.                  |
      | Hint 2              | Haven't you drunk enough alcohol?  |
    Then I should see "My first molecular editor question"
    # Checking that the next new question form displays user preferences settings.
    And I press "Create a new question ..."
    And I set the field "item_qtype_pmatchjme" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | id_allowsuperscript     | 0                                                             |
      | id_allowsubscript       | 1                                                             |
