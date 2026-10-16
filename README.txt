SAVANNA GROUP CAREERS - PHP + SQLite

FOLDERS
  website/  public site (Home, Jobs, Job details, Who We Are, Careers FAQ, My Profile, Sign in, Sign up)
  admin/    admin panel. It is a separate folder and the website has no link to it.
  shared/   database code used by both
  data/     the SQLite database file is created here on first run

RUN IT WITH XAMPP (Windows)
  1. Copy the whole "savanna" folder into C:\xampp\htdocs\
  2. Start Apache in the XAMPP control panel. No MySQL needed.
  3. Website:     http://localhost/savanna/website/
     Admin panel: http://localhost/savanna/admin/
  If you see "could not find driver", open php.ini, remove the ; before extension=pdo_sqlite and extension=sqlite3, then restart Apache.

RUN IT WITHOUT XAMPP (PHP 8+ installed)
  cd savanna
  php -S localhost:8000
  Website: http://localhost:8000/website/    Admin: http://localhost:8000/admin/

ADMIN LOGIN
  Username: Tycoon
  Password: Tycoon_$#
  Change it after your first login under Admin > Account.
  The login is only created when the database is first created. If you change it, then delete data/savanna.sqlite, it resets to the above.

WHAT THE ADMIN CAN DO
  Add, edit and delete jobs; edit and remove users; see applications;
  edit the home headline, Who We Are, FAQ and privacy consent text; change the admin username and password.

BEFORE GOING LIVE ON THE INTERNET
  - Use HTTPS.
  - Block web access to the data/ folder (on Apache add "Require all denied" in data/.htaccess - included).
  - Change the admin password.
  - The robot check is a simple sum. For real traffic, add Google reCAPTCHA.
