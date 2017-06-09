<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_easysdi_coreInstallerScript {
    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
     * If preflight returns false, Joomla will abort the update and undo everything already done.
     */

    function preflight($type, $parent) {

        // Installing component manifest file version
        $this->release = $parent->get("manifest")->version;

        // Alter joomla 3.1.1 schema to reflect the MySql version
        $db = JFactory::getDbo();
        if ($db->name == 'sqlsrv') {
            if ($this->release == '4.1.0') {
                $sqls = array();
                $sqls[] = "ALTER TABLE [#__extensions] ADD  DEFAULT ('') FOR [custom_data];";
                $sqls[] = "ALTER TABLE [#__menu] ADD  DEFAULT ('') FOR [path];";
                $sqls[] = "ALTER TABLE [#__menu] ADD  DEFAULT ('') FOR [img];";
                $sqls[] = "ALTER TABLE [#__menu] ADD  DEFAULT ('') FOR [params];";

                foreach ($sqls as $sql) {
                    $query = $db->getQuery(true);
                    $db->setQuery($sql);
                    $db->execute();
                }
            }

            //Clean sqlserver default constraints on pricing management
            if ($type == 'update' && $this->release == '4.3.2' && version_compare($this->getParam('version'), $this->release) == -1) {
                //DROP default constraints from #__sdi_pricing_order
                $sqls = array();
                $sqls[] = "DECLARE @SchemaName NVARCHAR(256),
									@TableName NVARCHAR(256),
									@SQL NVARCHAR(MAX),
									@NewLine CHAR(1)
	 
							SELECT  @SchemaName = N'dbo',
									@TableName = N'" . $db->getPrefix() . "sdi_pricing_order',
									@NewLine = CHAR(10)

							SELECT  @SQL = ISNULL(@SQL + @NewLine, '') + 
										'ALTER TABLE [' + S.name + '].[' + T.name + '] ' +
											'DROP CONSTRAINT [' + D.name + ']'
							FROM    sys.tables T
								INNER JOIN sys.default_constraints D
									ON D.parent_object_id = T.object_id
								INNER JOIN sys.columns C
									ON C.object_id = T.object_id
										AND C.column_id = D.parent_column_id
								INNER JOIN sys.schemas S
									ON T.schema_id = S.schema_id
							WHERE   S.name = @SchemaName
							  AND   T.name = @TableName

							EXECUTE (@SQL)";

                //DROP default constraints from #_sdi_pricing_order_supplier
                $sqls[] = "DECLARE @SchemaName NVARCHAR(256),
									@TableName NVARCHAR(256),
									@SQL NVARCHAR(MAX),
									@NewLine CHAR(1)
	 
							SELECT  @SchemaName = N'dbo',
									@TableName = N'" . $db->getPrefix() . "sdi_pricing_order_supplier',
									@NewLine = CHAR(10)

							SELECT  @SQL = ISNULL(@SQL + @NewLine, '') + 
										'ALTER TABLE [' + S.name + '].[' + T.name + '] ' +
											'DROP CONSTRAINT [' + D.name + ']'
							FROM    sys.tables T
								INNER JOIN sys.default_constraints D
									ON D.parent_object_id = T.object_id
								INNER JOIN sys.columns C
									ON C.object_id = T.object_id
										AND C.column_id = D.parent_column_id
								INNER JOIN sys.schemas S
									ON T.schema_id = S.schema_id
							WHERE   S.name = @SchemaName
							  AND   T.name = @TableName

							EXECUTE (@SQL)";

                //DROP default constraints from #_sdi_pricing_order_supplier_product
                $sqls[] = "DECLARE @SchemaName NVARCHAR(256),
									@TableName NVARCHAR(256),
									@SQL NVARCHAR(MAX),
									@NewLine CHAR(1)
	 
							SELECT  @SchemaName = N'dbo',
									@TableName = N'" . $db->getPrefix() . "sdi_pricing_order_supplier_product',
									@NewLine = CHAR(10)

							SELECT  @SQL = ISNULL(@SQL + @NewLine, '') + 
										'ALTER TABLE [' + S.name + '].[' + T.name + '] ' +
											'DROP CONSTRAINT [' + D.name + ']'
							FROM    sys.tables T
								INNER JOIN sys.default_constraints D
									ON D.parent_object_id = T.object_id
								INNER JOIN sys.columns C
									ON C.object_id = T.object_id
										AND C.column_id = D.parent_column_id
								INNER JOIN sys.schemas S
									ON T.schema_id = S.schema_id
							WHERE   S.name = @SchemaName
							  AND   T.name = @TableName

							EXECUTE (@SQL)";

                //DROP default constraints from #_sdi_pricing_order_supplier_product_profile
                $sqls[] = "DECLARE @SchemaName NVARCHAR(256),
									@TableName NVARCHAR(256),
									@SQL NVARCHAR(MAX),
									@NewLine CHAR(1)
	 
							SELECT  @SchemaName = N'dbo',
									@TableName = N'" . $db->getPrefix() . "sdi_pricing_order_supplier_product_profile',
									@NewLine = CHAR(10)

							SELECT  @SQL = ISNULL(@SQL + @NewLine, '') + 
										'ALTER TABLE [' + S.name + '].[' + T.name + '] ' +
											'DROP CONSTRAINT [' + D.name + ']'
							FROM    sys.tables T
								INNER JOIN sys.default_constraints D
									ON D.parent_object_id = T.object_id
								INNER JOIN sys.columns C
									ON C.object_id = T.object_id
										AND C.column_id = D.parent_column_id
								INNER JOIN sys.schemas S
									ON T.schema_id = S.schema_id
							WHERE   S.name = @SchemaName
							  AND   T.name = @TableName

							EXECUTE (@SQL)";

                //DROP default constraints from #_sdi_order
                $sqls[] = "DECLARE @SchemaName NVARCHAR(256),
									@TableName NVARCHAR(256),
									@SQL NVARCHAR(MAX),
									@NewLine CHAR(1)
	 
							SELECT  @SchemaName = N'dbo',
									@TableName = N'" . $db->getPrefix() . "sdi_order',
									@NewLine = CHAR(10)

							SELECT  @SQL = ISNULL(@SQL + @NewLine, '') + 
										'ALTER TABLE [' + S.name + '].[' + T.name + '] ' +
											'DROP CONSTRAINT [' + D.name + ']'
							FROM    sys.tables T
								INNER JOIN sys.default_constraints D
									ON D.parent_object_id = T.object_id
								INNER JOIN sys.columns C
									ON C.object_id = T.object_id
										AND C.column_id = D.parent_column_id
								INNER JOIN sys.schemas S
									ON T.schema_id = S.schema_id
							WHERE   S.name = @SchemaName
							  AND   T.name = @TableName
							  AND D.name LIKE '%valid%'

							EXECUTE (@SQL)";
		
				foreach ($sqls as $sql) {
                    $db->getQuery(true);
                    $db->setQuery($sql);
                    $db->execute();
                }
			}
			if ($type == 'update'){
				try{
					
					$sql = "DECLARE @ObjectName NVARCHAR(100)
								SELECT @ObjectName = OBJECT_NAME([default_object_id]) FROM SYS.COLUMNS
								WHERE [object_id] = OBJECT_ID('[dbo].[" . $db->getPrefix() . "sdi_order]') AND [name] = 'validate';
								EXEC('ALTER TABLE [dbo].[" . $db->getPrefix() . "sdi_order] DROP CONSTRAINT ' + @ObjectName)";
			
					
						$db->getQuery(true);
						$db->setQuery($sql);
						$db->execute();
					
				}
				catch (Exception $ex){
				}
			}
        }

        // Create stored procedure drop_foreign_key 
        $db = JFactory::getDbo();
        if (($db->name == 'mysqli') || ($db->name == 'mysql')) {
            $sqls = array();
            $sqls[] = "DROP PROCEDURE IF EXISTS drop_foreign_key;";
            $sqls[] = "CREATE PROCEDURE drop_foreign_key(IN tableName VARCHAR(64), IN constraintName VARCHAR(64))
                BEGIN
                    IF EXISTS(
                        SELECT * FROM information_schema.table_constraints
                        WHERE 
                            table_schema    = DATABASE()     AND
                            table_name      = CONCAT('" . $db->getPrefix() . "',tableName) AND
                            constraint_name = CONCAT('" . $db->getPrefix() . "',constraintName) AND
                            constraint_type = 'FOREIGN KEY')
                    THEN
                        SET @query = CONCAT('ALTER TABLE ','" . $db->getPrefix() . "',tableName, ' DROP FOREIGN KEY ','" . $db->getPrefix() . "',constraintName, ';');
                        PREPARE stmt FROM @query; 
                        EXECUTE stmt; 
                        DEALLOCATE PREPARE stmt; 
                    END IF; 
                END";

            foreach ($sqls as $sql) {
                $query = $db->getQuery(true);
                $db->setQuery($sql);
                $db->execute();
            }

            $sqls = array();
            $sqls[] = "DROP PROCEDURE IF EXISTS drop_column;";
            $sqls[] = "CREATE PROCEDURE drop_column(IN tableName VARCHAR(64), IN columnName VARCHAR(64))
                BEGIN
                    IF EXISTS(
                        SELECT * FROM information_schema.COLUMNS
                        WHERE 
                            table_schema    = DATABASE()     AND
                            table_name      = CONCAT('" . $db->getPrefix() . "',tableName) AND
                            column_name = columnName)
                    THEN
                        SET @query = CONCAT('ALTER TABLE ','" . $db->getPrefix() . "',tableName, ' DROP COLUMN ',columnName, ';');
                        PREPARE stmt FROM @query; 
                        EXECUTE stmt; 
                        DEALLOCATE PREPARE stmt; 
                    END IF; 
                END";

            foreach ($sqls as $sql) {
                $query = $db->getQuery(true);
                $db->setQuery($sql);
                $db->execute();
            }
        }

        // Show the essential information at the install/update back-end
        //echo '<p>EasySDI component Core [com_easysdi_core]';
        //echo '<br />' . JText::_('COM_EASYSDI_CORE_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
    }

    /*
     * $parent is the class calling this method.
     * install runs after the database scripts are executed.
     * If the extension is new, the install method is run.
     * If install returns false, Joomla will abort the install and undo everything already done.
     */

    function install($parent) {
        // You can have the backend jump directly to the newly installed component configuration page
        // $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
    }

    /*
     * $parent is the class calling this method.
     * update runs after the database scripts are executed.
     * If the extension exists, then the update method is run.
     * If this returns false, Joomla will abort the update and undo everything already done.
     */

    function update($parent) {
        // You can have the backend jump directly to the newly updated component configuration page
        // $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
    }

    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * postflight is run after the extension is registered in the database.
     */

    function postflight($type, $parent) {
        if ($type == 'install') {
            require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
            $params['infrastructureID'] = Easysdi_coreHelper::uuid();
            $this->setParams($params);
        }

        if ($type == 'install') {
            // import joomla's filesystem classes
            jimport('joomla.filesystem.folder');

            // Create folders inside media folder
            if (!JFolder::create(JPATH_ROOT . '/media/easysdi', "0755")) {
                echo "Unable to create media EasySDI folder";
            }
            if (!JFolder::create(JPATH_ROOT . '/media/easysdi/catalog', "0755")) {
                echo "Unable to create media Catalog folder";
            }
            if (!JFolder::create(JPATH_ROOT . '/media/easysdi/catalog/xsl', "0755")) {
                echo "Unable to create media XSL folder";
            }
        }
        
        //remove files that no longer exist
        $this->deleteUnexistingFiles();
    }

    /*
     * $parent is the class calling this method
     * uninstall runs before any other action is taken (file removal or database processing).
     */

    function uninstall($parent) {
        
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */

    function getParam($name) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('manifest_cache');
        $query->from('#__extensions');
        $query->where('name = ' . $db->quote('com_easysdi_core'));
        $db->setQuery($query);
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    /*
     * sets parameter values in the component's row of the extension table
     */

    function setParams($param_array) {
        if (count($param_array) > 0) {
            // read the existing component value(s)
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('params');
            $query->from('#__extensions');
            $query->where('name = ' . $db->quote('com_easysdi_core'));

            $db->setQuery($query);
            $params = json_decode($db->loadResult(), true);
            // add the new variable(s) to the existing one(s)
            foreach ($param_array as $name => $value) {
                $params[(string) $name] = (string) $value;
            }
            // store the combined new and existing values back as a JSON string
            $paramsString = json_encode($params);
            $query = $db->getQuery(true);
            $query->update('#__extensions');
            $query->set('params = ' . $db->quote($paramsString));
            $query->where('name = ' . $db->quote('com_easysdi_core'));
            $db->setQuery($query);
            $db->query();
        }
    }

    /**
     * Delete files that should not exist
     *
     * @return  void
     */
    public function deleteUnexistingFiles() {

        $files = array(
            ////// easySDI < 4.4.1
            //remove postgres scripts
            '/administrator/components/com_easysdi_catalog/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_catalog/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_catalog/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_catalog/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_catalog/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_catalog/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_catalog/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_contact/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_contact/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_contact/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_contact/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_contact/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_contact/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_contact/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.1.0.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.0-beta-1.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.0-beta-2.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.0-beta-3.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.0-rc-1.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.0-rc-2.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.0-rc-3.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.2.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.3.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.6.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.7.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.2.8.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-beta-1.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-beta-2.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-beta-3.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-beta-4.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-beta-6.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-beta-7.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-rc-2.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-rc-7.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0-rc.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.0.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.1.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.3.2.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/4.4.0.sql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_dashboard/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_dashboard/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_dashboard/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_dashboard/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_dashboard/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_dashboard/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_dashboard/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_map/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_map/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_map/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_map/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_map/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_map/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_map/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_monitor/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_monitor/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_monitor/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_monitor/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_monitor/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_monitor/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_monitor/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_service/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_service/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_service/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_service/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_service/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_service/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_service/sql/updates/postgresql/index.html',
            '/administrator/components/com_easysdi_shop/sql/install/postgresql/alter.sql',
            '/administrator/components/com_easysdi_shop/sql/install/postgresql/insert.sql',
            '/administrator/components/com_easysdi_shop/sql/install/postgresql/install.sql',
            '/administrator/components/com_easysdi_shop/sql/install/postgresql/uninstall.sql',
            '/administrator/components/com_easysdi_shop/sql/install/postgresql/index.html',
            '/administrator/components/com_easysdi_shop/sql/updates/postgresql/4.0.0.sql',
            '/administrator/components/com_easysdi_shop/sql/updates/postgresql/index.html',
            //Dashboard: old indicator layouts
            '/administrator/components/com_easysdi_dashboard/indicators/shop_extractionstype.html.php',
            '/administrator/components/com_easysdi_dashboard/indicators/shop_global.html.php',
            '/administrator/components/com_easysdi_dashboard/indicators/shop_responsetimeproduct.html.php',
            '/administrator/components/com_easysdi_dashboard/indicators/shop_topdownloads.html.php',
            '/administrator/components/com_easysdi_dashboard/indicators/shop_topextractions.html.php',
            '/administrator/components/com_easysdi_dashboard/indicators/shop_topusers.html.php',
            //BE JS libs to FE
            '/administrator/components/com_easysdi_core/libraries/easysdi/catalog/addToBasket.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/catalog/bootbox.min.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/catalog/editMetadata.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/catalog/searchMetadata.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi.min.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/geoext/data/PrintProvider.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/geoext/ux/PrintPreview.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/geoext/widgets/PrintMapPanel.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/en.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/fr.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/BingSource.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/GoogleGeocoder.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/GoogleSource.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/LayerManager.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/LayerTree.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/LoadingIndicator.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/OLSource.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/OSMSource.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/Print.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/WMSSource.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/widgets/ScaleOverlay.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/widgets/Viewer.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/widgets/form/GoogleGeocoderComboBox.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/map/predefinedperimeter.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/OpenLayers/override-openlayers.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/index.html',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/plugins/index.html',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/plugins/LayerDetailSheet.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/plugins/LayerDownload.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/plugins/LayerOrder.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/plugins/SearchCatalog.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/widgets/IndoorLevelslider.css',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/widgets/IndoorLevelSlider.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js/sdi/widgets/IndoorLevelSliderTip.js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/view/index.html',
            '/administrator/components/com_easysdi_core/libraries/easysdi/view/view.js',
            '/administrator/components/com_easysdi_core/assets/css/easysdi_loader.css',
            //https://forge.easysdi.org/issues/1344 Remove jQuery-File-Upload servers
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-go/app.yaml',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-go/app/main.go',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-go/static/favicon.ico',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-go/static/robots.txt',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-python/app.yaml',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-python/main.py',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-python/static/favicon.ico',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/gae-python/static/robots.txt',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/node/package.json',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/node/server.js',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/php/index.php',
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server/php/UploadHandler.php',
            ////// easySDI < 4.4.4
            //https://forge.easysdi.org/issues/1380
            '/administrator/components/com_easysdi_map/models/fields/createdby.php'
            
        );

        $folders = array(
            //// easySDI < 4.4.1
            //remove postgres scripts
            '/administrator/components/com_easysdi_catalog/sql/install/postgresql',
            '/administrator/components/com_easysdi_catalog/sql/updates/postgresql',
            '/administrator/components/com_easysdi_contact/sql/install/postgresql',
            '/administrator/components/com_easysdi_contact/sql/updates/postgresql',
            '/administrator/components/com_easysdi_core/sql/install/postgresql',
            '/administrator/components/com_easysdi_core/sql/updates/postgresql',
            '/administrator/components/com_easysdi_dashboard/sql/install/postgresql',
            '/administrator/components/com_easysdi_dashboard/sql/updates/postgresql',
            '/administrator/components/com_easysdi_map/sql/install/postgresql',
            '/administrator/components/com_easysdi_map/sql/updates/postgresql',
            '/administrator/components/com_easysdi_monitor/sql/install/postgresql',
            '/administrator/components/com_easysdi_monitor/sql/updates/postgresql',
            '/administrator/components/com_easysdi_service/sql/install/postgresql',
            '/administrator/components/com_easysdi_service/sql/updates/postgresql',
            '/administrator/components/com_easysdi_shop/sql/install/postgresql',
            '/administrator/components/com_easysdi_shop/sql/updates/postgresql',
            //BE JS libs to FE
            '/administrator/components/com_easysdi_core/libraries/DataTables-1.9.4',
            '/administrator/components/com_easysdi_core/libraries/ext',
            '/administrator/components/com_easysdi_core/libraries/filesaver',
            '/administrator/components/com_easysdi_core/libraries/gemetclient-2.0.0',
            '/administrator/components/com_easysdi_core/libraries/geoext',
            '/administrator/components/com_easysdi_core/libraries/gxp',
            '/administrator/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3',
            '/administrator/components/com_easysdi_core/libraries/leaflet',
            '/administrator/components/com_easysdi_core/libraries/openlayers',
            '/administrator/components/com_easysdi_core/libraries/OpenLayers-2.13.1',
            '/administrator/components/com_easysdi_core/libraries/proj4js-1.1.0',
            '/administrator/components/com_easysdi_core/libraries/proj4js-1.4.1',
            '/administrator/components/com_easysdi_core/libraries/proxy',
            '/administrator/components/com_easysdi_core/libraries/syntaxhighlighter',
            '/administrator/components/com_easysdi_core/libraries/tablednd',
            '/administrator/components/com_easysdi_core/libraries/ux',
            '/administrator/components/com_easysdi_core/libraries/easysdi/js',
            '/administrator/components/com_easysdi_core/libraries/easysdi/view',
            '/administrator/components/com_easysdi_processing/assets/js',
            '/administrator/components/com_easysdi_monitor/libraries/ext',
            '/administrator/components/com_easysdi_catalog/assets/images',
            '/administrator/components/com_easysdi_catalog/assets/css',
            //https://forge.easysdi.org/issues/1344 Remove jQuery-File-Upload servers
            '/components/com_easysdi_core/libraries/jQuery-File-Upload-9.9.3/server'
        );

        jimport('joomla.filesystem.file');

        foreach ($files as $file) {
            if (JFile::exists(JPATH_ROOT . $file) && !JFile::delete(JPATH_ROOT . $file)) {
                echo JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file) . '<br />';
            }
        }

        jimport('joomla.filesystem.folder');

        foreach ($folders as $folder) {
            if (JFolder::exists(JPATH_ROOT . $folder) && !JFolder::delete(JPATH_ROOT . $folder)) {
                echo JText::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $folder) . '<br />';
            }
        }
    }

}
