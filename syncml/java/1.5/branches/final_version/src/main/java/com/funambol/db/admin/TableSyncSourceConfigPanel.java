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
import java.awt.Rectangle;

import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.swing.*;
import javax.swing.border.TitledBorder;
import javax.swing.event.ListSelectionEvent;
import javax.swing.event.ListSelectionListener;

import org.apache.commons.lang.StringUtils;


import com.funambol.framework.engine.source.ContentType;
import com.funambol.framework.engine.source.SyncSourceInfo;
import com.funambol.framework.engine.source.SyncSource;

import com.funambol.framework.logging.FunambolLogger;
import com.funambol.framework.logging.FunambolLoggerFactory;

import com.funambol.admin.AdminException;

import com.funambol.db.admin.support.DataFromDBPanel;
import com.funambol.db.admin.support.FieldMappingTableModel;
import com.funambol.db.admin.support.TableRenderer;
import com.funambol.db.admin.support.Tools;
import com.funambol.db.engine.source.TableSyncSource;
import java.util.Iterator;

/**
 * This is the configuration panel for the <code>TableSyncSource</code>.
 *
 * @version $Id: TableSyncSourceConfigPanel.java,v 1.6 2007/06/18 14:31:44 luigiafassina Exp $
 */
public class TableSyncSourceConfigPanel extends BaseConfigPanel {

    // --------------------------------------------------------------- Constants

    public static final String NAME_ALLOWED_CHARS =
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-_.";

    private static final String DEFAULT_JNDI_NAME = "jdbc/fnblds";

    // ------------------------------------------------------------ Private data

    private JLabel panelName                  = new JLabel();
    private TitledBorder titledBorder         = new TitledBorder("");

    private JLabel sourceUriLabel             = new JLabel();
    private JTextField sourceUriValue         = new JTextField();

    private JLabel nameLabel                  = new JLabel();
    private JTextField nameValue              = new JTextField();

    private JLabel typeLabel                  = new JLabel();
    private JTextField typeValue              = new JTextField();

    private JLabel jndiNameLabel              = new JLabel();
    private JTextField jndiNameValue          = new JTextField();

    private JPanel tableInfoPanel             = new JPanel();

    private JLabel tableNameLabel             = new JLabel();
    private JTextField tableNameValue         = new JTextField();

    private JLabel keyFieldLabel              = new JLabel();
    private JComboBox keyFieldValue           = new JComboBox();

    private JLabel updateDateFieldLabel       = new JLabel();
    private JComboBox updateDateFieldValue    = new JComboBox();

    private JLabel updateTypeFieldLabel       = new JLabel();
    private JComboBox updateTypeFieldValue    = new JComboBox();

    private JLabel principalFieldLabel        = new JLabel();
    private JComboBox principalFieldValue     = new JComboBox();

    private JLabel fieldMappingTableLabel     = new JLabel();
    private JTable fieldMappingTable          = null;
    private FieldMappingTableModel tableModel = null;

    private JButton newMappingButton          = new JButton();
    private JButton removeMappingButton       = new JButton();

    private JButton loadDataFromDBButton      = new JButton();

    private JLabel loadDataFromDBLabel        = new JLabel();

    private JButton confirmButton             = new JButton();

    private DataFromDBPanel dataFromDB        = null;
   
    protected static final FunambolLogger log = FunambolLoggerFactory.getLogger("myconnector");
    
    /**
     * Creates the config panel
     */
    public TableSyncSourceConfigPanel() {
    	 init();
    }

