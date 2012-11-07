# I need backup

I need backup is a tool that takes backup of a database and organize the SQL files in a stack for future restore.

## Current state

It's currently in heavy development. So if you are able to make it work, do it with precaution because it may crash easily.

## How to install

You can install it using PEAR :

    pear channel-discover pear.ada-consult.com
    pear install ada/INeedBackup

## Configuration

 1. Create the file ~/.need.ini and put this configuration :

        backup.command=mysqldump -u root {{database_name}}
        restore.command=mysqladmin -u root --force drop {{database_name}} \&\& mysqladmin -u root create {{database_name}} \&\& mysql -u root {{database_name}}

    Customize the backup and restore command to work in your setup (username, 
    password, hostname). For example, if your mysql information are

     * ***Username***: root
     * ***Password***: my-password
     * ***Hostname***: mysql-hostname.org

    The configuration would be :

        backup.command=mysqldump -u root -pmy-password -h mysql-hostname.org {{database_name}}
        restore.command=mysqladmin -u root -pmy-password -h mysql-hostname.org --force drop {{database_name}} \&\& mysqladmin -u root -pmy-password -h mysql-hostname.org create {{database_name}} \&\& mysql -u root -pmy-password -h mysql-hostname.org {{database_name}}

     Ensure that the user you configure in the command line have full rights 
     on the database (needed for restauration).

 1. Create the directory ~/.need-storage
 
## Usage

### Create a backup of a database

Creates a backup of `database_name` and put's it at the top of the stack of backup's. There is a stack for each database name.

    need-backup.php database_name
    
### Restore a backup at the top of the stack

Takes the backup at the top of the `database_name` stack and restore it in MySQL.

    need-restore.php database_name
    
## Internal

The stack database is a json file located in `~/.need.json`. If you have python installed on your system, you can visualize the file with this command :

    python -mjson.tool ~/.need.json