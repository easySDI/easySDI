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

package com.funambol.db.admin.support;

import java.text.MessageFormat;
import java.util.*;

import javax.swing.JOptionPane;
import javax.swing.table.AbstractTableModel;


/**
 * Table model used to handle the field mapping (with 3 columns).
 * This model uses:
 * <ui>
 * <li>a HashMap to store the mapping from server field and client field</li>
 * <li>an ArrayList with the server fields list</li>
 * <li>an ArrayList with the binary fields list</li>
 * </ui>
 * Uses also an ArrayList of the fields already used. This list is used to set the
 * background color of the already used fields to gray.
 *
 *
 * @version $Id: FieldMappingTableModel.java,v 1.5 2007/06/18 14:31:45 luigiafassina Exp $
 */
public class FieldMappingTableModel extends AbstractTableModel {
 
    // --------------------------------------------------------------- Constants 
    private static final String MESSAGE_SERVER_FIELD_DUPLICATED = 
        "The server field ''{0}'' is already used";
 
    private static final String MESSAGE_CLIENT_FIELD_DUPLICATED = 
        "The client field ''{0}'' is already used";
   
    private static final String MESSAGE_SERVER_FIELD_EMPTY = 
        "The server field must not be empty";
    
    private static final String MESSAGE_CLIENT_FIELD_EMPTY = 
        "The client field must not be empty";
    
    // ------------------------------------------------------------ Private data

    private String[] columnNames       = null;
    private Map mapping                = null;
    private List serverColumn          = null;
    private List binaryFields          = null;
    private List fieldAlreadyUsed      = null;
    
    /**
     * Creates a new table model with the given column title
     * @param col0 the title of the first column
     * @param col1 the title of the second column
     * @param col2 the title of the third column
     */
    public FieldMappingTableModel(String col0, String col1, String col2) {
        columnNames = new String[3];
        columnNames[0] = col0;
        columnNames[1] = col1;
        columnNames[2] = col2;
    }

    /**
     * Returns the title of the given column
     * @param column int
     * @return String
     */
    public String getColumnName(int column) {
        return columnNames[column];
    }

    /**
     * Returns the class of the given column.
     * <br/>In this implementations return Boolean for the first column,
     * String for the second and the third column.
     * @param column int
     * @return Class
     */
    public Class getColumnClass(int column) {
        Class columnClass = null;

        if (column == 0 || column == 1) {
            columnClass = "".getClass();
        } else if (column == 2) {
            columnClass = Boolean.TRUE.getClass();
        }

        return columnClass;

    }

    /**
     * Return true if the table column of the given row is already used.
     *
     * @param row int
     * @return boolean
     */
    public boolean isAlreadyUsed(int row) {

        String fieldName = (String)serverColumn.get(row);

        if (fieldAlreadyUsed != null &&
            fieldAlreadyUsed.indexOf(fieldName) != -1) {
            return true;
        }

        return false;
    }
    
    /**
     * Set the given table field as alreadyUsed
     * @param field String
     */
    public void setAlreadyUsed(String field) {
        if (fieldAlreadyUsed == null) {
            fieldAlreadyUsed = new ArrayList();
        }
        fieldAlreadyUsed.add(field);
    }

    /**
     * Remove the given field from the list of the alreadyUsed fields
     * @param field String
     */
    public void removeAlreadyUse(String field) {
        if (fieldAlreadyUsed != null) {
            fieldAlreadyUsed.remove(field);
        }
    }

    /**
     * Get the value in the give row and col.
     * <br/>For the first column returns Boolean.TRUE if the field is "binary".
     * <br/>For the second column returns the server field.
     * <br/>For the third column returns the mapping of the server field.
     * @param row int
     * @param col int
     * @return Object
     */
    public Object getValueAt(int row, int col) {
        Object value = null;

        if (col == 0) {
            value = (String)serverColumn.get(row);
        } else if (col == 1) {
            String fc = (String)serverColumn.get(row);
            value = (String)mapping.get(fc);
        } else if (col == 2) {
            String firstColumnName = (String)serverColumn.get(row);
            if (binaryFields.indexOf(firstColumnName) != -1) {
                value = new Boolean(true);
            } else {
                value = new Boolean(false);
            }
        }

        return value;
    }

