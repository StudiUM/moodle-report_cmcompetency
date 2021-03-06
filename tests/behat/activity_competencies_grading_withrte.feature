@report @javascript @report_cmcompetency @_file_upload
Feature: Grade the competencies for an activity
  As a teacher

  Background:
    Given richtext editor is enabled
    And the cmcompetency fixtures exist
    And I log in as "teacher"
    And I am on "Anatomy" course homepage

  Scenario: Rate the activity 1 (group assignment)
    # For Rebecca
    Given I follow "Private files"
    And I upload "report/cmcompetency/tests/fixtures/moodlelogo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on "Anatomy" course homepage
    And I follow "Module 1"
    When I navigate to "Competencies assessment" in current page administration
    Then I should see "Module 1" in the "//h2" "xpath_element"
    And I should see "Rebecca Armenta"
    And I should see "Competencies assessment"
    And I should see "not good" in the "Competency A" "table_row"
    And I click on "not good" "link" in the "Competency A" "table_row"
    And "User competency summary" "dialogue" should be visible
    And I click on "Rate" "button"
    And "Rate" "dialogue" should be visible
    And I set the field "rating" to "very good"
    And I set the field "Evidence notes" to "This is a note for Rebecca and Pablo"
    And "Apply rating and evidence notes to the entire group" "checkbox" should exist
    And the cmcompetency "Apply rating and evidence notes to the entire group" "checkbox" should be checked
    And I click on "Insert or edit image" "button" in the "//*[@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodlelogo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "Moodle logo"
    And I click on "Save image" "button"
    And I click on "//input[contains(@data-action, 'rate')]" "xpath_element"
    And I should see "Competency A" in the "User competency summary" "dialogue"
    And I should see "Module 1" in the "User competency summary" "dialogue"
    And I should see "very good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I click on "very good" "link" in the "Competency A" "table_row"
    And I should see "This is a note for Rebecca and Pablo" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]//img[contains(@src, 'moodlelogo.png') and @alt='Moodle logo']" "xpath_element" should exist
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I should see "very good" in the "Competency A" "table_row"
    
    # For Pablo
    And I set the field with xpath "//input[contains(@id, 'form_autocomplete_input')]" to "Pablo"
    And I click on "Pablo Menendez" item in the autocomplete list
    And I should see "very good" in the "Competency A" "table_row"
    And I click on "very good" "link" in the "Competency A" "table_row"
    And "User competency summary" "dialogue" should be visible
    And I should see "very good" in the "//dl/dt[text()='Rating']/following-sibling::dd[1]" "xpath_element"
    And I should see "This is a note for Rebecca and Pablo" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]//img[contains(@src, 'moodlelogo.png') and @alt='Moodle logo']" "xpath_element" should exist
    And I should see "The competency rating was manually set in the course activity" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
    And I should see "very good" in the "//dl/dt[text()='Evidence']/following-sibling::dd[1]/div[1]" "xpath_element"
