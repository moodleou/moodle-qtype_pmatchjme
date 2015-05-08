@ou @ou_vle @qtype @qtype_pmatchjme
Feature: Preview a drag-drop into text question
  As a teacher
  In order to check my drag-drop into text questions will work for students
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
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  @javascript
  Scenario: Preview a question and get it right.
    When I click on "Preview" "link" in the "Draw benzene" "table_row"
    And I switch to "questionpreview" window
    # Select Benzene ring tool.
    And I click on "//div[@name = 'jme1']/div/div[@class = 'jsa-resetDiv']/div[@class = 'jsa-resetDiv'][3]/*[name() = 'svg']/*[name() = 'rect' and @x = '200' and @y = '25' and @width = '24']" "xpath_element"
    # Click in the drawing area.
    And I click on "//div[@name = 'jme1']/div/div[@class = 'jsa-resetDiv']/div[@class = 'jsa-resetDiv'][2]" "xpath_element"
    And I press "Submit and finish"
    Then the state of "Please draw a benzene molecule." question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"