    /**
     * Set a value in the model
     * @param value the value to set
     * @param row the row of the value
     * @param col the column of the value
     */
    public void setValueAt(Object value, int row, int col) {

        String oldValueFirstColumn  = (String)getValueAt(row, 0);
        String oldValueSecondColumn = (String)getValueAt(row, 1);

        if (col == 0) {
            //check if the server value is empty or already used
            if ((((String)value) == null) || (((String)value).equals(""))){
                JOptionPane.showMessageDialog(
                        null,
                        MESSAGE_SERVER_FIELD_EMPTY);
            } else if (checkDoubleValues(row, col, (String)value)){
                JOptionPane.showMessageDialog(
                        null,
                        MessageFormat.format(
                                MESSAGE_SERVER_FIELD_DUPLICATED,
                                new Object[] {value}));
            } else {
                serverColumn.remove(row);
                mapping.remove(oldValueFirstColumn);

                mapping.put( (String)value, oldValueSecondColumn);
                serverColumn.add(row, (String)value);                
            }                        
        }

        if (col == 1) {
            //check if the client value is empty or already used
            if ((((String)value) == null) || (((String)value).equals(""))){
                JOptionPane.showMessageDialog(
                        null,
                        MESSAGE_CLIENT_FIELD_EMPTY);
            } else if (checkDoubleValues(row, col,(String)value)){
                JOptionPane.showMessageDialog(
                        null,
                        MessageFormat.format(
                                MESSAGE_CLIENT_FIELD_DUPLICATED,
                                new Object[] {value}));                 
            } else {
                serverColumn.remove(row);
                mapping.remove(oldValueFirstColumn);

                mapping.put(oldValueFirstColumn,
                        (String)value);
                serverColumn.add(row, oldValueFirstColumn);              
            }              
        }

        if (col == 2) {
            if ( ( (Boolean)value).booleanValue()) {
                binaryFields.add( (String)serverColumn.get(row));
            } else {
                binaryFields.remove( (String)serverColumn.get(row));
            }
        }
    }
       
    /**
     * Remove the given row.
     * @param row int
     */
    public void removeRow(int row) {            
        String fc = (String)serverColumn.get(row);
        serverColumn.remove(row);
        mapping.remove(fc);
        binaryFields.remove(fc);
    }

    /**
     * Add new mapping entry
     */
    public void addNewMapping() {

        boolean found       = true;

        int     contServer  = 0;
        int     contClient  = 0;
        
        String  serverField = "serverField_";
        String  clientField = "CLIENTFIELD_";
        
        if (mapping.size() != 0){
            while (found){
                found=false;
                // search the first value not used for the server field
                for (int i= 0; i < mapping.size() ; i++){
                    if ((serverField + contServer).equals(getValueAt(i,0))){
                        found = true;
                    }
                }
                contServer++;
            }
            contServer--;
            
            found = true;
            while (found){
                found = false;
                // search the first value not used for the client field
                for (int i= 0; i < mapping.size() ; i++){
                    if ((clientField + contClient).equals(getValueAt(i,1))){                     
                        found = true;
                    }
                }
                contClient++;
            }
            contClient--;
        }

        serverColumn.add(serverField+contServer);
        mapping.put(serverField+contServer, clientField+contClient);
        
    }

    /**
     * Returns the number of the rows
     * @return int
     */
    public int getRowCount() {
        if (mapping == null) {
            return 0;
        }
        return mapping.size();
    }

    /**
     * Returns the number of the columns
     * @return int
     */
    public int getColumnCount() {
        return 3;
    }

    /**
     * Returns the mapping
     * @return Map
     */
    public Map getMapping() {
        return mapping;
    }

    /**
     * Returns the binary fields.
     * @return ArrayList
     */
    public List getBinaryFields() {
        return binaryFields;
    }

    /**
     * Load the given mapping and the given binary fields list.
     * @param mapping HashMap
     * @param binaryFields ArrayList
     */
    public void loadMapping(Map mapping, List binaryFields) {

        this.mapping = mapping;

        Iterator keysIt = mapping.keySet().iterator();

        serverColumn = new ArrayList();

        String key   = null;
        String value = null;
        while (keysIt.hasNext()) {
            key   = (String)keysIt.next();
            value = (String)mapping.get(key);
            mapping.put(key, value);
            serverColumn.add(key);
        }

        this.binaryFields = binaryFields;
        if (this.binaryFields == null) {
            this.binaryFields = new ArrayList();
        }
    }

    /**
     * Returns always TRUE. All cells are editable.
     * @param row int
     * @param col int
     * @return boolean
     */
    public boolean isCellEditable(int row, int col) {
        return true;
    }

    /**
     * Checks if the given rows is checked (binary field).
     * @param row int
     * @return boolean
     */
    public boolean isChecked(int row) {
        Object obj = getValueAt(row, 2);
        return ( (Boolean)obj).booleanValue();
    }
    
    // --------------------------------------------------------- Private Methods    
    /**
     * Check if there is a duplicated value in the specified column
     * @param col the column to check
     * @param value the value to be compared
     */
    private boolean checkDoubleValues(int row, int col, String value) {

        int counter = 0;

        for (int i = 0 ; i < mapping.size() ; i ++){
            if (((String)getValueAt(i, col)).equals(value) && row != i){
                counter ++;
            }
        }

        if (counter == 0) {
            return false;
        } else {
            return true;
        }            
    }
}
