# Hypermanager

Hypermanager is a server magnament tool, that allow you to manage all your Linux based servers.

- You can test this powerfull tool at hypermanager.net

# Requeriments
- Web Server
    - PHP 7
    - MariaDB (5.5.54)
    - Apache (2.4.7)
- Server to monitor
    - Lenguage: Spanish or English (As default)
    - Service: SSH service active

# How to install it

  - Clone this respository to your web server directory.
  - Create new Database and import this script [DB]
  - Replace this string "https://hypermanager.net/" with your "http://directory_url/" in some files (User rplace in files from notepad++ or another text editor).
  - Modify **lib/info/notify.php** with your <mail> and <telegram> info.

   [db]: <https://sok.sx/hypermanager.sql>
