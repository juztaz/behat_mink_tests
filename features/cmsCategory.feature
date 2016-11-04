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


