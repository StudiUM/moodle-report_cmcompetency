@report @javascript @report_cmcompetency
Feature: View the competencies report for an activity
  As a teacher

  Background:
    Given the cmcompetency fixtures exist
    And I log in as "teacher"
    And I am on "Anatomy" course homepage

  Scenario: View the competency report in activity 1
    # For Rebecca
    Given I am on the "Module 1" "assign activity" page
    When I navigate to "Competencies assessment" in current page administration
    Then I should see "Module 1" in the "//h1" "xpath_element"
    And I should see "Rebecca Armenta"
    And I should see "Competencies assessment"
    And I should not see "Competency B"
    And I should see "not good" in the "Competency A" "table_row"
    And I click on "Competency A" "link"
    And "Competency A" "dialogue" should be visible
    And I click on "Close" "button" in the "Competency A" "dialogue"
    And I click on "not good" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 1" in the "User competency summary" "dialogue"
    And I should see "not good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "My note for Rebecca" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "not good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    # For Pablo
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I should not see "Competency B"
    And I should see "good" in the "Competency A" "table_row"
    And I click on "Competency A" "link"
    And "Competency A" "dialogue" should be visible
    And I click on "Close" "button" in the "Competency A" "dialogue"
    And I click on "good" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 1" in the "User competency summary" "dialogue"
    And I should see "good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"

  Scenario: View the competency report in activity 2
    # For Rebecca
    Given I am on the "Module 2" "forum activity" page
    When I navigate to "Competencies assessment" in current page administration
    Then I should see "Module 2" in the "//h1" "xpath_element"
    And I should see "Rebecca Armenta"
    And I should see "Competencies assessment"
    And I should see "Not rated" in the "Competency A" "table_row"
    And I should see "qualified" in the "Competency B" "table_row"
    And I click on "Competency A" "link"
    And "Competency A" "dialogue" should be visible
    And I click on "Close" "button" in the "Competency A" "dialogue"
    And I click on "Competency B" "link"
    And "Competency B" "dialogue" should be visible
    And I click on "Close" "button" in the "Competency B" "dialogue"
    And I click on "Not rated" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 2" in the "User competency summary" "dialogue"
    And I should see "-" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I click on "qualified" "link" in the "Competency B" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency B" in the "User competency summary" "dialogue"
    And I should see "Module 2" in the "User competency summary" "dialogue"
    And I should see "qualified" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "qualified" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    # For Pablo
    And I open the autocomplete suggestions list
    And I click on "Pablo Menendez" item in the autocomplete list
    And I should see "Not rated" in the "Competency A" "table_row"
    And I should see "Not rated" in the "Competency B" "table_row"
    And I click on "Competency B" "link"
    And "Competency B" "dialogue" should be visible
    And I click on "Close" "button" in the "Competency B" "dialogue"
    And I click on "Not rated" "link" in the "Competency A" "table_row"
    And I wait "1" seconds
    And "User competency summary" "dialogue" should be visible
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 2" in the "User competency summary" "dialogue"
    And I should see "-" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"