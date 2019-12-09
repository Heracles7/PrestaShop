# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s misc
@reset-database-before-feature
Feature: Switch debug mode
  In order to see or hide what exactly is causing the error
  As a BO user
  I need to be able to enable or disable debug mode

  Scenario: Enable debug mode
    When I enable debug mode
    Then debug mode should be enabled in the configuration

  Scenario: Disable debug mode
    When I disable debug mode
    Then debug mode should be disabled in the configuration
