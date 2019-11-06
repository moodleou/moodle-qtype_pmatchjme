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
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Preview a question and get it right.
    When I choose "Preview" action for "Draw benzene" in the question bank
    And I switch to "questionpreview" window
    # This next line should not be necessary. However, the JME was not loading, and this seems to fix it.
    And I reload the page
    # Select Benzene ring tool.
    And I click on "//div[@id = 'qtype_pmatchjme-applet1']/div/div[@class = 'jsa-resetDiv']/div[@class = 'jsa-resetDiv'][3]/*[name() = 'svg']/*[name() = 'g']/*[name() = 'rect' and @x = '5790' and @y = '750' and @width = '660']" "xpath_element"
    # Click in the drawing area.
    And I click on "//div[@id = 'qtype_pmatchjme-applet1']/div/div[@class = 'jsa-resetDiv']/div[@class = 'jsa-resetDiv'][2]" "xpath_element"
    And I press "Submit and finish"
    Then the state of "Please draw a benzene molecule." question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"
    And I switch to the main window