    /**
     * Load the sync source
     */
    public void updateForm() {

        if (!(getSyncSource() instanceof TableSyncSource)) {
            notifyError(
                    new AdminException(
                            "This is not a TableSyncSource! Unable to process SyncSource values."
                    )
            );
            return;
        }

        TableSyncSource syncSource = (TableSyncSource)getSyncSource();

        if (getState() == STATE_INSERT) {
            confirmButton.setText("Add");
        } else if (getState() == STATE_UPDATE) {
            confirmButton.setText("Save");
        }
        sourceUriValue.setText(this.getSyncSource().getSourceURI());
        nameValue.setText(this.getSyncSource().getName());
        //typeValue.setText((this.getSyncSource()).getType());
        //typeValue.setText(this.getSyncSource().getType().toString());

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
        tableNameValue.setText(syncSource.getTableName());

        keyFieldValue.removeAllItems();
        updateDateFieldValue.removeAllItems();
        updateTypeFieldValue.removeAllItems();
        principalFieldValue.removeAllItems();
        keyFieldValue.insertItemAt(syncSource.getKeyField(), 0);

        updateDateFieldValue.insertItemAt(
            syncSource.getUpdateDateField(), 0);

        updateTypeFieldValue.insertItemAt(
            syncSource.getUpdateTypeField(), 0);

        principalFieldValue.insertItemAt(
            syncSource.getPrincipalField(),  0);

        keyFieldValue.setSelectedIndex(0);
        updateDateFieldValue.setSelectedIndex(0);
        updateTypeFieldValue.setSelectedIndex(0);
        principalFieldValue.setSelectedIndex(0);

        if (this.getSyncSource().getSourceURI() != null) {
            sourceUriValue.setEditable(false);
        }

        Map mapping = syncSource.getFieldMapping();
        if (mapping == null) {
            mapping = new HashMap();
            syncSource.setFieldMapping(mapping);
        }
        tableModel.loadMapping(mapping, syncSource.getBinaryFields());
    }


    // --------------------------------------------------------- Private methods

