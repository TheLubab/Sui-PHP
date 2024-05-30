=== Login with Sui ===
Tags: Sui, login, blockchain
Requires at least: 6.0.1
Requires PHP: 7.4
Tested up to: 6.1
Stable tag: 0.0.1
License: MIT

Login with Sui WordPress plugin, allows users to register/login with Sui wallets.

== Description ==

Login with Sui WordPress plugin allows to easy setup and customize login with Sui functionality at your WordPress site by using simple shortcode.

**Benefits**

1. **Provides the most popular Sui Wallets**

2. **Easy registration and login**: If user has a Sui Wallet, he already can use all functionality from your site and identified as WP User.

3. **Call smart contracts from single endpoint**: Plugin is a wrapped implementation for official Sui Typescript SDK. 

4. **Easy to modify**: You can easily change login, logout button text and provide advanced classed to customize style from code

== For plugin developers ==
* If you want to create plugin with smart contract and call it from logged users you simple use the global indentifier: **window.sui**


== Guide ==

1. Fill the form with your configs.
2. Add shortcode to [sui_login_button] to any place on your site.
3. Logout button will replace login button after login.
