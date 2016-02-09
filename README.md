# profis-testas

Mini comments website as test for applying job at profis.lt

# How to run it?

You can clone this repository and in your command line write `vagrant up` (for this command to work you need have [vagrant](https://www.vagrantup.com/downloads.html) and [virtualbox](https://www.virtualbox.org) installed on your local system). Wait few minutes until virtual box boots and than you can type in your browser `http://localhost:8080`.

If you want to use this code without vagrant you will need configure your webserver to point to `www` folder as web root, import `structure.sql` to your MySQL database, edit `www/config.php` file and run `composer update` from command line in `www/` folder. Also you need to make sure that all requests are redirected to `index.php` file (if you use apache, you can easily use supplied .htaccess file otherwise you need edit your server config files to get same functionality). 

# Login data

*Login:* admin@admin.org<br />
*Pass:* admin
