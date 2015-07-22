<?php
/**
 * Amazon Login
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

$installer = $this;

$installer->startSetup();

$amazon_table = $installer->getConnection()
    ->newTable($installer->getTable('amazon_login/login'))
    ->addColumn('login_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
    ), 'Login ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
        'nullable' => false,
        'unsigned' => true
    ), 'Customer Entity ID')
    ->addColumn('amazon_uid', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array (
        'nullable' => true,
        'unsigned' => true
    ), 'Amazon User ID')
    ->addIndex($installer->getIdxName('amazon_login/login', array('customer_id')), array('customer_id'))
    ->addIndex($installer->getIdxName('amazon_login/login', array('amazon_uid')), array('amazon_uid'));

$installer->getConnection()->createTable($amazon_table);

$installer->getConnection()->addConstraint(
    'fk_amazon_login_customer_entity_id',
    $installer->getTable('amazon_login/login'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    'cascade',
    'restrict'
);

$installer->endSetup();