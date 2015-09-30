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
                    var headerRow = admin_rows[0];
                    debugger;
                    var childElements = headerRow.childElements();
                    var td, text, idColumn, found;
                    found = false;
                    for (i=0; i<childElements.length; i++) {
                        var td = childElements[i];
                        if (td && (text = td.getInnerText().trim().toLowerCase())){
                            if (text == 'id'){
                                idColumn = i;
                                found = true;
                                break;
                            }
                        }
                    }
                    if (!found){
                        return ;
                    }
                    var i, tr, href, id, link;
                    for (i = 2; i < admin_rows.length; i++) {
                        tr = admin_rows[i];
                        href = tr.title;
                        td = tr.childElements()[idColumn];
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