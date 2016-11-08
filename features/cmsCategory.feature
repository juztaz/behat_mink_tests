# features/cms-category.feature
Feature: CMS Category
  In order to use CMS
  As a website user
  I need to be able to perform CRUD operations with Category static property

  Background:
    Given I am logged in "demo" with "admin"

  @mink:selenium2
  Scenario: Access category page
    When I click "Categories"
    Then I should be in "Categories" page	

  @mink:selenium2
  Scenario: Access category creation page
    When I click "Categories"
    And press "Create new"
    Then I should be in "Category" page

  @mink:selenium2
  Scenario: Create new category
    When I go to "/#/category/new" page
    And I fill in "general_slugInput" with "atp-category-slug"
    Then I click "EN" tab
    And I fill in "en_titleInput" with "ATP Category Title EN"
    And I fill in "en_slugInput" with "atp-category-slug-en"
    Then I click "DE" tab
    And I fill in "de_titleInput" with "ATP Category Title DE"
    And I fill in "de_slugInput" with "atp-category-slug-de"
    Then I click "ES" tab
    And I fill in "es_titleInput" with "ATP Category Title ES"
    And I fill in "es_slugInput" with "atp-category-slug-es"
    Then I click "IT" tab
    And I fill in "it_titleInput" with "ATPCategory Title IT"
    And I fill in "it_slugInput" with "atp-category-slug-it"
    Then I click "FR" tab
    And I fill in "fr_titleInput" with "ATP Category Title FR"
    And I fill in "fr_slugInput" with "atp-category-slug-fr"
    And I press "Save"
    Then I should see notification with "Data saved successfully"

  @mink:selenium2
  Scenario: Edit existing category
    When I click "Categories"
    And I click "edit" for "ATP Category Title EN" property
    Then I fill in "general_slugInput" with "atp-category-slug-edited"
    And I press "Save"
    Then I should see notification with "Data saved successfully"

  @mink:selenium2
  Scenario: Delete existing category
    When I click "Categories"
    And I click "delete" for "ATP Category Title EN" property
    And in confirmation window I click "Cancel"
    Then I should "see" "ATP Category Title EN" in the list
    And I click "delete" for "ATP Category Title EN" property
    And in confirmation window I click "Yes"
    Then I should "not see" "ATP Category Title EN" in the list
    
      