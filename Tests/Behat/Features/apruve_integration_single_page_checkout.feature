@regression
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentTermBundle:PaymentTermIntegration.yml
@fixture-OroApruveBundle:Checkout.yml
@fixture-OroWarehouseBundle:Checkout.yml
Feature: Apruve integration Single Page Checkout
  ToDo: BAP-16103 Add missing descriptions to the Behat features

  Scenario: Create different window session
    Given sessions active:
      | Admin          |first_session |
      | User           |second_session|

  Scenario: Create Apruve integration
    Given I proceed as the Admin
    And I login as administrator
    And I enable the existing warehouses
    And go to System/ Integrations/ Manage Integrations
    And click "Create Integration"
    When fill "Apruve Integration Form" with:
      | Type        | Apruve                           |
      | Name        | Apruve                           |
      | Label       | Apruve                           |
      | Short Label | Apruve Short Label               |
      | Test Mode   | True                             |
      | API Key     | d0cbaf64fccdf9de4209895b0f8404ab |
      | Merchant ID | 507c64f0cbcf190ce548d19e93d5c909 |
      | Status      | Active                           |
    And save form
    Then should see "Integration saved" flash message
    And I go to System/ Payment Rules
    And click "Create Payment Rule"
    And fill "Payment Rule Form" with:
      | Enable     | true     |
      | Name       | Apruve   |
      | Sort Order | 1        |
      | Currency   | $        |
      | Method     | [Apruve] |
    And save and close form
    Then should see "Payment rule has been saved" flash message

  Scenario: Enable SinglePage checkout
    Given go to System/Workflows
    When I click "Activate" on row "Single Page Checkout" in grid
    And I click "Activate"
    Then I should see "Workflow activated" flash message

  Scenario: Check out and cancel with Apruve integration
    Given I proceed as the User
    And I signed in as AmandaRCole@example.org on the store frontend
    When I open page with shopping list List 2
    And I click "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" from "Select Billing Address"
    And I select "Fifth avenue, 10115 Berlin, Germany" from "Select Shipping Address"
    And I check "Flat Rate" on the checkout page
    And I click "Submit Order"
    Then I should see "We were unable to process your payment. Please verify your payment information and try again." flash message

  Scenario: Check order status in admin panel after order creation
    Given I proceed as the Admin
    When I go to Sales/ Orders
    Then there is no "Payment Authorized" in grid

  Scenario: Successful order payment with Apruve integration
    Given I proceed as the User
    When I open page with shopping list List 1
    And I click "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" from "Select Billing Address"
    And I select "Fifth avenue, 10115 Berlin, Germany" from "Select Shipping Address"
    And I check "Flat Rate" on the checkout page
    And I click "Submit Order"
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
