@ou @ou_vle @qtype @qtype_pmatchjme
Feature: Test all the basic functionality of this question type
  In order to evaluate students chemical knowledge
  As a teacher
  I need to create and preview pattern match with JME questions.

  # We do not yet attempt to simulate a student drawing a molecule.

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
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  @javascript
  Scenario: Create, edit then preview a pattern match with JME question.

    # Create a new question
    When I add a "Pattern match with JME" question filling the form with:
      | Question name      | My first JME question             |
      | Question text      | Draw ethanol                      |
      | Answer 1           | match (CCO)                       |
      | id_fraction_0      | 100%                              |
      | id_feedback_0      | Well done!                        |
      | Answer 2           | match (CO)                        |
      | id_fraction_1      | 100%                              |
      | id_feedback_1      | Don't confuse meths with alcohol! |
      | id_atomcount_1     | 1                                 |
      | id_otherfeedback   | Sorry, no.                        |
      | id_atomcount_other | 1                                 |
      | Hint 1             | Please try again.                 |
      | Hint 2             | Haven't you drunk enough alcohol? |
    Then I should see "My first JME question"

    # Preview it.
    When I click on "Preview" "link" in the "My first JME question" "table_row"
    And I switch to "questionpreview" window
    Then I should see "Preview question: My first JME question"
    And I switch to the main window

    # Backup the course and restore it.
    When I log out
    And I log in as "admin"
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    Then I should see "Course 2"
    When I navigate to "Question bank" node in "Course administration"
    Then I should see "My first JME question"

    # Edit the copy and verify the form field contents.
    When I click on "Edit" "link" in the "My first JME question" "table_row"
    Then the following fields match these values:
      | Question name      | My first JME question             |
      | Question text      | Draw ethanol                      |
      | Answer 1           | match (CCO)                       |
      | id_fraction_0      | 100%                              |
      | id_feedback_0      | Well done!                        |
      | Answer 2           | match (CO)                        |
      | id_fraction_1      | 100%                              |
      | id_feedback_1      | Don't confuse meths with alcohol! |
      | id_atomcount_1     | 1                                 |
      | id_otherfeedback   | Sorry, no.                        |
      | id_atomcount_other | 1                                 |
      | Hint 1             | Please try again.                 |
      | Hint 2             | Haven't you drunk enough alcohol? |

    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
