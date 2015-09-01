<?php
namespace ZainPrePend\AdminProductLink;
use ZainPrePend\lib;

class T {
    public static function addProductLink()
    {
        if ($_POST){
            return ;
        }
        if ($_GET){
            return ;
        }
        if (!isset($_SERVER['REQUEST_URI'])) {
            return;
        }
        $vRequestPath = $_SERVER['REQUEST_URI'];
        if (strpos($vRequestPath, 'catalog_product/index') !== 0) {
            $vRequestPath = trim($vRequestPath,'/');
            if (strpos($vRequestPath,'edit')){
                return ;
            }
            if (strpos($vRequestPath,'/id/')){
                return ;
            }
            $iParts =  count(explode('/',$vRequestPath));
            if ($iParts != 6 ){
                return ;
            }

            ?>
            <script>
                function zainAddAdminRows()
                {
                    var admin_rows = $$('#productGrid_table tr');
                    // more than header rows
                    if (admin_rows.length < 3){
                        return false;
                    }
                    var i, tr, href, td, id, link;
                    for (i = 2; i < admin_rows.length; i++) {
                        tr = admin_rows[i];
                        href = tr.title;
                        td = tr.childElements()[1];
                        id = parseInt(td.getInnerText());
                        link = "<a href='_href'>_id</a> <a href='javascript:void(0)' onclick='window.prompt(_id,_id)'>copy</a>";
                        link =  link.replace(/_href/g,href);
                        link = link.replace(/_id/g,id);
                        td.innerHTML = link;
                    }
                }
                zainAddAdminRows();

                Ajax.Responders.register({
                    onComplete: function() {
                        zainAddAdminRows();
                    }
                });

            </script>
            <?php
        }
    }
}