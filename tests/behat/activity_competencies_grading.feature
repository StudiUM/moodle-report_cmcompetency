@report @javascript @report_cmcompetency
Feature: Grade the competencies for an activity
  As a teacher

  Background:
    Given the cmcompetency fixtures exist
    And I log in as "teacher"
    And I am on "Anatomy" course homepage

  Scenario: Rate the activity 1 (group assignment)
    # For Rebecca
    Given I am on the "Module 1" "assign activity" page
    When I navigate to "Competencies assessment" in current page administration
    Then I should see "Module 1" in the "//h2" "xpath_element"
    And I should see "Rebecca Armenta"
    And I should see "Competencies assessment"
    And I should see "not good" in the "Competency A" "table_row"
    And I click on "not good" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I click on "Rate" "button"
    And "Rate" "dialogue" should be visible
    And I set the field "rating" to "very good"
    And I set the field "Evidence notes" to "This is a note for Rebecca and Pablo"
    And "Apply rating and evidence notes to the entire group" "checkbox" should exist
    And the cmcompetency "Apply rating and evidence notes to the entire group" "checkbox" should be checked
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 1" in the "User competency summary" "dialogue"
    And I should see "very good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I click on "very good" "link" in the "Competency A" "table_row"
    And I should see "This is a note for Rebecca and Pablo" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "very good" in the "Competency A" "table_row"
    
    # For Pablo
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I should see "very good" in the "Competency A" "table_row"
    And I click on "very good" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "very good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "This is a note for Rebecca and Pablo" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Rate" "button"
    And "Rate" "dialogue" should be visible
    And I set the field "rating" to "not good"
    And I set the field "Evidence notes" to "This is a note for Pablo only"
    And "Apply rating and evidence notes to the entire group" "checkbox" should exist
    And the cmcompetency "Apply rating and evidence notes to the entire group" "checkbox" should be checked
    And I click on "Apply rating and evidence notes to the entire group" "checkbox"
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 1" in the "User competency summary" "dialogue"
    And I should see "not good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "This is a note for Pablo only" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "not good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "This is a note for Rebecca and Pablo" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[2]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[2]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[2]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "not good" in the "Competency A" "table_row"

    # For Rebecca - keeps same evaluation because group evaluation not applied.
    And I open the autocomplete suggestions list
    And I click on "Rebecca Armenta" item in the autocomplete list
    And I should see "very good" in the "Competency A" "table_row"
    And I click on "very good" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "very good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "This is a note for Rebecca and Pablo" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"

 Scenario: Rate the activity 1 (forum - no groups)
    # For Rebecca
    Given I am on the "Module 2" "forum activity" page
    When I navigate to "Competencies assessment" in current page administration
    Then I should see "Module 2" in the "//h2" "xpath_element"
    And I should see "Rebecca Armenta"
    And I should see "Competencies assessment"
    And I should see "Not rated" in the "Competency A" "table_row"
    And I click on "Not rated" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I click on "Rate" "button"
    And "Rate" "dialogue" should be visible
    And I set the field "rating" to "very good"
    And I set the field "Evidence notes" to "This is a note for Rebecca"
    And "Apply rating and evidence notes to the entire group" "checkbox" should not exist
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 2" in the "User competency summary" "dialogue"
    And I should see "very good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I click on "very good" "link" in the "Competency A" "table_row"
    And I should see "This is a note for Rebecca" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "very good" in the "Competency A" "table_row"
    
    # For Pablo
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I should see "Not rated" in the "Competency A" "table_row"
    And I click on "Not rated" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I click on "Rate" "button"
    And "Rate" "dialogue" should be visible
    And I set the field "rating" to "not good"
    And I set the field "Evidence notes" to "This is a note for Pablo only"
    And "Apply rating and evidence notes to the entire group" "checkbox" should not exist
    And I click on "//button[contains(@data-action, 'save')]" "xpath_element"
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 2" in the "User competency summary" "dialogue"
    And I should see "not good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I click on "not good" "link" in the "Competency A" "table_row"
    And I should see "This is a note for Pablo only" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "not good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "not good" in the "Competency A" "table_row"