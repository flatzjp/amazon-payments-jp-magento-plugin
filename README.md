## はじめに

Magento用のAmazon ログイン&ペイメントモジュールを日本のAmazon ログイン＆ペイメントにローカライズしたモジュールです。
本モジュールを利用することで、Magentoで日本のAmazonログイン＆ペイメントを利用することが可能になります。

日本のAmazonログイン＆ペイメントの詳細については https://payments.amazon.co.jp/ を参照してください。

## Quick Start - Login and Pay with Amazon for Magento

[View the Complete User Guide](https://github.com/amzn/amazon-payments-magento-plugin/wiki)
or [Learn More about Amazon Payments] (https://payments.amazon.com/sp/magento)


### Pre-Requisites
* Magento CE 1.6+ or EE 1.11+.
    * Magento 1.5 is supported with a patch see [here](https://github.com/amzn/amazon-payments-magento-plugin/wiki/Magento-1.5-Compatibility) for more information
* SSL is installed on your site and active on Checkout and Login pages
* Compilation is turned off in your Magento system


### Installation
> **NOTE** Before you begin, make a backup of your Magento site.

* Using Magento Connect (Recommended):
    * http://www.magentocommerce.com/magento-connect/pay-with-amazon-for-magento.html
* Using Manual Installation:
    * Click the Download Zip button and save to your local machine
    * Transfer the zip file to your Magento webserver
    * Unpack the archive in the root directory of your Magento instance
    * Flush your Magento caches
        * In the admin page for your Magento instance, navigate to System->Cache Management
        * Click the 'Flush Magento Cache'
        * More information on Magento Cache Management [here](http://www.magentocommerce.com/knowledge-base/entry/cache-storage-management)
    * Log out of the admin page and then log back in to ensure activation of the module


### Configure Magento
* The plugin is configured under "System" (top-nav) -> "Configuration" (menu) -> "Payment Methods" (left nav) -> "Amazon Payments" (main page).
* If you haven't already registered, use the link in the module to create an Amazon Payments account.
* Apply your Amazon account keys, shown during registration or find them in Seller Central:
    * Merchant/Seller ID
       * Switch to "Amazon Payments Advanced" in the top nav
       * Navigate to "Settings" (top-nav) -> "Integration Settings" (menu)
    * API Access and Secret Key
       * Navigate to "Integration" (top-nav) -> "MWS Access Keys" (menu)
    * Client and Secret Key (under "Login with Amazon")
       * Switch to "Login with Amazon" in the top-nav
       * Click "Register a New Application" if you haven't done so already
       * Find the client and secret key under "Web Settings"


### Configure Amazon Payments
 * Allow your site to use your Login with Amazon account:
    * Switch to "Login with Amazon" in the top-nav
    * Add any domains your site is hosted on under "Allowed Javascript Origins" (i.e., https://www.yourdomain.com, https://yourdomain.com)
 * Add your store logo to the Amazon Login screen
    * Upload a 150x150 image under "Application Information" -> "Logo Image"
 * Complete your store setup
    * Switch to "Amazon Payments Advanced" in the top nav
    * Navigate to "Settings" (top-nav) -> "Account Info" (menu)
    * Complete the "Seller Information" information which is used in Buyer-facing communication
    * Complete the tax interview under "Legal Entity"
    * Add your bank account for disbursements under "Deposit Information"

## Release Notes

v1.2 Release - Asynchronous Authorization
Login and Pay for Magento now support asynchronous authorizations!
How it works
* In the admin configuration, you enable Asynchronous mode by setting the 'Asynchronous Mode' to 'Yes' Now when buyers checkout, the call is asynchronous and the state returned from any Authorize call is 'Pending'
* The system uses Magento's built-in cron job functionality to poll Amazon systems for the status of any Open orders. See http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/how_to_setup_a_cron_job for more information.
* NOTE Orders will never update to their correct status if you do not have cron enabled as specified in the Magento documentation.
* The polling interval is 5 minutes and when the cron job executes, it will get the new status, update the order in Magento appropriately to your configured 'New Order Status' and, if Authorize and Capture is configured, create the Invoice in Magento.
When should you use async vs sync?
* You should use async when you have large average order values, on the order of > $500-$1000
* You might also consider using async to speed up checkout since the authorizations come back immediately with a 'Pending' status.
* Only merchants who have an existing workflow for reaching out to customer's whose payment method was declined should use the asynchronous model.

Features:
* Developer client restrictions
** #78 Implement Developer Client Restrictions
* Sort order variable
** #75 Fire/Onestep/IWD : Add sort order variable that determines where Amazon Payments shows up in list
* Works with iwd onepage checkout/firecheckout
** #77 Fire/Onestep/IWD : Amazon address pullled into form when buyer bails out of Amazon flow
** #74 Fire/Onestep/IWD : Launch amazon checkout (login) on radio button select
Bug Fixes
* #88 Standalone Checkout with Modal: Content blocked when account verification required
* #85 PaymentPlanNotSet exception when invalid payment method.
* #83 CE v1.5 missing core public method lookupTransaction
* #82 Orders break when plugin disabled.
* #81 Display as Payment Option setting is not respected in Onestepcheckout
* #80 Use CSS !Important for Place Order Button

v1.1.2
Features
* Integration with Firecheckout extension
* Integration with IWD OnePage checkout extension
* Capture shipping address in customer address book in Magento
* Allow configuration for secure cart (on/off). Allows for AJAX "Add to Cart" extensions to function and/or merchants to function without a secure cart
Issues Resolved
* Fix #62 - add Amazon address to customer shipping address book when order is placed, checking for duplicates 5ef24c4
* Fix #40 - allow 115% or $75 over-refunds, whichever is smaller 51573d9
* Add configuration for secure cart on/off #71 b720bf6
* Fix iundefined isSecureCart #71 b4943d5
* Fix redirect if secure URL config is not HTTPS #71 f95734f
* #24 more fixes for undefined index notices on certain themes 7dd2e20
* Add Amazon pay button under Payment Info (enabled in config, off by default) for thrid-party checkouts 96fbb35
* #72 add payment option pay button 4721339
* Fix order state to be processing for auth & capture 27bfec4
* Fix customer address update for virtual (no shipping address) orders cea65e4
* Fix order status e5a8015

v1.1.1
* Fix product shortcut button - JS SyntaxError: missing ) after argumen… … 0157078
* Fix #60 remove port numbers from URL in help text 7cdde2c
* Fix #57 hide amazon payments method if no amazon session a8e2fde
* Adding package file such that tgz downloads are installable in Magent… … f33fb83
* Add payments button block reference to onepage f8fe834
* Fix#24 - overrode Progress block to prevent 'undefined index: widget' error 3e6e9b4
* Fix #66 fix terms and conditions on stand alone checkout 8ead600
* Update README.md 582ed69
* Shortened authorizationReferenceId e735839
* Fix #67 add spinner and opacity to review on submit 1e762cf
* Fix #69 - remove undefined blackslash 872d6dd
* Fix #70 - skip payment processing on -bash orders 73d7a20
* Add benchmark for Amazon API calls and optimized order error checking

v1.1.0
* Updated payments admin screen to make it easier to configure.
* Fixed JQuery conflict on product detail button shortcut.
* Fixed compilation issue. (Issue #52)
* Added button styling options. (Issue #47)

v1.0.1
* Fixed NOTICE and LICENSE
