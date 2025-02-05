@report @javascript @report_cmcompetency
Feature: Bulk rate competencies for an activity
  As a teacher
  In order to rate competencies on an activity rapidly
  I need to rate many students at the same time

  Background:
    Given the cmcompetency fixtures exist
    When I am on the "Module 2" "forum activity" page logged in as teacher
    And I navigate to "Competencies assessment" in current page administration
    Then I should see "Bulk rating for all students for all competencies of this activity"

  Scenario: Bulk rate an activity without groups and one competency
    Given I click on "Bulk rating for all students for all competencies of this activity" "button"
    # Test that changing modules work correctly and the competencies are correct for Module 1.
    When I open the autocomplete suggestions list
    And I click on "Module 1" item in the autocomplete list
    Then "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table/caption" "xpath_element" should exist
    And I should see "Competency A" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table/caption" "xpath_element"
    And "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]/table/caption" "xpath_element" should not exist
    # Competency A : Verify that the default option ('not good') is actually checked and check 'good' instead.
    And I should see "not good" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[1]/td[1]" "xpath_element"
    And "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[1]/td[2]/input[@checked]" "xpath_element" should exist
    And I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[2]/td[2]/input" "xpath_element"
    # Save the task.
    And I click on "Save" "button"
    And I should see "Evaluations will be executed soon"
    And I open the autocomplete suggestions list
    And I click on "Module 2" item in the autocomplete list
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    And I open the autocomplete suggestions list
    And I click on "Module 1" item in the autocomplete list
    And I should see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be disabled
    # Run the task and check the messages don't appear anymore.
    And I run all adhoc tasks
    And I open the autocomplete suggestions list
    And I click on "Module 2" item in the autocomplete list
    And I open the autocomplete suggestions list
    And I click on "Module 1" item in the autocomplete list
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    # Check that Stepanie have been rated, but not Rebecca (who was already rated).
    And I am on the "Module 1" "assign activity" page
    And I navigate to "Competencies assessment" in current page administration
    And I open the autocomplete suggestions list
    And I click on "Stepanie Grant" item in the autocomplete list
    And I should see "good" in the "Competency A" "table_row"
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    And I should see "not good" in the "Competency A" "table_row"

  Scenario: Bulk rate an activity with groups and 2 competencies
    Given I click on "Bulk rating for all students for all competencies of this activity" "button"
    # Choose a group
    When I set the field "group" to "Team 1"
    # Competency A : Verify that the default option ('not good') is actually checked and check 'good' instead.
    Then I should see "Competency A" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table/caption" "xpath_element"
    And I should see "not good" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[1]/td[1]" "xpath_element"
    And "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[1]/td[2]/input[@checked]" "xpath_element" should exist
    And I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][1]/table//tr[2]/td[2]/input" "xpath_element"
    # Competency B : Check the 'Do not bulk rate this competency' checkbox.
    And I should see "Competency B" in the "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]/table/caption" "xpath_element"
    And "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]//div[contains(@class, 'donotapplybulk')]//input[@checked]" "xpath_element" should not exist
    And I click on "//form[@id='savescalesvalues']//div[contains(@class, 'scale-comp-item')][2]//div[contains(@class, 'donotapplybulk')]//input" "xpath_element"
    # Save the task.
    And I click on "Save" "button"
    And I should see "Evaluations will be executed soon"
    And I set the field "group" to "Team 2"
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    And I set the field "group" to "Team 1"
    And I should see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be disabled
    And I set the field "group" to "All participants"
    And I should see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be disabled
    # Run the task and check the messages don't appear anymore.
    And I run all adhoc tasks
    And I set the field "group" to "Team 1"
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    And I set the field "group" to "All participants"
    And I should not see "Evaluation is in progress, please wait for it to finish before starting a new one"
    And the "Save" "button" should be enabled
    # Check that Rebecca and Pablo have been rated, but not Stepanie (who is in team 2).
    And I am on the "Module 2" "forum activity" page
    And I navigate to "Competencies assessment" in current page administration
    And I set the field "group" to "All participants"
    And I open the autocomplete suggestions list
    And I click on "Stepanie Grant" item in the autocomplete list
    And I should see "Not rated" in the "Competency A" "table_row"
    And I should see "Not rated" in the "Competency B" "table_row"
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    And I should see "good" in the "Competency A" "table_row"
    And I should see "qualified" in the "Competency B" "table_row"
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I should see "good" in the "Competency A" "table_row"
    And I should see "Not rated" in the "Competency B" "table_row"
