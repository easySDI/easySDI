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

import java.awt.Font;
import java.awt.Frame;
import java.awt.Rectangle;

import java.awt.event.*;

import java.io.*;
import java.net.*;
import java.sql.*;

import java.util.*;
import java.util.regex.Pattern;

import javax.swing.*;
import javax.swing.event.*;
import javax.swing.filechooser.FileFilter;


/**
 * It's the dialog panel to load the data from the database
 *
 * @version $Id: DataFromDBPanel.java,v 1.5 2007/06/18 14:31:44 luigiafassina Exp $
 */
public class DataFromDBPanel extends JDialog {

    // --------------------------------------------------------------- Constants
    private static final String PROPERTIES_FILE = 
        "funambol-db-connector.properties";

    private static final String PROPERTIES_FILE_HEADER ="Funambol DB Connector";

    private static final String PROPERTY_JAR_FILE = "jar";
    private static final String PROPERTY_DRIVER   = "driver";
    private static final String PROPERTY_URL      = "url";
    private static final String PROPERTY_USER     = "user";
    private static final String PROPERTY_PASSWORD = "password";

    private static final String DEFAULT_JAR_FILE_VALUE      = "Search in classpath";
    private static final String DEFAULT_DRIVER_VALUE        = "org.hsqldb.jdbcDriver";
    private static final String DEFAULT_URL_VALUE           = "jdbc:hsqldb:hsql://localhost/funambol";
    private static final String DEFAULT_USER_VALUE          = "sa";
    private static final String DEFAULT_PASSWORD_VALUE      = "";

    private static final String FILE_SPLITER                = ";";

    // ------------------------------------------------------------ Private Data

    private Connection conn      = null;
    private String tableSelected = null;
    private List   columnsName   = null;


    private JLabel jarLabel                   = new JLabel();
    private JTextField jarValue               = new JTextField();

    private JButton browseFile                = new JButton();

    private JLabel driverLabel                = new JLabel();
    private JTextField driverValue            = new JTextField();

    private JLabel urlLabel                   = new JLabel();
    private JTextField urlValue               = new JTextField();

    private JLabel userLabel                  = new JLabel();
    private JTextField userValue              = new JTextField();

    private JLabel passwordLabel              = new JLabel();
    private JPasswordField passwordValue      = new JPasswordField();

    public JButton connectButton              = new JButton();

    private JScrollPane scrollPaneListTable   = new JScrollPane();
    private JLabel listTableLabel             = new JLabel();
    private JList listTable                   = new JList();

    private JFileChooser fileChooser          = new JFileChooser();

    private JScrollPane scrollPaneListColumns = new JScrollPane();
    private JLabel listColumnLabel            = new JLabel();
    private JList listColumns                 = new JList();

    private JButton okButton                  = new JButton();
    private JButton cancelButton              = new JButton();

    private Font defaultFont                  = null;
    private Font defaultTableHeaderFont       = null;

    /**
     * Creates a new DataFromDBPanel
     * @param owner Frame
     * @param defaultFont Font
     * @param defaultTableHeaderFont Font
     */
    public DataFromDBPanel(Frame owner, Font defaultFont, Font defaultTableHeaderFont) {
        super(owner);
        this.defaultFont = defaultFont;
        this.defaultTableHeaderFont = defaultTableHeaderFont;

        init();
    }

    // ---------------------------------------------------------- Public methods

    /**
     * Returns tableSelected
     * @return the table selected. <code>null</code> if there isn't a table selected.
     */
    public String getTableSelected() {
        return tableSelected;
    }

    /**
     * Returns the columns list of the selected table
     * @return the columns list. <code>null</code>  if there isn't a table selected.
     */
    public List getColumnsName() {
        return columnsName;
    }

