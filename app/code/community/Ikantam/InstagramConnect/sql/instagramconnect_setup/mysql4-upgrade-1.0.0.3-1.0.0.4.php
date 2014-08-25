 
<?php
// $installer->getConnection()
//     ->addColumn($installer->getTable('catalog/product'),
//     'instagram_approved',
//     array(
//         'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
//         'length'    => '64K',
//         'nullable' => true,
//         'default' => null,
//         'comment' => 'Instagram Approved'
//     )
// );
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('instagramconnect/instagramimage'),
    'product_instagram',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Product Id'
    )
);
$installer->endSetup();
