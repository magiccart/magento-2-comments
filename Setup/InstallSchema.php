<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-07-10 10:40:51
 * @@Modify Date: 2018-07-19 18:50:17
 * @@Function:
 */

namespace Magiccart\Comments\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('magiccart_comments'))
            ->addColumn(
                'comment_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Comment ID'
            )
            ->addColumn('parent_id', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Parent Id')
            ->addColumn('product_id', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Product Id')
            ->addColumn('customer_id', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Customer Id')
            ->addColumn('comment_ip', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Comment IP')
            ->addColumn('nickname', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], 'Nick Name')
            ->addColumn('content', Table::TYPE_TEXT, '1M', [], 'comment')
            ->addColumn('email', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
            ->addColumn('is_mod', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Moderator')
            ->addColumn('author_product', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Author product')
            ->addColumn('is_private', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Private comment')
            ->addColumn('notify', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Notify reply')
            ->addColumn('replied', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Reply')
            ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Status')
            ->addColumn('store', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => '0'])
            ->addColumn('created_time', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created Time')
            ->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Update Time')
            ->addIndex($installer->getIdxName('comment_id', ['comment_id']), ['comment_id'])
            ->setComment('Magiccart Comments');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
