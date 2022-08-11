@ou @ou_vle @qtype @qtype_pmatchjme
Feature: Test editing a pattern match with JME questions
  As a teacher
  In order to be able to update my pattern match with JME questions
  I need to edit them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
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

  @javascript
  Scenario: Edit a pattern match with JME question
    # Edit the question.
    When I am on the "Draw benzene" "core_question > edit" page logged in as teacher1
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
