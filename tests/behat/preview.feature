@ou @ou_vle @qtype @qtype_pmatchjme @_switch_window @javascript
Feature: Preview a pattern match with JME question
  As a teacher
  In order to check my pattern match with JME questions will work for students
  I need to preview them

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name         | template |
      | Test questions   | pmatchjme | Draw benzene | benzene  |

  Scenario: Preview a question and get it right.
    When I am on the "Draw benzene" "core_question > preview" page logged in as "teacher1"
    And I click on the benzene tool in the pattern match with JME question
    And I click on the drawing area in the pattern match with JME question
    And I press "Submit and finish"
    Then the state of "Please draw a benzene molecule." question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"
