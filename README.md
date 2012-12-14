# I need backup

I need backup is a tool that takes backup of a database and organize the SQL 
files in a stack for future restore.

## How it works

When you call the command `need-backup.php`, it creates an SQL file with the
command `mysqldump` and store it in a stack database. If you call the command
multiple time, it will each time add a SQL file at the top of the stack.

When you call the command `need-restore.php`, it takes the backup at the top of
the stack and put it in the MySQL database using `mysqladmin` and `mysql`command
line tool. If the restore was successful, it removes the backup of the top of
the stack.

There is multiple stack, one per database. When you call the command :

    need-backup.php your_database_name

it will create a stack named *your\_database\_name*". This way, you can backup
multiple database on the same MySQL server.

## Dependencies

Make sure you have **MySQL client** installed on your system. You don't need the
full MySQL server to make it work. You also need PHP 5.2 working in command
line.

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

The stack database is a json file located in `~/.need.json`. If you have python 
installed on your system, you can visualize the file with this command :

    python -mjson.tool ~/.need.json

All the backup SQL file are saved in the directory `~/.need-storage` and named
by the name of the database and the timestamp of the backup. For example, if you
do a backup of the database name *my\_database* on the 2012-12-14 at 11:39, it
will create a file named *my_database_2012-12-14T11:39:00+01:00*.
