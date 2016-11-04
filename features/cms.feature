# features/cms.feature
Feature: CMS
  In order to use CMS
  As a website user
  I need to be able to sign in

  @mink:selenium2
  Scenario: Sign in screen
    Given I go to "demo" CMS
    Then I should see ".form-signin" element
    And I can see that page is disabled

  @mink:selenium2
  Scenario: Sign in functionality
    Given I see ".form-signin" element
    When I fill in "email" with "admin@mydriver.com"
    And I fill in "password" with "password"
    And I press "Sign in"
    Then I should not see ".form-signin" element
    And I can see that page is not disabled
  
  @mink:selenium2
  Scenario: Sign out functionality
    Given I cannot see ".form-signin" element
    And I can see that page is not disabled
    When I click "Logout"
    Then I should see ".form-signin" element
    And I can see that page is disabled

