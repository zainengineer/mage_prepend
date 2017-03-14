mage_prepend
============

Use Auto prepend before magento index

```
git clone git@github.com:zainengineer/mage_prepend.git zain_custom

sudo vi /etc/php.d/auto_prepend.ini 
auto_prepend_file = '/vagrant/zain_custom/auto_prepend_file.php'
; auto_prepend_file = '/vagrant/public/zain_custom/auto_prepend_file.php'
sudo service apache restart

```

to detect actual location of php include path 
```
//in index.php add this to find include directories
echo php_ini_loaded_file() . "<br/>\n";
$aDir = array();
if ($filelist = php_ini_scanned_files()) {
    if (strlen($filelist) > 0) {
        $files = explode(',', $filelist);

        foreach ($files as $file) {
            $aDir[dirname(trim($file))] = dirname(trim($file));
        }
    }
}
echo "<pre>";
print_r($aDir);
die;

```

for multiple vhosts if you want separate repository clone for each project use the following:

```
sudo vi /etc/apache2/sites-enabled/site_config_file
php_value auto_prepend_file "/var/www/vhosts/your_website_root/zain_custom/auto_prepend_file.php"
sudo service php5-fpm restart
sudo service apache2 restart
```

for shared host you can use
* `vi .htaccess`
* `php_value  auto_prepend_file zain_custom/auto_prepend_file.php`

ignore changes in temp file and fix permissions 

    cd zain_custom
    git update-index --assume-unchanged include/local_modified/*.php
    chmod a+w include/local_modified -R

To temporarily replace magento Exception printing with custom exception printing

* `app/Mage.php:694`
* `ini_set('memory_limit','2G');\ZainPrePend\ShutDown\T::printException($e);exit();`
