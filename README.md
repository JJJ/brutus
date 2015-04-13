# Brutus

Brutus is a WordPress plugin designed to protect the `wp-login.php` file of your installation against brute-force attacks.

It does this by:

1. Assigning each anonymous visitor to your site a unique ID in a cookie (which is good for 24 hours) and is used to generate something called a nonce
2. Nonces are used to confirm that requests to `wp-login.php` were initialized by a person who is physically browsing around your site vs. an automated script trying to brute-force its way through
3. Any request that fails the nonce check is bounced back to the `network_home_url()` of your site

## Buyer Beware

Brutus is still relatively new, might have bugs, or maybe won't even work at all. Please leave feedback in the form of issues here on Github or @JJJ on Twitter.

## Signing in

When a user signs in to your site, the anonymous cookie is removed

## Signing out

When a user signs out, a new anonymous cookie is immediately set

## Signing up

When a user attempts to register an account, all redirects should happen per usual

## Password Reset

When a user attempts to reset their password, all redirects should happen per usual

## Adverse Affects

There are a few potential gotchas

* If you've customized your login or registration process with a plugin or custom theme, your mileage may vary here. Please submit pull requests if you're able to help make Brutus more flexible.
* When a user signs out, they are redirected back to the root of the site rather than `wp-login.php`. This is because the nonce used by the previously signed-in user does not match the nonce of the now signed-out user, Brutus (correctly) detects a mismatch, and bounces the user away from `wp-login.php`. This could be improved, but I haven't spent enough time trying to unwind the redirection dance here.
* This has not been tested with BuddyPress or bbPress yet. Please submit pull requests if you're able to improve compatibility with these or any other plugins.
* Sites that use full-page caching and also link to `wp-login.php` in a sidebar or toolbar may not function correctly without additional configuration. This is because pages with these links will contain nonces that are not meant for the current anonymous user to see. This can likely be worked around with some JavaScript at a later date (pull requests welcome and encouraged.)

## Why was this built?

This plugin exists because I wanted an inexpensive way within WordPress to protect against brute-force attacks to `wp-login.php` without requiring a connection to WordPress.com via Jetpack and using what used to be known as BruteProtect, which is a really great service if you're able to run it.

## What is the release schedule?

Probably random, or as pull requests come in
