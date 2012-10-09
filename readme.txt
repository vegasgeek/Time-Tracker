Description:
The Time Tracker theme was built to track contractors' hours for projects. How it works is pretty simple, provide each contractor an author level user account on a WP install. They will be able add new entries for hours they've worked. Then, when it's time to send an invoice, as an admin you click the invoice link, select the client and date range and a list of all billable hours displayed and the hours are totaled for you.

Setup:
- Install a fresh copy of WordPress
- Install the Genesis theme and the Time Tracker theme
- Activate the Time Tracker theme
- Install & activate the following plugins:
	- Gravity Forms
	- Gravity Forms + Custom Post Types
	- Registered Users Only
- Use the WordPress Importer to import other-assets/import-these-pages.xml
- Use the Gravity Forms form imported to import other-assets/import-these-gravityforms.xml
- On the Settings->Reading tab, set the posts per page to 999 and set the front page to Static and choose "Add Hours"
- On the Settings->Permalinks page, select Post Name
- Create a menu and add "Add Hours" as the only link (the rest of the links are added automatically for admin users only)

That's all, folks.

Future development plans:
- More reporting
- Easier setup
- Further lockdown of admin pages