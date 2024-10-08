@regression
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentTermBundle:PaymentTermIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroApruveBundle:Checkout.yml
@fixture-OroWarehouseBundle:Checkout.yml
@behat-test-env
Feature: Apruve integration Single Page Checkout
  In order to be able to purchase products using Apruve payment system
  As a Buyer
  I want to be able to make orders under Single Page Checkout

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |
    And I activate "Single Page Checkout" workflow

  Scenario: Create Apruve integration
    Given I proceed as the Admin
    And I login as administrator
    And I enable the existing warehouses
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
    And I create payment rule with "Apruve" payment method

  Scenario: Check out and cancel with Apruve integration
    Given I proceed as the Buyer
    And I signed in as AmandaRCole@example.org on the store frontend
    When I open page with shopping list List 2
    And I click "Create Order"
    And I select "ORO, Fifth avenue, 10115 Berlin, Germany" from "Billing Address"
    And I select "ORO, Fifth avenue, 10115 Berlin, Germany" from "Shipping Address"
    And I check "Flat Rate" on the checkout page
    And I click "Submit Order"
    Then I should see "We were unable to process your payment. Please verify your payment information and try again." flash message

  Scenario: Check order status in admin panel after unsuccessful order creation
    Given I proceed as the Admin
    When I go to Sales/ Orders
    Then there is no "Payment Authorized" in grid

  Scenario: Successful order payment with Apruve integration
    Given I proceed as the Buyer
    When I open page with shopping list List 1
    And I click "Create Order"
    And I select "ORO, Fifth avenue, 10115 Berlin, Germany" from "Billing Address"
    And I select "ORO, Fifth avenue, 10115 Berlin, Germany" from "Shipping Address"
    And I check "Flat Rate" on the checkout page
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title

  Scenario: Check order status in admin panel after order creation and charge the order
    Given I proceed as the Admin
    And I go to Sales/ Orders
    When I click view "Payment authorized" in grid
    Then I should see order with:
      | Payment Method | Apruve             |
      | Payment Status | Payment authorized |
    And I click "Send Invoice" on row "Authorize" in grid "Order Payment Transaction Grid"
    When I click "Yes, Charge"
    Then I should see "Invoice has been sent successfully" flash message
    And I should see order with:
      | Payment Status | Invoiced |
    And I should see following "Order Payment Transaction Grid" grid:
      | Payment Method | Type      | Successful |
      | Apruve         | Shipment  | Yes        |
      | Apruve         | Invoice   | Yes        |
      | Apruve         | Authorize | Yes        |