    /**
     * Load the connection properties and show this dialog panel
     */
    public void showDataFromDB() {

        setLocationRelativeTo(this.getParent());
        Properties prop = new Properties();
        try {
            prop.load(new FileInputStream(PROPERTIES_FILE));

            jarValue.setText(prop.getProperty(PROPERTY_JAR_FILE));
            driverValue.setText(prop.getProperty(PROPERTY_DRIVER));
            urlValue.setText(prop.getProperty(PROPERTY_URL));
            userValue.setText(prop.getProperty(PROPERTY_USER));
            passwordValue.setText(prop.getProperty(PROPERTY_PASSWORD));

        } catch (IOException ex) {
            // if there is a error the values are empty
            // (for example at first time)
            driverValue.setText(DEFAULT_DRIVER_VALUE);
            urlValue.setText(DEFAULT_URL_VALUE);
            userValue.setText(DEFAULT_USER_VALUE);
            passwordValue.setText(DEFAULT_PASSWORD_VALUE);
        }

        if (jarValue.getText().equals("")) {
            jarValue.setText(DEFAULT_JAR_FILE_VALUE);
        }

        listTable.setListData(new Object[0]);
        listColumns.setListData(new Object[0]);

        this.setVisible(true);
    }


    // --------------------------------------------------------- Private Methods

