mage_prepend
============

Use Auto prepend before magento index

```
git clone git@github.com:zainengineer/mage_prepend.git zain_custom

sudo vi /etc/php5/fpm/conf.d/auto_prepend.ini 
auto_prepend_file = '/vagrant/zain_custom/auto_prepend_file.php'
; auto_prepend_file = '/vagrant/public/zain_custom/auto_prepend_file.php'

sudo vi /etc/php5/cli/conf.d/auto_prepend.ini 
auto_prepend_file = '/vagrant/zain_custom/auto_prepend_file.php'
; auto_prepend_file = '/vagrant/public/zain_custom/auto_prepend_file.php'

sudo service php5-fpm restart
```
