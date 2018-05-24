@regression
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentTermBundle:PaymentTermIntegration.yml
@fixture-OroApruveBundle:Checkout.yml
@fixture-OroWarehouseBundle:Checkout.yml
@ticket-BB-10071
Feature: Apruve Guest Checkout Integration
  In order to be able to purchase products using Apruve payment system
  As a Guest
  I want to be able to make orders without registration

  Scenario: Create two session
    Given sessions active:
      | Admin   | first_session  |
      | Guest   | second_session |

  Scenario: Create Apruve integration
    Given I proceed as the Admin
    And login as administrator
    And go to System/ Integrations/ Manage Integrations
    And click "Create Integration"
    When fill "Apruve Integration Form" with:
      | Type          | Apruve                           |
      | Name          | Apruve                           |
      | Label         | Apruve                           |
      | Short Label   | Apruve Short Label               |
      | Test Mode     | True                             |
      | API Key       | d0cbaf64fccdf9de4209895b0f8404ab |
      | Merchant ID   | 507c64f0cbcf190ce548d19e93d5c909 |
      | Status        | Active                           |
      | Default owner | John Doe                         |
    And I save and close form
    Then should see "Integration saved" flash message
    And I go to System/ Payment Rules
    And click "Create Payment Rule"
    And fill "Payment Rule Form" with:
      | Enable     | true     |
      | Name       | Apruve   |
      | Sort Order | 1        |
      | Currency   | $        |
      | Method     | [Apruve] |
    When save and close form
    Then should see "Payment rule has been saved" flash message

  Scenario: Enable guest shopping list setting
    Given I go to System/ Configuration
    And I follow "Commerce/Sales/Shopping List" on configuration sidebar
    And uncheck "Use default" for "Enable guest shopping list" field
    And I check "Enable guest shopping list"
    And I save form
    Then I should see "Configuration saved" flash message
    And the "Enable guest shopping list" checkbox should be checked

  Scenario: Enable guest checkout setting
    Given I follow "Commerce/Sales/Checkout" on configuration sidebar
    And uncheck "Use default" for "Enable Guest Checkout" field
    And I check "Enable Guest Checkout"
    When I save form
    Then the "Enable Guest Checkout" checkbox should be checked

  Scenario: Create Shopping List as unauthorized user
    Given I proceed as the Guest
    And I am on homepage
    And type "SKU123" in "search"
    And I click "Search Button"
    And I click "testname"
    And I click "Add to Shopping List"
    And I should see "Product has been added to" flash message
    When I click "Shopping List"
    And I should see "testname"

  Scenario: Successful order payment with Apruve
    Given I press "Create Order"
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
    And press "Continue"
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And on the "Payment" checkout step I press Continue
    And I uncheck "Save my data and create an account" on the checkout page
    And I press "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title

  Scenario: Check order status in admin panel after order creation and charge the order
    Given I proceed as the Admin
    And go to Sales/ Orders
    When click view "Payment authorized" in grid
    Then I should see order with:
      | Payment Method | Apruve             |
      | Payment Status | Payment authorized |
    And click "Send Invoice" on row "Authorize" in grid "Order Payment Transaction Grid"
    And click "Yes, Charge"
    Then should see "Invoice has been sent successfully" flash message
    And I should see order with:
      | Payment Status | Invoiced |
    And I should see following "Order Payment Transaction Grid" grid:
      | Payment Method | Type      | Successful |
      | Apruve         | Shipment  | Yes        |
      | Apruve         | Invoice   | Yes        |
      | Apruve         | Authorize | Yes        |