    /**
     * Init the panel
     */
    private void init() {

        this.setLayout(null);
        this.setPreferredSize(new Dimension(600, 675));

        panelName.setFont(titlePanelFont);
        panelName.setText("Edit Table SyncSource");
        panelName.setBounds(new Rectangle(14, 5, 316, 28));
        panelName.setAlignmentX(SwingConstants.CENTER);
        panelName.setBorder(titledBorder);

        int x1 = 14;
        int x2 = 170;
        int y  = 50;
        int dy = 25;

        sourceUriLabel.setText("Source URI: ");
        sourceUriLabel.setFont(defaultFont);
        sourceUriLabel.setBounds(new Rectangle(x1, y, 150, 18));
        sourceUriValue.setFont(defaultFont);
        sourceUriValue.setBounds(new Rectangle(x2, y, 350, 18));

        y += dy;

        nameLabel.setText("Name: ");
        nameLabel.setFont(defaultFont);
        nameLabel.setBounds(new Rectangle(x1, y, 150, 18));
        nameValue.setFont(defaultFont);
        nameValue.setBounds(new Rectangle(x2, y, 350, 18));

        y += dy;

        typeLabel.setText("Type: ");
        typeLabel.setFont(defaultFont);
        typeLabel.setBounds(new Rectangle(x1, y, 150, 18));
        typeValue.setFont(defaultFont);
        typeValue.setBounds(new Rectangle(x2, y, 350, 18));

        y += dy;

        jndiNameLabel.setText("JNDI Name datasource: ");
        jndiNameLabel.setFont(defaultFont);
        jndiNameLabel.setBounds(new Rectangle(x1, y, 150, 18));
        jndiNameValue.setFont(defaultFont);
        jndiNameValue.setBounds(new Rectangle(x2, y, 350, 18));
        jndiNameValue.setText(DEFAULT_JNDI_NAME);

        y += dy;
        y += 10;

        initTableInfoPanel();
        tableInfoPanel.setLocation(5, y);

        y += dy;
        y += 425;

        confirmButton.setFont(defaultFont);
        confirmButton.setText("Add");
        confirmButton.setBounds(230, y, 70, 25);

        confirmButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                try {
                    validateValues();
                    getValues();
                    if (getState() == STATE_INSERT) {
                        TableSyncSourceConfigPanel.this.actionPerformed(new
                                ActionEvent(TableSyncSourceConfigPanel.this,
                                            ACTION_EVENT_INSERT,
                                            event.getActionCommand()));
                    } else {
                        TableSyncSourceConfigPanel.this.actionPerformed(new
                                ActionEvent(TableSyncSourceConfigPanel.this,
                                            ACTION_EVENT_UPDATE,
                                            event.getActionCommand()));
                    }
                } catch (Exception e) {
                    notifyError(new AdminException(e.getMessage()));
                }
            }
        });


        // add all components to the panel
        this.add(sourceUriLabel, null);
        this.add(sourceUriValue, null);
        this.add(panelName, null);
        this.add(nameLabel, null);
        this.add(nameValue, null);
        this.add(typeLabel, null);
        this.add(typeValue, null);        
        this.add(jndiNameLabel, null);
        this.add(jndiNameValue, null);

        this.add(tableInfoPanel, null);

        this.add(confirmButton, null);
    }


    private void initTableInfoPanel() {

        tableInfoPanel.setLayout(null);

        tableInfoPanel.setBorder(new javax.swing.border.TitledBorder("Table Infos:"));
        tableInfoPanel.setSize(650, 425);

        int x1 = 14;
        int x2 = 170;
        int y  = 25;
        int dy = 25;

        loadDataFromDBButton.setFont(defaultFont);
        loadDataFromDBButton.setText("Load data from db");
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
        tableNameLabel.setBounds(new Rectangle(x1, y, 150, 18));
        tableNameValue.setFont(defaultFont);
        tableNameValue.setBounds(new Rectangle(x2, y, 350, 18));

        y += dy;

        keyFieldLabel.setText("Key field: ");
        keyFieldLabel.setFont(defaultFont);
        keyFieldLabel.setBounds(new Rectangle(x1, y, 150, 18));
        keyFieldValue.setFont(defaultFont);
        keyFieldValue.setBounds(new Rectangle(x2, y, 350, 18));
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

        updateDateFieldLabel.setText("Last update timestamp field: ");
        updateDateFieldLabel.setFont(defaultFont);
        updateDateFieldLabel.setBounds(new Rectangle(x1, y, 150, 18));
        updateDateFieldValue.setFont(defaultFont);
        updateDateFieldValue.setBounds(new Rectangle(x2, y, 350, 18));
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
        updateTypeFieldLabel.setBounds(new Rectangle(x1, y, 150, 18));
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

        principalFieldLabel.setText("Principal field: ");
        principalFieldLabel.setFont(defaultFont);
        principalFieldLabel.setBounds(new Rectangle(x1, y, 150, 18));
        principalFieldValue.setFont(defaultFont);
        principalFieldValue.setBounds(x2, y, 350, 18);
        principalFieldValue.setEditable(true);

        principalFieldValue.addItemListener(new ItemListener() {
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
        tableInfoPanel.add(loadDataFromDBButton, null);
        tableInfoPanel.add(loadDataFromDBLabel, null);

        tableInfoPanel.add(tableNameLabel, null);
        tableInfoPanel.add(tableNameValue, null);

        tableInfoPanel.add(keyFieldLabel, null);
        tableInfoPanel.add(keyFieldValue, null);

        tableInfoPanel.add(updateDateFieldLabel, null);
        tableInfoPanel.add(updateDateFieldValue, null);

        tableInfoPanel.add(updateTypeFieldLabel, null);
        tableInfoPanel.add(updateTypeFieldValue, null);

        tableInfoPanel.add(principalFieldLabel, null);
        tableInfoPanel.add(principalFieldValue, null);

        tableInfoPanel.add(fieldMappingTableLabel, null);

        y += dy;

        //init and add table to panel
        initTable(y);

        y += 75;

        newMappingButton.setFont(defaultFont);
        newMappingButton.setText("New");
        newMappingButton.setBounds(540, y, 80, 25);

        newMappingButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
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
                stopCellEditing();
                removeSelectedMapping();
            }
        });

        removeMappingButton.setEnabled(false);

        y += 120;

        // add remaining components to the panel
        tableInfoPanel.add(newMappingButton, null);
        tableInfoPanel.add(removeMappingButton, null);        
    }

    /**
     * Init the mapping table
     * @param y int
     */
    private void initTable(int y) {
        tableModel = new FieldMappingTableModel("Server",
                                                "Client",
                                                "Binary data");
        fieldMappingTable = new JTable(tableModel);
        //fieldMappingTable.setDefaultRenderer((new Object()).getClass(), 
        											//new TableRenderer());

        fieldMappingTable.setDefaultRenderer(Object.class,
                                                     new TableRenderer());
        fieldMappingTable.setShowGrid(true);
        fieldMappingTable.setAutoscrolls(true);
        fieldMappingTable.setSelectionMode(DefaultListSelectionModel.
                                           SINGLE_SELECTION);
        JScrollPane scrollpane = new JScrollPane(fieldMappingTable);
        scrollpane.setBounds(14, y, 506, 200);

        fieldMappingTable.setPreferredScrollableViewportSize(new Dimension(
                506, 200));

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

        tableInfoPanel.add(scrollpane, BorderLayout.CENTER);
    }


    /**
     * Shows the data panel to load the informations from the database and
     * sets them in this config panel
     */
    private void loadDataFromDB() {

        if (dataFromDB == null) {
            dataFromDB = new DataFromDBPanel(getOwner(), defaultFont,
                                             defaultTableHeaderFont);
        }
        ( (DataFromDBPanel)dataFromDB).showDataFromDB();


        String tableName = dataFromDB.getTableSelected();

        HashMap newMapping = new HashMap();
        ArrayList binaryFields = new ArrayList();
        if (StringUtils.isNotEmpty(tableName)) {

            tableNameValue.setText(tableName);

            List columns = dataFromDB.getColumnsName();
            Tools.replaceAllExceptFirst(keyFieldValue, columns);
            Tools.replaceAllExceptFirst(principalFieldValue, columns);
            Tools.replaceAllExceptFirst(updateDateFieldValue, columns);
            Tools.replaceAllExceptFirst(updateTypeFieldValue, columns);

            int num = columns.size();
            for (int i = 0; i < num; i++) {
                newMapping.put( (String)columns.get(i),
                                (String)columns.get(i));
            }
            tableModel.loadMapping(newMapping, binaryFields);
            fieldMappingTable.updateUI();
        }
    }

    /**
     * Checks if the values provided by the user are all valid. In case of errors,
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

        value = tableNameValue.getText();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Table name' cannot be empty. Please provide a table name.");
        }

        value = (String)keyFieldValue.getSelectedItem();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Key field' cannot be empty. Please provide a key field.");
        }

        value = (String)updateDateFieldValue.getSelectedItem();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Last update timestamp field' cannot be empty. Please provide a last update timestamp field.");
        }

        value = (String)updateTypeFieldValue.getSelectedItem();
        if (StringUtils.isEmpty(value)) {
            throw new IllegalArgumentException(
                "Field 'Last update type field' cannot be empty. Please provide a last update type field.");
        }


        //
        // We have to check if there is some values used more times
        //
        checkValuesForDuplication();

        validateMapping();
    }

    /**
     * Set syncSource properties with the values provided by the user.
     */
    private void getValues() {
        TableSyncSource syncSource = (TableSyncSource)getSyncSource();

        syncSource.setSourceURI(sourceUriValue.getText().trim());
        syncSource.setName(nameValue.getText().trim());
        syncSource.setType(typeValue.getText().trim());

        syncSource.setJndiName(jndiNameValue.getText().trim());

        syncSource.setTableName(tableNameValue.getText().trim());
        syncSource.setKeyField( ( (String)keyFieldValue.getSelectedItem()).
                               trim());
        syncSource.setUpdateDateField( ( (String)updateDateFieldValue.
                                        getSelectedItem()).trim());
        syncSource.setUpdateTypeField( ( (String)updateTypeFieldValue.
                                        getSelectedItem()).trim());

        String temp = ( (String)principalFieldValue.getSelectedItem());
        if (temp != null) {
            temp = temp.trim();
        }
        syncSource.setPrincipalField(temp);

        syncSource.setFieldMapping(tableModel.getMapping());
        syncSource.setBinaryFields(tableModel.getBinaryFields());

        ContentType[] contentTypes = new ContentType[1];

        contentTypes[0] = new ContentType();
        contentTypes[0].setType("text/plain");
        contentTypes[0].setVersion("1.0");

        SyncSourceInfo sourceInfo = new SyncSourceInfo(contentTypes, 0);
        sourceInfo.setPreferred(0);

        syncSource.setInfo(sourceInfo);
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
     * Adds new mapping
     */
    private void addNewMapping() {
        tableModel.addNewMapping();
        int rowToSelect = tableModel.getRowCount() - 1;
        fieldMappingTable.setRowSelectionInterval(rowToSelect, rowToSelect);
        fieldMappingTable.updateUI();

        removeMappingButton.setEnabled(true);
    }

    /**
     * Stop cell editing
     */
    private void stopCellEditing() {
        if (fieldMappingTable.isEditing()) {
            fieldMappingTable.getCellEditor().stopCellEditing();
        }
    }

    /**
     * Check if there is some value used more times.  In case of errors,
     * a IllegalArgumentException is thrown.
     */
    private void checkValuesForDuplication() {

        String keyField        = null;
        String updateDateField = null;
        String updateTypeField = null;
        String principalField  = null;
        Map    mapping         = null;

        keyField        = (String)keyFieldValue.getSelectedItem();
        updateDateField = (String)updateDateFieldValue.getSelectedItem();
        updateTypeField = (String)updateTypeFieldValue.getSelectedItem();
        principalField  = (String)principalFieldValue.getSelectedItem();

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

        if (StringUtils.isNotEmpty(principalField)) {
            if (StringUtils.equalsIgnoreCase(keyField, principalField)) {
                throw new IllegalArgumentException(
                    "Fields 'Key field' and 'Principal field' can not contain the same value.");
            }
        }

        //
        // Check updateDateField
        //
        if (StringUtils.equalsIgnoreCase(updateDateField, updateTypeField)) {
            throw new IllegalArgumentException(
                "Fields 'Last update timestamp field' and 'Last update type field' can not contain the same value.");
        }

        if (StringUtils.isNotEmpty(principalField)) {
            if (StringUtils.equalsIgnoreCase(updateDateField, principalField)) {
                throw new IllegalArgumentException(
                    "Fields 'Last update timestamp field' and 'Principal field' can not contain the same value.");
            }
        }

        //
        // Check updateTypeField
        //
        if (StringUtils.isNotEmpty(principalField)) {
            if (StringUtils.equalsIgnoreCase(updateTypeField, principalField)) {
                throw new IllegalArgumentException(
                    "Fields 'Last update type field' and 'Principal field' can not contain the same value.");
            }
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

        principalField = (String)principalFieldValue.getSelectedItem();
        if (StringUtils.isNotEmpty(principalField) && mapping.get(principalField) != null) {
            throw new IllegalArgumentException(
                    "The mapping contains a value already used as 'Principal field'.\n" +
                    "Please change it or remove the mapping.");
        }
    }

    /**
     * Checks if the mapping contains a empty value. In case of errors,
     * a IllegalArgumentException is thrown.
     */
    private void validateMapping() {
        Map mapping = null;

        mapping = tableModel.getMapping();
        Iterator it = mapping.keySet().iterator();
        String serverField  = null;
        String clientField  = null;
        List   clientFields = new ArrayList();
        while (it.hasNext()) {
            serverField = (String)it.next();
            clientField = (String)mapping.get(serverField);
            if (clientFields.indexOf(clientField) != -1) {
                throw new IllegalArgumentException(
                    "There are different server field mapped on the same client field (" + clientField + ").");
            }
            clientFields.add(clientField);
        }
    }
}
