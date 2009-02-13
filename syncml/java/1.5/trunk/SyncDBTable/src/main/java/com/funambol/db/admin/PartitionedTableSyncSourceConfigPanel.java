/*
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the Honest Public License, as published by
 * Funambol, either version 1 or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY, TITLE, NONINFRINGEMENT or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the Honest Public License for more details.
 *
 * You should have received a copy of the Honest Public License
 * along with this program; if not, write to Funambol,
 * 643 Bair Island Road, Suite 305 - Redwood City, CA 94063, USA
 */

package com.funambol.db.admin;

import java.awt.BorderLayout;
import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;

import java.io.Serializable;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;

import javax.swing.*;
import javax.swing.border.TitledBorder;
import javax.swing.event.ListSelectionEvent;
import javax.swing.event.ListSelectionListener;

import org.apache.commons.lang.StringUtils;

import com.funambol.admin.AdminException;

import com.funambol.framework.engine.source.ContentType;
import com.funambol.framework.engine.source.SyncSourceInfo;

import com.funambol.db.admin.support.DataFromDBPanel;
import com.funambol.db.admin.support.FieldMappingTableModel;
import com.funambol.db.admin.support.TableRenderer;
import com.funambol.db.admin.support.Tools;
import com.funambol.db.engine.source.PartitionedTableSyncSource;



/**
 * Configuration panel for PartitionedTableSyncSource
 *
 * @version $Id: PartitionedTableSyncSourceConfigPanel.java,v 1.6 2007/06/18 14:31:44 luigiafassina Exp $
 */
