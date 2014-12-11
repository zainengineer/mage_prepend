mage_prepend
============

Use Auto prepend before magento index

```
sudo vi /etc/php5/fpm/conf.d/auto_prepend.ini 
auto_prepend_file = '/vagrant/zain_custom/auto_prepend_file.php'
sudo service php5-fpm restart
```
