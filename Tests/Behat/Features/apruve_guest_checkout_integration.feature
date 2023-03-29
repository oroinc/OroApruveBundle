@regression
@ticket-BB-10071
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentTermBundle:PaymentTermIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroApruveBundle:Checkout.yml
@fixture-OroWarehouseBundle:Checkout.yml
@behat-test-env
Feature: Apruve Guest Checkout Integration
  In order to be able to purchase products using Apruve payment system
  As a Guest
  I want to be able to make orders without registration

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Guest | second_session |

  Scenario: Create Apruve integration
    Given I proceed as the Admin
    And I login as administrator
    And I go to System/ Integrations/ Manage Integrations
    And I click "Create Integration"
    When I fill "Apruve Integration Form" with:
      | Type          | Apruve                           |
      | Name          | Apruve                           |
      | Label         | Apruve                           |
      | Short Label   | Apruve Short Label               |
      | Test Mode     | True                             |
      | API Key       | d0cbaf64fccdf9de4209895b0f8404ab |
      | Merchant ID   | 507c64f0cbcf190ce548d19e93d5c909 |
      | Status        | Active                           |
      | Default owner | John Doe                         |
    And I click "Check Apruve connection"
    And I should see "Apruve Connection is valid" flash message
    And I save form
    Then I should see "Integration saved" flash message
    And I should see "/admin/apruve/webhook/notify/"
    When I go to System/ Payment Rules
    And I create payment rule with "Apruve" payment method

  Scenario: Enable Guest Shopping List setting
    Given I go to System/ Configuration
    And I follow "Commerce/Sales/Shopping List" on configuration sidebar
    And uncheck "Use default" for "Enable Guest Shopping List" field
    And I check "Enable Guest Shopping List"
    When I save form
    Then I should see "Configuration saved" flash message
    And the "Enable Guest Shopping List" checkbox should be checked

  Scenario: Enable guest checkout setting
    Given I follow "Commerce/Sales/Checkout" on configuration sidebar
    And uncheck "Use default" for "Enable Guest Checkout" field
    And I check "Enable Guest Checkout"
    When I save form
    Then the "Enable Guest Checkout" checkbox should be checked

  Scenario: Create Shopping List as unauthorized user
    Given I proceed as the Guest
    And I am on homepage
    And I type "SKU123" in "search"
    And I click "Search Button"
    And I click "testname"
    When I click "Add to Shopping List"
    Then I should see "Product has been added to" flash message and I close it
    When I open shopping list widget
    And I click "View List"
    Then I should see "testname"

  Scenario: Successful order payment with Apruve
    Given I click on "Create Order"
    And I click "Continue as a Guest"
    And I fill form with:
      | First Name      | Tester1         |
      | Last Name       | Testerson       |
      | Email           | tester@test.com |
      | Street          | Fifth avenue    |
      | City            | Berlin          |
      | Country         | Germany         |
      | State           | Berlin          |
      | Zip/Postal Code | 10115           |
    And I click "Ship to This Address"
    And I click "Continue"
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And on the "Payment" checkout step I press Continue
    And I uncheck "Save my data and create an account" on the checkout page
    When I press "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title

  Scenario: Check order status in admin panel after order creation and charge the order
    Given I proceed as the Admin
    And I go to Sales/ Orders
    When I click view "Payment authorized" in grid
    Then I should see order with:
      | Payment Method | Apruve             |
      | Payment Status | Payment authorized |
    And I click "Send Invoice" on row "Authorize" in grid "Order Payment Transaction Grid"
    And I click "Yes, Charge"
    And I should see "Invoice has been sent successfully" flash message
    And I should see order with:
      | Payment Status | Invoiced |
    And I should see following "Order Payment Transaction Grid" grid:
      | Payment Method | Type      | Successful |
      | Apruve         | Shipment  | Yes        |
      | Apruve         | Invoice   | Yes        |
      | Apruve         | Authorize | Yes        |
