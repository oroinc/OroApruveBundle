@regression
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentTermBundle:PaymentTermIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroApruveBundle:Checkout.yml
@fixture-OroWarehouseBundle:Checkout.yml
@behat-test-env
Feature: Apruve Checkout Integration
  In order to be able to purchase products using Apruve payment system
  As a Buyer
  I want to be able to make orders under Checkout

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |

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
    When I go to System/ Payment Rules
    And I click "Create Payment Rule"
    And I fill "Payment Rule Form" with:
      | Enable     | true     |
      | Name       | Apruve   |
      | Sort Order | 1        |
      | Currency   | $        |
      | Method     | [Apruve] |
    And I save and close form
    Then I should see "Payment rule has been saved" flash message

  Scenario: Unsuccessful order payment with Apruve integration
    Given I proceed as the Buyer
    And Currency is set to USD
    And I enable the existing warehouses
    And I signed in as AmandaRCole@example.org on the store frontend
    When I open page with shopping list List 2
    And I press "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Billing Information" checkout step and press Continue
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Shipping Information" checkout step and press Continue
    And on the "Shipping" checkout step I press Continue
    And on the "Payment" checkout step I press Continue
    And I click "Submit Order"
    Then I should see "We were unable to process your payment. Please verify your payment information and try again." flash message

  Scenario: Check order status in admin panel after unsuccessful order creation
    Given I proceed as the Admin
    When I go to Sales/ Orders
    Then there is no "Payment Authorized" in grid

  Scenario: Successful order payment with Apruve integration
    Given I proceed as the Buyer
    When I open page with shopping list List 1
    And I press "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Billing Information" checkout step and press Continue
    And I select "Fifth avenue, 10115 Berlin, Germany" on the "Shipping Information" checkout step and press Continue
    And on the "Shipping" checkout step I press Continue
    And on the "Payment" checkout step I press Continue
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

#  This part seems impossible to implement without actual requests to apruve servers during behat scenarios
#  Scenario: Cofirm order from Apruve
#    Given I switch to the "Apruve" session
#    And I go to "https://test.apruve.com/"
#    And fill in "user_email" with "apruve-qa+buyer@orocommerce.com"
#    And fill in "user_password" with "wyVjpjA2"
#    And click "Log in"
#    And switch to the "Invoices" tab
#    And select open invoice
#    And click "Pay"
#    And select "Paper Check" option
#    And click "Pay"
#    And should see "Thank you!"
#
#  Scenario: Check order status in admin panel after confirm from Apruve
#    Given I proceed as the Admin
#    And login as administrator
#    And go to Sales/ Orders
#    When click view "Amanda Cole" in grid
#    Then I should see order with:
#      | Payment Method | Apruve       |
#      | Payment Status | Paid in full |
