=== Simple User Password Generator ===
Contributors: jakemgold, 10up
Donate link: http://10up.com/plugins/simple-user-password-generator-wordpress/
Tags: users, password, security
Requires at least: 3.8
Tested up to: 4.0
Stable tag: 3.0.1
License: GPLv2

Generate a secure password when adding or updating new users.

== Description ==

Users with the ability to manage users (administrators) have a new button on the add and edit user screens to generate a secure password.

Also adds an option to encourage the user to change their password, when logged in to the dashboard, as well as an option to send
existing users the new, generated password.

No new settings pages or configuration screens, nothing added to the database. Just install and go!

Localization ready; includes Spanish translation. Other translations welcome!

== Installation ==

1. Install either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Get to work adding and editing users with automatically generated, secure passwords!

== Screenshots ==

1. New "Generate Password" button and "Encourage user to change their password" features seen on the add new users screen.

== Changelog ==

= 3.0.1 =
* Bug fix (forgot to remove unused hook)

= 3.0 =
* Refactored for performance / maintainability
* Updated layout of controls for WordPress 3.8+

= 2.0.2 =
* Refactored / improved JavaScript (faster)
* Fixed improper PHP class variable name that collided with Simple Local Avatars plug-in

= 2.0.1 =
* French translation added

= 2.0 =
* Adds password generation and email notification support when editing existing users.
* Removes unnecessary code for resetting password on the actual profile editing screen.