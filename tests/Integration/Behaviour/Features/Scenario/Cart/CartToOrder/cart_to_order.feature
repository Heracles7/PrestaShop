@reset-database-before-feature
Feature: Check cart to order data copy
  As a customer
  I must be able to have a correct order when validating payment step

  Scenario: 1 product in cart, 1 cart rule
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for quantities between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule1"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Then current cart order total for products should be 20.6 tax included
    Then current cart order total for products should be 19.81 tax excluded
    Then current cart order total discount should be 10.3 tax included
    Then current cart order total discount should be 9.91 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded
    Then current cart order should have a discount in position 1 with an amount of 10.3 tax included and 9.91 tax excluded
    Then customer "customer1" should have 0 cart rules that apply to him

  Scenario: 1 product in cart, 2 cart rules
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for quantities between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule1"
    When I use the discount "cartrule2"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Then current cart order total for products should be 20.6 tax included
    Then current cart order total for products should be 19.81 tax excluded
    Then current cart order total discount should be 15.45 tax included
    Then current cart order total discount should be 14.86 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded
    Then current cart order should have a discount in position 1 with an amount of 10.3 tax included and 9.91 tax excluded
    Then current cart order should have a discount in position 2 with an amount of 5.15 tax included and 4.95 tax excluded
    Then customer "customer1" should have 0 cart rules that apply to him

  Scenario: 3 product in cart, 1 cart rule
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given product "product2" belongs to tax group "taxrule1"
    Given product "product3" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for quantities between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product2" in my cart
    When I add 1 items of product "product1" in my cart
    When I add 2 items of product "product3" in my cart
    When I use the discount "cartrule1"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Then current cart order total for products should be 119.15 tax included
    Then current cart order total for products should be 114.58 tax excluded
    Then current cart order total discount should be 59.58 tax included
    Then current cart order total discount should be 57.29 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded
    Then current cart order should have a discount in position 1 with an amount of 59.58 tax included and 57.29 tax excluded
    Then customer "customer1" should have 0 cart rules that apply to him

  Scenario: 3 product in cart, 3 cart rules
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a product in the catalog named "product2" with a price of 32.388 and 1000 items in stock
    Given there is a product in the catalog named "product3" with a price of 31.188 and 1000 items in stock
    Given there is a cart rule named "cartrule1" that applies a percent discount of 50.0% with priority 1, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule1" has a discount code "foo1"
    Given there is a cart rule named "cartrule2" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule2" has a discount code "foo2"
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given product "product2" belongs to tax group "taxrule1"
    Given product "product3" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for quantities between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product2" in my cart
    When I add 1 items of product "product1" in my cart
    When I add 2 items of product "product3" in my cart
    When I use the discount "cartrule1"
    When I use the discount "cartrule2"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Then current cart order total for products should be 119.15 tax included
    Then current cart order total for products should be 114.58 tax excluded
    Then current cart order total discount should be 89.36 tax included
    Then current cart order total discount should be 85.94 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded
    Then current cart order should have a discount in position 1 with an amount of 59.58 tax included and 57.29 tax excluded
    Then current cart order should have a discount in position 2 with an amount of 29.79 tax included and 28.65 tax excluded
    Then customer "customer1" should have 0 cart rules that apply to him

  Scenario: 1 product in cart, 1 cart rule with too-much amount
    Given I have an empty default cart
    Given email sending is disabled
    Given shipping handling fees are set to 2.0
    Given there is a product in the catalog named "product1" with a price of 19.812 and 1000 items in stock
    Given there is a cart rule named "cartrule5" that applies an amount discount of 500.0 with priority 5, quantity of 1000 and quantity per user 1000
    Given cart rule "cartrule5" has a discount code "foo5"
    Given there is a zone named "zone1"
    Given there is a country named "country1" and iso code "FR" in zone "zone1"
    Given there is a state named "state1" with iso code "TEST-1" in country"country1" and zone "zone1"
    Given there is an address named "address1" with postcode "1" in state "state1"
    Given there is a tax named "tax1" and rate 4.0%
    Given there is a tax rule named "taxrule1"in country "country1" and state "state1" where tax "tax1" is applied
    Given product "product1" belongs to tax group "taxrule1"
    Given there is a customer named "customer1" whose email is "fake@prestashop.com"
    Given address "address1" is associated to customer "customer1"
    Given there is a carrier named "carrier1"
    Given carrier "carrier1" ships to all groups
    Given carrier "carrier1" applies shipping fees of 5.0 in zone "zone1" for quantities between 0 and 10000
    When I am logged in as "customer1"
    When I add 1 items of product "product1" in my cart
    When I use the discount "cartrule5"
    When I select address "address1" in my cart
    When I select carrier "carrier1" in my cart
    When I validate my cart using payment module fake
    Then current cart order total for products should be 20.6 tax included
    Then current cart order total for products should be 19.81 tax excluded
    Then current cart order total discount should be 20.6 tax included
    Then current cart order total discount should be 19.81 tax excluded
    Then current cart order shipping fees should be 7.0 tax included
    Then current cart order shipping fees should be 7.0 tax excluded
    Then current cart order should have a discount in position 1 with an amount of 20.6 tax included and 19.81 tax excluded
    Then customer "customer1" should have 1 cart rules that apply to him
    Then cart rule for customer "customer1" in position 1 should apply a discount of 480.19
