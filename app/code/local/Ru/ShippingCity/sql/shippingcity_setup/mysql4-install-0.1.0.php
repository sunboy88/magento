<?php
$regionsFile = dirname(__FILE__) . '/regions.csv';

if (file_exists($regionsFile)) {
    $csv = new Varien_File_Csv();
    $regions = $csv->getData($regionsFile);

    $countryCode = 'VN';
    $locale = 'vn_VN';
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = $this;
    $installer->startSetup();
    /*
     * Clean up VN region records
     */
    $query = "DELETE FROM {$installer->getTable('directory/country_region')} WHERE country_id = 'VN';
    DELETE FROM {$installer->getTable('directory/country_region_name')} WHERE locale = 'ru_VN';";
    $installer->run($query);
    /*
     * Insert VN Regions
     */
    for ($i = 0; $i < count($regions); $i++) {
        if (count($regions[$i]) < 2) {
            continue;
        }
        $regionCode = $regions[$i][0];
        $regionName = $regions[$i][1];
        $bind = array(
            'region_id' => NULL,
            'country_id' => $countryCode,
            'code' => $regionCode,
            'default_name' => $regionName
        );
        $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
        $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));
        $bind2 = array(
            'locale' => $locale,
            'region_id' => $regionId,
            'name' => $regionName
        );
        $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind2);
    }

    //$websiteId = Mage::getModel('core/website')->load('ndg_ru')->getId();
    //if (!is_null($websiteId)) {
        $cityFile = dirname(__FILE__) . '/cities.csv';

        if (file_exists($cityFile)) {

            /*
             * Create table directory_country_region_city
             */
            $installer->run("
                DROP TABLE IF EXISTS `{$installer->getTable('directory_country_region_city')}`;
                ");
            $table = $installer->getConnection()
                ->newTable($installer->getTable('directory_country_region_city'))
                ->addColumn('city_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ), 'City Id')
                ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'nullable' => false,
                    'default' => 0,
                ), 'Region Id')
                ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'City Name')
                ->addIndex($installer->getIdxName('directory/country_region', array('region_id')), array('region_id'))
                ->addForeignKey(
                    $installer->getFkName('directory_country_region_city', 'region_id', 'directory/country_region', 'region_id'), 'region_id', $installer->getTable('directory/country_region'), 'region_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->setComment('Directory Country Region City');
            $installer->getConnection()->createTable($table);
            /*
             * Insert VN Cities according to VN Regions
             */
            $csv = new Varien_File_Csv();
            $cities = $csv->getData($cityFile);
            for ($i = 0; $i < count($cities); $i++) {
                if (count($cities[$i]) < 2) {
                    continue;
                }

                $regionName = $cities[$i][0];
                $cityName = $cities[$i][1];
                $query = "SELECT `region_id` FROM `{$installer->getTable('directory/country_region_name')}` WHERE `name`='{$regionName}'";
                $regionId = $installer->getConnection()->fetchOne($query);
                $bind = array(
                    'city_id' => null,
                    'region_id' => $regionId,
                    'name' => $cityName
                );
                $installer->getConnection()->insert($installer->getTable('directory_country_region_city'), $bind);
            }
        }
   // }
    $installer->endSetup();
}