    /**
     * Init the dialog panel
     */
    private void init() {

        this.getContentPane().setLayout(null);
        this.setTitle("Load data from db");
        this.setModal(true);
        this.setSize(700, 380);

        this.setResizable(false);

        addComponentListener(new ComponentAdapter() {
            public void componentShown(ComponentEvent e) {
                connectButton.requestFocus(true);
            }
        });

        jarLabel.setText("Jar driver: ");
        jarLabel.setFont(defaultFont);
        jarLabel.setBounds(new Rectangle(14, 25, 100, 18));
        jarValue.setFont(defaultFont);
        jarValue.setBounds(new Rectangle(120, 25, 200, 18));

        browseFile.setText("...");
        browseFile.setFont(defaultFont);
        browseFile.setBounds(new Rectangle(325, 25, 30, 18));

        browseFile.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                showFileChooser();
            }
        });

        FileFilter filter = new SimpleFileFilter("jar", "Jar file");

        fileChooser.setFileFilter(filter);
        fileChooser.setMultiSelectionEnabled(true);

        driverLabel.setText("Driver: ");
        driverLabel.setFont(defaultFont);
        driverLabel.setBounds(new Rectangle(14, 50, 100, 18));
        driverValue.setFont(defaultFont);
        driverValue.setBounds(new Rectangle(120, 50, 200, 18));

        urlLabel.setText("URL: ");
        urlLabel.setFont(defaultFont);
        urlLabel.setBounds(new Rectangle(14, 75, 100, 18));
        urlValue.setFont(defaultFont);
        urlValue.setBounds(new Rectangle(120, 75, 200, 18));

        userLabel.setText("User: ");
        userLabel.setFont(defaultFont);
        userLabel.setBounds(new Rectangle(14, 100, 100, 18));
        userValue.setFont(defaultFont);
        userValue.setBounds(new Rectangle(120, 100, 200, 18));

        passwordLabel.setText("Password: ");
        passwordLabel.setFont(defaultFont);
        passwordLabel.setBounds(new Rectangle(14, 125, 100, 18));
        passwordValue.setFont(defaultFont);
        passwordValue.setBounds(new Rectangle(120, 125, 200, 18));

        connectButton.setText("Connect");
        connectButton.setFont(defaultFont);
        connectButton.setBounds(new Rectangle(115, 150, 100, 18));

        connectButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                connectToDB();
            }
        });

        listTableLabel.setText("Tables List: ");
        listTableLabel.setFont(defaultTableHeaderFont);
        listTableLabel.setBounds(new Rectangle(14, 175, 100, 18));

        listTable.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        listTable.addListSelectionListener(new ListSelectionListener() {
            public void valueChanged(ListSelectionEvent sel) {
                tableSelected = (String)listTable.getSelectedValue();
                if (tableSelected != null) {
                    loadColumnsList(tableSelected);
                } else {
                    listColumns.setListData(new Object[] {});
                }
            }
        });
        scrollPaneListTable = new JScrollPane(listTable);
        scrollPaneListTable.setBounds(new Rectangle(14, 200, 306, 100));
        scrollPaneListTable.setAutoscrolls(true);

        listColumns.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        listColumnLabel.setText("Columns List: ");
        listColumnLabel.setFont(defaultTableHeaderFont);
        listColumnLabel.setBounds(new Rectangle(364, 175, 100, 18));

        scrollPaneListColumns = new JScrollPane(listColumns);
        scrollPaneListColumns.setBounds(new Rectangle(364, 200, 306, 100));
        scrollPaneListColumns.setAutoscrolls(true);

        okButton.setText("OK");
        okButton.setFont(defaultFont);
        okButton.setBounds(new Rectangle(247, 315, 70, 18));

        okButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                disconnectToDB();
                setVisible(false);
            }
        });

        cancelButton.setText("Cancel");
        cancelButton.setFont(defaultFont);
        cancelButton.setBounds(new Rectangle(367, 315, 70, 18));

        cancelButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                tableSelected = null;
                if (columnsName != null) {
                    columnsName.clear();
                }
                disconnectToDB();
                setVisible(false);
            }
        });

        this.getContentPane().add(jarLabel);
        this.getContentPane().add(jarValue);
        this.getContentPane().add(browseFile);

        this.getContentPane().add(driverLabel);
        this.getContentPane().add(driverValue);

        this.getContentPane().add(urlLabel);
        this.getContentPane().add(urlValue);

        this.getContentPane().add(userLabel);
        this.getContentPane().add(userValue);

        this.getContentPane().add(passwordLabel);
        this.getContentPane().add(passwordValue);

        this.getContentPane().add(connectButton);

        this.getContentPane().add(listTableLabel);
        this.getContentPane().add(scrollPaneListTable);

        this.getContentPane().add(listColumnLabel);
        this.getContentPane().add(scrollPaneListColumns);

        this.getContentPane().add(okButton);
        this.getContentPane().add(cancelButton);

    }

    /**
     * Disconnect to database
     */
    private void disconnectToDB() {
        if (conn != null) {
            try {
                conn.close();
            } catch (SQLException ex) {
            }
        }
    }

    /**
     * Connect to database getting the connection properties from the textfield
     * and saving them in the properties file.
     * Load the driver from the selected file using a URLClassLoader.
     */
    private void connectToDB() {

        Properties prop = new Properties();

        String jar      = jarValue.getText().trim();

        if (!DEFAULT_JAR_FILE_VALUE.equals(jar)) {
            prop.setProperty(PROPERTY_JAR_FILE, jar);
        } else {
            jar = null;
        }

        if (jar == null || jar.equals("")) {
            jarValue.setText(DEFAULT_JAR_FILE_VALUE);
            jar = null;
        }

        String driverClassName   = driverValue.getText().trim();
        String url      = urlValue.getText().trim();
        String user     = userValue.getText().trim();
        String password = new String(passwordValue.getPassword());

        // save properties
        prop.setProperty(PROPERTY_DRIVER,   driverClassName);
        prop.setProperty(PROPERTY_URL,      url);
        prop.setProperty(PROPERTY_USER,     user);
        prop.setProperty(PROPERTY_PASSWORD, password);

        try {
            prop.store(new FileOutputStream(PROPERTIES_FILE),
                       PROPERTIES_FILE_HEADER);
        } catch (IOException e) {
            showError("Error saving properties file: " + e.getMessage());
        }

        try {

            ClassLoader cl    = null;

            if (jar != null) {

                String[] jars = splitText(jarValue.getText());
                URL[] urls = new URL[jars.length];
                cl = null;
                for (int i = 0; i < jars.length; i++) {
                    urls[i] = new URL(jars[i]);
                }
                cl = new URLClassLoader(urls);

            } else {
                cl = this.getClass().getClassLoader();
            }

            Driver driver = (Driver)cl.loadClass(driverClassName).newInstance();

            Properties info = new Properties();

            if (user != null) {
                info.put("user", user);
            }
            if (password != null) {
                info.put("password", password);
            }

            conn = driver.connect(url, info);

            loadTablesList();

        } catch (MalformedURLException ex) {
            showError("Jar driver path is incorrect. "
                      + "\nNOTE: If you what to set more then one file, "
                      + "\nsplit them with an comma \";\" character.\n"
                      + "DETAILS: " + ex.getMessage());
        } catch (SQLException ex) {
            showError(ex.getMessage());
        } catch (ClassNotFoundException ex) {
            showError("Driver not found : " + ex.getMessage());
        } catch (InstantiationException ex) {
            showError(ex.getMessage());
        } catch (IllegalAccessException ex) {
            showError(ex.getMessage());
        }
    }

    /**
     * Load the table list and show them in the table
     * @throws Exception
     */
    private void loadTablesList() throws SQLException {
        listColumns.setListData(new Object[] {});
        List tablesList = loadTablesName();
        listTable.setListData(tablesList.toArray());
        scrollPaneListTable.setVisible(true);
    }

    /**
     * Load the columns list of the given table
     * @param tableName String
     */
    private void loadColumnsList(String tableName) {
        columnsName = loadColumnsName(tableName);
        listColumns.setListData(columnsName.toArray());
        scrollPaneListColumns.setVisible(true);
    }

    /**
     * Show the file chooser (to choose the jar driver)
     */
    private void showFileChooser() {

        int response = fileChooser.showOpenDialog(this);
        if (response == JFileChooser.APPROVE_OPTION) {

            File[] files = fileChooser.getSelectedFiles();
            String jars = null;
            try {
                jarValue.setText(files[0].toURL().toString());
                for (int i = 1; i < files.length; i++) {
                    jars = jarValue.getText()
                           + FILE_SPLITER + "  "
                           + files[i].toURL().toString();
                    jarValue.setText(jars);
                }

            } catch (MalformedURLException ex) {
                showError("Error choosing the jar file: " + ex.getMessage());
            }
        }
    }

    /**
     * Show the given error message
     * @param msg String
     */
    private void showError(String msg) {
        JOptionPane.showMessageDialog(null, msg, "Error",
                                      JOptionPane.ERROR_MESSAGE);
    }

    /**
     * Load the tables list
     * @return the list of the table.
     * @throws Exception
     */
    private List loadTablesName() throws SQLException {

        if (conn == null) {
            throw new IllegalStateException("The connection is null");
        }

        DatabaseMetaData dbMetaData = conn.getMetaData();
        ResultSet rs                = dbMetaData.getTables(null, null, null, new String[]{"TABLE","VIEW"});
        List tablesName             = new ArrayList();

        while (rs.next()) {
            String name    = rs.getString("TABLE_NAME");
            String type    = rs.getString("TABLE_TYPE");

            if ("TABLE".equalsIgnoreCase(type) || "VIEW".equalsIgnoreCase(type)) {
                tablesName.add(name);
            }
        }

        return tablesName;
    }

    /**
     * Load the columns name of the given table
     * @param tableName the table name
     * @return the list of the columns
     */
    private Vector loadColumnsName(String tableName) {

        Vector columnsName = new Vector();

        try {
            String query = "SELECT * FROM " + tableName;

            Statement stmt               = conn.createStatement();
            ResultSet rs                 = stmt.executeQuery(query);
            ResultSetMetaData rsMetaData = rs.getMetaData();

            int numCol = rsMetaData.getColumnCount();

            for (int i = 1; i <= numCol; i++) {
                String columnName = rsMetaData.getColumnName(i);

                columnsName.add(columnName);
            }
        } catch (SQLException ex) {
            showError(ex.getMessage());
        }
        return columnsName;
    }

    private String[] splitText(String mensagem) {

        Pattern regex = Pattern.compile(FILE_SPLITER, Pattern.UNICODE_CASE);
        String[] splitedText = regex.split(mensagem);
        for (int i = 0; i < splitedText.length; i++) {
            splitedText[i] = splitedText[i].trim();
        }
        return splitedText;
    }

}

/**
 * This is the file filter used to show only jar file
 */
class SimpleFileFilter extends FileFilter {

    private String extension   = null;
    private String description = null;

    public SimpleFileFilter(String extension, String description) {
        if (extension == null) {
            throw new IllegalStateException("Null value not permitted");
        }
        this.extension   = "." + extension;
        this.description = description;
    }

    public boolean accept(File file) {
        if (file.isDirectory()) {
            return true;
        }
        String fileName = file.getName();
        if (fileName.toUpperCase().endsWith(extension.toUpperCase())) {
            return true;
        }
        return false;
    }

    public String getDescription() {
        return description;
    }

}