public class PartitionedTableSyncSourceConfigPanel extends BaseConfigPanel implements
    Serializable {
    // ----------------------------------------------------------------- Constants

    public static final String NAME_ALLOWED_CHARS =
        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-_.";

    private static final String DEFAULT_JNDI_NAME = "jdbc/fnblds";

    // -------------------------------------------------------------- Private data
    /** label for the panel's name */
    private JLabel panelName = new JLabel();

    /** border to evidence the title of the panel */
    private TitledBorder titledBorder1;

    private JLabel sourceUriLabel                   = new JLabel();

    private JTextField sourceUriValue               = new JTextField();

    private JLabel nameLabel                        = new JLabel();

    private JTextField nameValue                    = new JTextField();

    private JLabel typeLabel                        = new JLabel();

    private JTextField typeValue                    = new JTextField();

    private JLabel jndiNameLabel                    = new JLabel();

    private JTextField jndiNameValue                = new JTextField();

    private JButton confirmButton                   = new JButton();

    private PanelPartitioningTable panelPartitioningTable = new PanelPartitioningTable();

    private PanelDataTable panelDataTable                 = new PanelDataTable();

    private DataFromDBPanel dataFromDB              = null;


    // ------------------------------------------------------------ Constructors

    /**
     * Creates a new configuration panel
     */
    public PartitionedTableSyncSourceConfigPanel() {
        init();
    }

    // --------------------------------------------------------- Private methods

    /**
     * Init the panel
     */
    private void init() {


        // set layout
        this.setLayout(null);

        this.setPreferredSize(new Dimension(590, 900));

        titledBorder1 = new TitledBorder("");

        panelName.setFont(titlePanelFont);
        panelName.setText("Edit Partitioned Table SyncSource");
        panelName.setBounds(14, 5, 316, 28);
        panelName.setAlignmentX(SwingConstants.CENTER);
        panelName.setBorder(titledBorder1);

        int x1 = 14;
        int x2 = 170;
        int y  = 50;
        int dy = 25;

        sourceUriLabel.setText("Source URI: ");
        sourceUriLabel.setFont(defaultFont);
        sourceUriLabel.setBounds(x1, y, 150, 18);
        sourceUriValue.setFont(defaultFont);
        sourceUriValue.setBounds(x2, y, 350, 18);

        y += dy;

        nameLabel.setText("Name: ");
        nameLabel.setFont(defaultFont);
        nameLabel.setBounds(x1, y, 150, 18);
        nameValue.setFont(defaultFont);
        nameValue.setBounds(x2, y, 350, 18);

        y += dy;

        typeLabel.setText("Type: ");
        typeLabel.setFont(defaultFont);
        typeLabel.setBounds(x1, y, 150, 18);
        typeValue.setFont(defaultFont);
        typeValue.setBounds(x2, y, 350, 18);

        y += dy;

        jndiNameLabel.setText("JNDI Name datasource: ");
        jndiNameLabel.setFont(defaultFont);
        jndiNameLabel.setBounds(x1, y, 150, 18);
        jndiNameValue.setFont(defaultFont);
        jndiNameValue.setBounds(x2, y, 350, 18);
        jndiNameValue.setText(DEFAULT_JNDI_NAME);

        y += dy;

        panelPartitioningTable.setBounds(5, y, 535, 190);

        y += 200;

        panelDataTable.setBounds(5, y, 640, 420);

        y += 430;

        confirmButton.setFont(defaultFont);
        confirmButton.setText("Add");
        confirmButton.setBounds(275, y, 70, 25);

        confirmButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                try {
                    validateValues();
                    getValues();
                    if (getState() == STATE_INSERT) {
                        PartitionedTableSyncSourceConfigPanel.this.actionPerformed(new
                                ActionEvent(PartitionedTableSyncSourceConfigPanel.this,
                                            ACTION_EVENT_INSERT,
                                            event.getActionCommand()));
                    } else {
                        PartitionedTableSyncSourceConfigPanel.this.actionPerformed(new
                                ActionEvent(PartitionedTableSyncSourceConfigPanel.this,
                                            ACTION_EVENT_UPDATE,
                                            event.getActionCommand()));
                    }
                } catch (Exception e) {
                    notifyError(new AdminException(e.getMessage()));
                }
            }
        });

        this.add(panelName, null);

        this.add(sourceUriLabel, null);
        this.add(sourceUriValue, null);

        this.add(nameLabel, null);
        this.add(nameValue, null);

        this.add(typeLabel, null);
        this.add(typeValue, null);        

        this.add(jndiNameLabel, null);
        this.add(jndiNameValue, null);

        this.add(panelPartitioningTable, null);

        this.add(panelDataTable, null);

        this.add(confirmButton, null);

    }

    /**
     * Shows panel to load data from db.
     * @return DataFromDBPanel
     */
    public DataFromDBPanel showDataFromDB() {
        ( (DataFromDBPanel)dataFromDB).showDataFromDB();
        return ( (DataFromDBPanel)dataFromDB);
    }


    // ----------------------------------------------------------- Private methods

    /**
     * Set syncSource properties with the values provided by the user.
     */
    private void getValues() {

        PartitionedTableSyncSource syncSource = (PartitionedTableSyncSource)getSyncSource();

        syncSource.setSourceURI(sourceUriValue.getText().trim());
        syncSource.setName(nameValue.getText().trim());
        syncSource.setType(typeValue.getText().trim());

        syncSource.setJndiName(jndiNameValue.getText().trim());

        ContentType[] contentTypes = new ContentType[1];

        contentTypes[0] = new ContentType();
        contentTypes[0].setType("text/plain");
        contentTypes[0].setVersion("1.0");

        SyncSourceInfo sourceInfo = new SyncSourceInfo(contentTypes, 0);
        sourceInfo.setPreferred(0);

        syncSource.setInfo(sourceInfo);

        panelPartitioningTable.getValues();
        panelDataTable.getValues();
    }

    /**
     * Update the form with syncsource properties
     */
    public void updateForm() {
        if (!(getSyncSource() instanceof PartitionedTableSyncSource)) {
             notifyError(
                     new AdminException(
                             "This is not a PartitionedTableSyncSource! Unable to process SyncSource values."
                     )
             );
             return;
         }

         PartitionedTableSyncSource syncSource = (PartitionedTableSyncSource)getSyncSource();

         if (getState() == STATE_INSERT) {
             confirmButton.setText("Add");
         } else if (getState() == STATE_UPDATE) {
             confirmButton.setText("Save");
        }

        sourceUriValue.setText(syncSource.getSourceURI());
        nameValue.setText(syncSource.getName());
        //typeValue.setText(syncSource.getType());
        
        SyncSourceInfo info = syncSource.getInfo();        
        if (info != null) {
            ContentType[] types = info.getSupportedTypes();

            StringBuffer typesList = new StringBuffer(), versionsList = new StringBuffer();
            for (int i=0; ((types != null) && (i<types.length)); ++i) {
                typesList.append(types[i].getType());
                versionsList.append(types[i].getVersion());
                //
                // Also if the SyncSourceInfo contains more contentTypes, we handle
                // just the first one
                //
                break;
            }

            typeValue.setText(typesList.toString());
            //if we handle version...
            //versionValue.setText(versionsList.toString());
        }
        
        String jndiName = syncSource.getJndiName();
        if (jndiName == null || jndiName.equals("")) {
            jndiName = DEFAULT_JNDI_NAME;
        }
        jndiNameValue.setText(jndiName);

        if (syncSource.getSourceURI() != null) {
            sourceUriValue.setEditable(false);
        }

        Map mapping = syncSource.getFieldMapping();
        if (mapping == null) {
            mapping = new TreeMap();
            syncSource.setFieldMapping(mapping);
        }

        panelPartitioningTable.loadSyncSource(syncSource);
        panelDataTable.loadSyncSource(syncSource);

    }


    /**
     * Checks if the values provided by the user are all valid. In caso of errors,
     * a IllegalArgumentException is thrown.
     */
    private void validateValues() throws IllegalArgumentException {
        String value = null;

        value = sourceUriValue.getText();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Source URI' cannot be empty. Please provide a SyncSource URI.");
        }

        value = nameValue.getText();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Name' cannot be empty. Please provide a SyncSource name.");
        }

        if (!StringUtils.containsOnly(value, NAME_ALLOWED_CHARS.toCharArray())) {
            throw new IllegalArgumentException(
                "Only the following characters are allowed for field 'Name': \n" +
                NAME_ALLOWED_CHARS);
        }

        value = typeValue.getText();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Type' cannot be empty. Please provide a SyncSource type.");
        }

        value = jndiNameValue.getText();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'JNDI Name datasource' cannot be empty. Please provide a JNDI name.");
        }

        panelPartitioningTable.validateValues();
        panelDataTable.validateValues();
    }


    /**
     * Represent the panel to configure the partitioning table properties
     */
    public class PanelPartitioningTable extends JPanel {

        private JLabel tableNameLabel                        = new JLabel();

        private JTextField tableNameValue                    = new JTextField();

        private JLabel linkFieldLabel                        = new JLabel();

        private JComboBox linkFieldValue                     = new JComboBox();

        private JLabel updateDateFieldLabel                  = new JLabel();

        private JComboBox updateDateFieldValue               = new JComboBox();

        private JLabel updateTypeFieldLabel                  = new JLabel();

        private JComboBox updateTypeFieldValue               = new JComboBox();

        private JLabel principalFieldLabel                   = new JLabel();

        private JComboBox principalFieldValue                = new JComboBox();

        private JButton loadDataFromDBButton                 = new JButton();

        private JLabel loadDataFromDBLabel                   = new JLabel();


        /**
         * Creates a new PanelPartitioningTable
         */
        public PanelPartitioningTable() {
            init();
        }

        /**
         * Init the panel
         */
        private void init() {
            setLayout(null);

            setBorder(new TitledBorder("Partitioning Table"));

            int x1 = 9;
            int x2 = 165;
            int y  = 25;
            int dy = 25;


            loadDataFromDBButton.setText("Load data from db");
            loadDataFromDBButton.setFont(defaultFont);
            loadDataFromDBButton.setBounds(x1, y, 150, 18);
            loadDataFromDBLabel.setText("Press this button to select the desired table and load its fields");
            loadDataFromDBLabel.setFont(defaultFont);
            loadDataFromDBLabel.setBounds(x2, y, 350, 18);

            loadDataFromDBButton.addActionListener(new ActionListener() {
                public void actionPerformed(ActionEvent event) {
                    loadDataFromDB();
                }
            });

            y += dy;

            tableNameLabel.setText("Table name: ");
            tableNameLabel.setFont(defaultFont);
            tableNameLabel.setBounds(x1, y, 150, 18);
            tableNameValue.setFont(defaultFont);
            tableNameValue.setBounds(x2, y, 350, 18);

            y += dy;

            principalFieldLabel.setText("Principal field: ");
            principalFieldLabel.setFont(defaultFont);
            principalFieldLabel.setBounds(x1, y, 150, 18);
            principalFieldValue.setFont(defaultFont);
            principalFieldValue.setBounds(x2, y, 350, 18);
            principalFieldValue.setEditable(true);

            y += dy;

            linkFieldLabel.setText("Link field: ");
            linkFieldLabel.setFont(defaultFont);
            linkFieldLabel.setBounds(x1, y, 150, 18);
            linkFieldValue.setFont(defaultFont);
            linkFieldValue.setBounds(x2, y, 350, 18);
            linkFieldValue.setEditable(true);

            y += dy;

            updateDateFieldLabel.setText("Last update timestamp field: ");
            updateDateFieldLabel.setFont(defaultFont);
            updateDateFieldLabel.setBounds(x1, y, 150, 18);
            updateDateFieldValue.setFont(defaultFont);
            updateDateFieldValue.setBounds(x2, y, 350, 18);
            updateDateFieldValue.setEditable(true);

            y += dy;

            updateTypeFieldLabel.setText("Last update type field: ");
            updateTypeFieldLabel.setFont(defaultFont);
            updateTypeFieldLabel.setBounds(x1, y, 150, 18);
            updateTypeFieldValue.setFont(defaultFont);
            updateTypeFieldValue.setBounds(x2, y, 350, 18);
            updateTypeFieldValue.setEditable(true);

            //add all the components to the panel
            this.add(loadDataFromDBButton, null);
            this.add(loadDataFromDBLabel, null);

            this.add(tableNameLabel, null);
            this.add(tableNameValue, null);

            this.add(principalFieldLabel, null);
            this.add(principalFieldValue, null);

            this.add(linkFieldLabel, null);
            this.add(linkFieldValue, null);
            
            this.add(updateDateFieldLabel, null);
            this.add(updateDateFieldValue, null);

            this.add(updateTypeFieldLabel, null);
            this.add(updateTypeFieldValue, null);
        }

        /**
         * Show the panel to load data from db
         */
        private void loadDataFromDB() {
            if (dataFromDB == null) {
                dataFromDB = new DataFromDBPanel(getOwner(),
                                                 defaultFont,
                                                 defaultTableHeaderFont);
            }
            ( (DataFromDBPanel)dataFromDB).showDataFromDB();

            String tableName = dataFromDB.getTableSelected();
            tableNameValue.setText(tableName);

            if (tableName != null) {
                List columns = dataFromDB.getColumnsName();
                Tools.replaceAllExceptFirst(principalFieldValue, columns);
                Tools.replaceAllExceptFirst(linkFieldValue, columns);
                Tools.replaceAllExceptFirst(updateDateFieldValue, columns);
                Tools.replaceAllExceptFirst(updateTypeFieldValue, columns);
            }

        }

        /**
         * Checks if the values provided by the user are all valid. In case of errors,
         * a IllegalArgumentException is thrown.
         */
        private void validateValues() throws IllegalArgumentException {

            String value = null;

            value = tableNameValue.getText();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Table name' for partitioning table cannot be empty. Please provide a table name.");
            }

            value = (String)principalFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Principal field' for partitioning table cannot be empty. Please provide a principal field.");
            }

            value = (String)linkFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Link field' for partitioning table cannot be empty. Please provide a link field.");
            }

            value = (String)updateDateFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Last update timestamp field' for partitioning table cannot be empty. Please provide a last update timestamp field.");
            }

            value = (String)updateTypeFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Last update type field' for partitioning table cannot be empty. Please provide a last update type field.");
            }
        }

        /**
         * Set syncSource properties with the values provided by the user.
         */
        private void getValues() {

            PartitionedTableSyncSource syncSource = (PartitionedTableSyncSource)getSyncSource();

            syncSource.setPartitioningTableName(
                tableNameValue.getText().trim()
            );

            syncSource.setPrincipalFieldPartitioningTable(
                ((String) principalFieldValue.getSelectedItem()).trim()
            );

            syncSource.setLinkFieldPartitioningTable(
                ((String) linkFieldValue.getSelectedItem()).trim()
            );

            syncSource.setUpdateDateFieldPartitioningTable(
                ((String) updateDateFieldValue.getSelectedItem()).trim()
            );

            syncSource.setUpdateTypeFieldPartitioningTable(
                ((String) updateTypeFieldValue.getSelectedItem()).trim()
            );
        }

        /**
         * Update the from with the properties of the given syncsource
         * @param source PartitionedTableSyncSource
         */
        public void loadSyncSource(PartitionedTableSyncSource source) {

            tableNameValue.setText(source.getPartitioningTableName());

            principalFieldValue.removeAllItems();
            linkFieldValue.removeAllItems();
            updateDateFieldValue.removeAllItems();
            updateTypeFieldValue.removeAllItems();

            linkFieldValue.insertItemAt(
                source.getLinkFieldPartitioningTable(),
                0
            );

            updateDateFieldValue.insertItemAt(
                source.getUpdateDateFieldPartitioningTable(),
                0
            );

            updateTypeFieldValue.insertItemAt(
                source.getUpdateTypeFieldPartitioningTable(),
                0
            );

            principalFieldValue.insertItemAt(
                source.getPrincipalFieldPartitioningTable(),
                0
            );

            principalFieldValue.setSelectedIndex(0);
            linkFieldValue.setSelectedIndex(0);
            updateDateFieldValue.setSelectedIndex(0);
            updateTypeFieldValue.setSelectedIndex(0);

            if (source.getSourceURI() != null) {
                sourceUriValue.setEditable(false);
            }
        }
    }

    /**
     * Represent the panel to configure the data table properties
     */
    public class PanelDataTable extends JPanel {

        private JLabel tableNameLabel             = new JLabel();

        private JTextField tableNameValue         = new JTextField();

        private JLabel keyFieldLabel              = new JLabel();

        private JComboBox keyFieldValue           = new JComboBox();

        private JLabel linkFieldLabel             = new JLabel();

        private JComboBox linkFieldValue          = new JComboBox();

        private JLabel updateDateFieldLabel       = new JLabel();

        private JComboBox updateDateFieldValue    = new JComboBox();

        private JLabel updateTypeFieldLabel       = new JLabel();

        private JComboBox updateTypeFieldValue    = new JComboBox();

        private JLabel fieldMappingTableLabel     = new JLabel();

        private JTable fieldMappingTable          = null;

        private FieldMappingTableModel tableModel = null;

        private JButton newMappingButton          = new JButton();

        private JButton removeMappingButton       = new JButton();

        private JButton loadDataFromDBButton      = new JButton();

        private JLabel loadDataFromDBLabel        = new JLabel();
                
        /**
         * Creates a new PanelDataTable
         */
        public PanelDataTable() {
            init();
        }

        /**
         * Init the panel
         */
        private void init() {
            setLayout(null);

            setBorder(new TitledBorder("Data Table"));

            int x1 = 9;
            int x2 = 165;
            int y  = 25;
            int dy = 25;

            loadDataFromDBButton.setText("Load data from db");
            loadDataFromDBButton.setFont(defaultFont);
            loadDataFromDBButton.setBounds(x1, y, 150, 18);
            loadDataFromDBLabel.setText("Press this button to select the desired table and load its fields");
            loadDataFromDBLabel.setFont(defaultFont);
            loadDataFromDBLabel.setBounds(x2, y, 350, 18);

            loadDataFromDBButton.addActionListener(new ActionListener() {
                public void actionPerformed(ActionEvent event) {
                    loadDataFromDB();
                }
            });

            y += dy;

            tableNameLabel.setText("Table name: ");
            tableNameLabel.setFont(defaultFont);
            tableNameLabel.setBounds(x1, y, 150, 18);
            tableNameValue.setFont(defaultFont);
            tableNameValue.setBounds(x2, y, 350, 18);

            y += dy;

            keyFieldLabel.setText("Key field: ");
            keyFieldLabel.setFont(defaultFont);
            keyFieldLabel.setBounds(x1, y, 150, 18);
            keyFieldValue.setFont(defaultFont);
            keyFieldValue.setBounds(x2, y, 350, 18);
            keyFieldValue.setEditable(true);

            keyFieldValue.addItemListener(new ItemListener() {
                public void itemStateChanged(ItemEvent e) {
                    int state = e.getStateChange();
                    String item = (String)e.getItem();
                    if (state == e.DESELECTED) {
                        tableModel.removeAlreadyUse(item);
                    } else {
                        tableModel.setAlreadyUsed(item);
                    }
                    repaint();
                }
            });

            y += dy;

            linkFieldLabel.setText("Link field: ");
            linkFieldLabel.setFont(defaultFont);
            linkFieldLabel.setBounds(x1, y, 150, 18);
            linkFieldValue.setFont(defaultFont);
            linkFieldValue.setBounds(x2, y, 350, 18);
            linkFieldValue.setEditable(true);

            linkFieldValue.addItemListener(new ItemListener() {
                public void itemStateChanged(ItemEvent e) {
                    int state = e.getStateChange();
                    String item = (String)e.getItem();
                    if (state == e.DESELECTED) {
                        tableModel.removeAlreadyUse(item);
                    } else {
                        tableModel.setAlreadyUsed(item);
                    }
                    repaint();
                }
            });

            y += dy;

            updateDateFieldLabel.setText("Last update timestamp field: ");
            updateDateFieldLabel.setFont(defaultFont);
            updateDateFieldLabel.setBounds(x1, y, 150, 18);
            updateDateFieldValue.setFont(defaultFont);
            updateDateFieldValue.setBounds(x2, y, 350, 18);
            updateDateFieldValue.setEditable(true);

            updateDateFieldValue.addItemListener(new ItemListener() {
                public void itemStateChanged(ItemEvent e) {
                    int state = e.getStateChange();
                    String item = (String)e.getItem();
                    if (state == e.DESELECTED) {
                        tableModel.removeAlreadyUse(item);
                    } else {
                        tableModel.setAlreadyUsed(item);
                    }
                    repaint();
                }
            });

            y += dy;

            updateTypeFieldLabel.setText("Last update type field: ");
            updateTypeFieldLabel.setFont(defaultFont);
            updateTypeFieldLabel.setBounds(x1, y, 150, 18);
            updateTypeFieldValue.setFont(defaultFont);
            updateTypeFieldValue.setBounds(x2, y, 350, 18);
            updateTypeFieldValue.setEditable(true);

            updateTypeFieldValue.addItemListener(new ItemListener() {
                public void itemStateChanged(ItemEvent e) {
                    int state = e.getStateChange();
                    String item = (String)e.getItem();
                    if (state == e.DESELECTED) {
                        tableModel.removeAlreadyUse(item);
                    } else {
                        tableModel.setAlreadyUsed(item);
                    }
                    repaint();
                }
            });

            y += dy;

            fieldMappingTableLabel.setText("Fields Mapping:");
            fieldMappingTableLabel.setFont(defaultTableHeaderFont);
            fieldMappingTableLabel.setBounds(x1, y, 150, 18);

            //add components to panel
            this.add(loadDataFromDBButton, null);
            this.add(loadDataFromDBLabel, null);

            this.add(tableNameLabel, null);
            this.add(tableNameValue, null);

            this.add(keyFieldLabel, null);
            this.add(keyFieldValue, null);

            this.add(linkFieldLabel, null);
            this.add(linkFieldValue, null);

            this.add(updateDateFieldLabel, null);
            this.add(updateDateFieldValue, null);

            this.add(updateTypeFieldLabel, null);
            this.add(updateTypeFieldValue, null);

            y += dy;

            initTable(y);

            y += 75;

            newMappingButton.setFont(defaultFont);
            newMappingButton.setText("New");
            newMappingButton.setBounds(540, y, 80, 25);

            newMappingButton.addActionListener(new ActionListener() {
                public void actionPerformed(ActionEvent event) {
                    // stop cell editing
                    stopCellEditing();
 
                    addNewMapping();
                }
            });

            y += 35;

            removeMappingButton.setFont(defaultFont);

            removeMappingButton.setFont(defaultFont);
            removeMappingButton.setText("Remove");
            removeMappingButton.setBounds(540, y, 80, 25);

            removeMappingButton.addActionListener(new ActionListener() {
                public void actionPerformed(ActionEvent event) {
                    // stop cell editing
                    stopCellEditing();

                    removeSelectedMapping();
                }
            });

            removeMappingButton.setEnabled(false);

            //add remaining components to panel
            this.add(fieldMappingTableLabel, null);
            this.add(newMappingButton, null);

            this.add(removeMappingButton, null);

        }

        /**
         * Init the mapping table
         */
        private void initTable(int height) {
            tableModel = new FieldMappingTableModel("Server", "Client", "Binary data");
            fieldMappingTable = new JTable(tableModel);
            fieldMappingTable.setDefaultRenderer( (new Object()).getClass(),
                                                 new TableRenderer());

            fieldMappingTable.setShowGrid(true);
            fieldMappingTable.setAutoscrolls(true);
            fieldMappingTable.setSelectionMode(DefaultListSelectionModel.
                                               SINGLE_SELECTION);
            JScrollPane scrollpane = new JScrollPane(fieldMappingTable);
            scrollpane.setBounds(9, height, 506, 200);

            fieldMappingTable.setPreferredScrollableViewportSize(new
                Dimension(506, 200));
            // add listener to get changes on table values
            fieldMappingTable.getSelectionModel().addListSelectionListener(new
                ListSelectionListener() {
                public void valueChanged(ListSelectionEvent event) {
                    
                    int numRowsSelected = fieldMappingTable.getSelectedRowCount();
                    if (numRowsSelected > 0) {
                        removeMappingButton.setEnabled(true);
                    } else {
                        removeMappingButton.setEnabled(false);
                    }
                }
            });

            fieldMappingTable.setFont(defaultTableFont);
            fieldMappingTable.getTableHeader().setFont(
                defaultTableHeaderFont);

            this.add(scrollpane, BorderLayout.CENTER);
        }

        /**
         * Load table properties from the db
         */
        private void loadDataFromDB() {
            if (dataFromDB == null) {
                dataFromDB = new DataFromDBPanel(getOwner(),
                                                 defaultFont,
                                                 defaultTableHeaderFont);
            }
            ( (DataFromDBPanel)dataFromDB).showDataFromDB();

            String tableName = dataFromDB.getTableSelected();
            tableNameValue.setText(tableName);

            HashMap newMapping = new HashMap();

            if (tableName != null) {
                List columns = dataFromDB.getColumnsName();
                Tools.replaceAllExceptFirst(keyFieldValue, columns);
                Tools.replaceAllExceptFirst(linkFieldValue, columns);
                Tools.replaceAllExceptFirst(updateDateFieldValue, columns);
                Tools.replaceAllExceptFirst(updateTypeFieldValue, columns);

                int num = columns.size();

                ArrayList binaryFields = new ArrayList();

                for (int i = 0; i < num; i++) {
                    newMapping.put( (String)columns.get(i),
                                    (String)columns.get(i));
                }

                tableModel.loadMapping(newMapping, binaryFields);
                fieldMappingTable.updateUI();
            }
        }

        /**
         * Removes the selected mapping
         */
        private void removeSelectedMapping() {
            int rowToRemove = fieldMappingTable.getSelectedRow();
            tableModel.removeRow(rowToRemove);
            fieldMappingTable.updateUI();
            
            if(fieldMappingTable.getRowCount() == 0){
                removeMappingButton.setEnabled(false);
            } else if (fieldMappingTable.getRowCount() == fieldMappingTable.getSelectedRow()){
                fieldMappingTable.setRowSelectionInterval(
                        fieldMappingTable.getRowCount() - 1,
                        fieldMappingTable.getRowCount() - 1
                );               
            }

        }

        /**
         * Adds a new mapping
         */
        private void addNewMapping() throws IllegalArgumentException{
            tableModel.addNewMapping();
            int rowToSelect = tableModel.getRowCount() - 1;
            fieldMappingTable.setRowSelectionInterval(rowToSelect,
                rowToSelect);
            fieldMappingTable.updateUI();

            removeMappingButton.setEnabled(true);
        }              
        
        /**
         * Stop cell editing in the table
         */
        private void stopCellEditing() {
            if (fieldMappingTable.isEditing()) {
                fieldMappingTable.getCellEditor().stopCellEditing();
            }
        }

        /**
         * Checks if the values provided by the user are all valid. In caso of errors,
         * a IllegalArgumentException is thrown.
         */
        private void validateValues() throws IllegalArgumentException {

            stopCellEditing();

            String value = null;

            value = tableNameValue.getText();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Table name' for data table cannot be empty. Please provide a table name.");
            }

            value = (String)keyFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Key field' for data table cannot be empty. Please provide a key field.");
            }

            value = (String)linkFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Link field' for data table cannot be empty. Please provide a link field.");
            }

            value = (String)updateDateFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Last update timestamp field' for data table cannot be empty. Please provide a last update timestamp field.");
            }

            value = (String)updateTypeFieldValue.getSelectedItem();
            if (StringUtils.isEmpty(value)) {
                throw new IllegalArgumentException(
                    "Field 'Update type field' for data table cannot be empty. Please provide a last update type field.");
            }

            //
            // We have to check if there is some values used more times
            //
            checkValuesForDuplication();

        }
        
        /**
         * Check if there is some value used more times.  In case of errors,
         * a IllegalArgumentException is thrown.
         */
        private void checkValuesForDuplication() {

            String keyField        = null;
            String updateDateField = null;
            String updateTypeField = null;

            Map    mapping         = null;

            keyField        = (String)keyFieldValue.getSelectedItem();
            updateDateField = (String)updateDateFieldValue.getSelectedItem();
            updateTypeField = (String)updateTypeFieldValue.getSelectedItem();

            mapping = tableModel.getMapping();

            //
            // Check keyField
            //
            if (StringUtils.equalsIgnoreCase(keyField, updateDateField)) {
                throw new IllegalArgumentException(
                    "Fields 'Key field' and 'Last update timestamp field' can not contain the same value.");
            }

            if (StringUtils.equalsIgnoreCase(keyField, updateTypeField)) {
                throw new IllegalArgumentException(
                    "Fields 'Key field' and 'Last update type field' can not contain the same value.");
            }


            //
            // Check updateDateField
            //
            if (StringUtils.equalsIgnoreCase(updateDateField, updateTypeField)) {
                throw new IllegalArgumentException(
                    "Fields 'Last update timestamp field' and 'Last update type field' can not contain the same value.");
            }


            //
            // Check if keyField, updateDateField, updateTypeField and principalField
            // are already used in the mapping
            //
            keyField = (String)keyFieldValue.getSelectedItem();
            if (mapping.get(keyField) != null) {
                throw new IllegalArgumentException(
                    "The mapping contains a value already used as 'Key field'.\n" +
                    "Please change it or remove the mapping.");
            }

            updateDateField = (String)updateDateFieldValue.getSelectedItem();
             if (mapping.get(updateDateField) != null) {
                throw new IllegalArgumentException(
                         "The mapping contains a value already used as 'Last update timestamp field'.\n" +
                         "Please change it or remove the mapping.");
            }

            updateTypeField = (String)updateTypeFieldValue.getSelectedItem();
             if (mapping.get(updateTypeField) != null) {
                throw new IllegalArgumentException(
                         "The mapping contains a value already used as 'Last update type field'.\n" +
                         "Please change it or remove the mapping.");
            }

        }

        /**
         * Set syncSource properties with the values provided by the user.
         */
        private void getValues() {

            PartitionedTableSyncSource syncSource = (PartitionedTableSyncSource)getSyncSource();

            syncSource.setDataTableName(
                tableNameValue.getText().trim()
            );

            syncSource.setKeyFieldDataTable(
                ((String)keyFieldValue.getSelectedItem()).trim()
            );

            syncSource.setLinkFieldDataTable(
                ((String)linkFieldValue.getSelectedItem()).trim()
            );

            syncSource.setUpdateDateFieldDataTable(
                ((String)updateDateFieldValue.getSelectedItem()).trim()
            );

            syncSource.setUpdateTypeFieldDataTable(
                ((String)updateTypeFieldValue.getSelectedItem()).trim()
            );

            syncSource.setFieldMapping(tableModel.getMapping());
            syncSource.setBinaryFields(tableModel.getBinaryFields());
        }

        /**
         * Update the form with the properties of the given syncsource
         * @param source PartitionedTableSyncSource
         */
        public void loadSyncSource(PartitionedTableSyncSource source) {

            tableNameValue.setText(source.getDataTableName());

            keyFieldValue.removeAllItems();
            linkFieldValue.removeAllItems();
            updateDateFieldValue.removeAllItems();
            updateTypeFieldValue.removeAllItems();

            keyFieldValue.insertItemAt(source.getKeyFieldDataTable(), 0);

            linkFieldValue.insertItemAt(source.getLinkFieldDataTable(), 0);

            updateDateFieldValue.insertItemAt(
                source.getUpdateDateFieldDataTable(),
                0
            );

            updateTypeFieldValue.insertItemAt(
                source.getUpdateTypeFieldDataTable(),
                0
            );

            keyFieldValue.setSelectedIndex(0);
            linkFieldValue.setSelectedIndex(0);
            updateDateFieldValue.setSelectedIndex(0);
            updateTypeFieldValue.setSelectedIndex(0);

            if (source.getSourceURI() != null) {
                sourceUriValue.setEditable(false);
            }

            Map mapping = source.getFieldMapping();
            if (mapping == null) {
                mapping = new HashMap();
                source.setFieldMapping(mapping);
            }
            tableModel.loadMapping(mapping, source.getBinaryFields());
        }
    }
}
