/*
 * Copyright (C) 2017 arx iT
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
package org.easysdi.extract.web.model;

import java.util.Arrays;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.HashMap;
import org.apache.commons.collections.ListUtils;
import org.easysdi.extract.connectors.common.IConnector;
import org.easysdi.extract.domain.Connector;
import org.easysdi.extract.domain.Request;
import org.easysdi.extract.domain.Rule;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Assert;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;



/**
 *
 * @author Yves Grasset
 */
public class ConnectorModelTest {
    private final static String DEFAULT_IMPORT_MESSAGE = "Dummy connector, could not import";

    private final static Calendar DEFAULT_UPDATE_DATE = new GregorianCalendar(2015, 4, 20, 16, 53, 25);

    private final static int DUMMY_CONNECTOR_INSTANCE_ID = 5;

    private final static int DUMMY_RULE_INSTANCE_ID = 78;

    private Connector dummyConnectorInstance;
    private IConnector dummyConnectorPlugin;
    private RuleModel dummyRuleModel;



    public ConnectorModelTest() {
    }



    @BeforeClass
    public static void setUpClass() {
    }



    @AfterClass
    public static void tearDownClass() {
    }



    @Before
    public void setUp() {
        this.dummyConnectorPlugin = new DummyConnectorPlugin();

        this.dummyConnectorInstance = new Connector();
        this.dummyConnectorInstance.setActive(Boolean.TRUE);
        this.dummyConnectorInstance.setConnectorCode(this.dummyConnectorPlugin.getCode());
        this.dummyConnectorInstance.setConnectorLabel(this.dummyConnectorPlugin.getLabel());
        this.dummyConnectorInstance.setId(ConnectorModelTest.DUMMY_CONNECTOR_INSTANCE_ID);
        this.dummyConnectorInstance.setImportFrequency(120);
        this.dummyConnectorInstance.setLastImportDate(ConnectorModelTest.DEFAULT_UPDATE_DATE);
        this.dummyConnectorInstance.setLastImportMessage(ConnectorModelTest.DEFAULT_IMPORT_MESSAGE);
        this.dummyConnectorInstance.setName("Test connector model");
        this.dummyConnectorInstance.setRequestsCollection(ListUtils.EMPTY_LIST);

        HashMap<String, String> parametersValues = new HashMap<>();
        parametersValues.put("url", "http://titi.toto.ta");
        parametersValues.put("login", "myUser");
        parametersValues.put("password", "mYSuPeR5eCr3tPa2sW0Rd");
        this.dummyConnectorInstance.setConnectorParametersValues(parametersValues);

        Rule dummyRule = new Rule();
        dummyRule.setId(ConnectorModelTest.DUMMY_RULE_INSTANCE_ID);
        dummyRule.setActive(Boolean.TRUE);
        dummyRule.setConnector(this.dummyConnectorInstance);
        dummyRule.setPosition(1);
        dummyRule.setProcess(new org.easysdi.extract.domain.Process(25));
        dummyRule.setRule("product == \"SHP\"");

        this.dummyConnectorInstance.setRulesCollection(Arrays.asList(dummyRule));

        this.dummyRuleModel = new RuleModel();
        this.dummyRuleModel.setActive(true);
        this.dummyRuleModel.setId(ConnectorModelTest.DUMMY_CONNECTOR_INSTANCE_ID);
        this.dummyRuleModel.setProcessId(8);
        this.dummyRuleModel.setPosition(2);
        this.dummyRuleModel.setProcessName("Traitement test");
        this.dummyRuleModel.setRule("id > 0");
    }



    @After
    public void tearDown() {
    }



    /**
     * Test of hasActiveRequests method, of class ConnectorModel.
     */
    @Test
    public void testHasActiveRequests() {
        System.out.println("hasActiveRequests");
        Request request1 = new Request();
        request1.setStatus(Request.Status.FINISHED);
        request1.setConnector(this.dummyConnectorInstance);

        Request request2 = new Request();
        request2.setStatus(Request.Status.STANDBY);
        request2.setConnector(this.dummyConnectorInstance);

        this.dummyConnectorInstance.setRequestsCollection(Arrays.asList(request1, request2));
        ConnectorModel instance = new ConnectorModel(this.dummyConnectorPlugin, this.dummyConnectorInstance, null);
        Assert.assertEquals(true, instance.hasActiveRequests());
    }



    /**
     * Test of createDomainConnector method, of class ConnectorModel.
     */
    @Test
    public void testCreateDomainConnector() {
        System.out.println("createDomainConnector");

        ConnectorModel instance = new ConnectorModel(this.dummyConnectorPlugin);
        instance.setActive(this.dummyConnectorInstance.isActive());
        instance.setId(this.dummyConnectorInstance.getId());
        instance.setImportFrequency(this.dummyConnectorInstance.getImportFrequency());
        instance.setLastImportDate(this.dummyConnectorInstance.getLastImportDate());
        instance.setLastImportMessage(this.dummyConnectorInstance.getLastImportMessage());
        instance.setName(this.dummyConnectorInstance.getName());
        Connector createdInstance = instance.createDomainConnector();

        Assert.assertEquals(this.dummyConnectorInstance.isActive(), createdInstance.isActive());
        Assert.assertEquals(this.dummyConnectorPlugin.getCode(), createdInstance.getConnectorCode());
        Assert.assertEquals(this.dummyConnectorPlugin.getLabel(), createdInstance.getConnectorLabel());
        Assert.assertNull(createdInstance.getId());
        Assert.assertEquals(this.dummyConnectorInstance.getImportFrequency(), createdInstance.getImportFrequency());
        Assert.assertNull(createdInstance.getLastImportDate());
        Assert.assertNull(createdInstance.getLastImportMessage());
        Assert.assertEquals(this.dummyConnectorInstance.getName(), createdInstance.getName());
    }



    /**
     * Test of updateDomainConnector method, of class ConnectorModel.
     */
    @Test
    public void testUpdateDomainConnector() {
        System.out.println("updateDomainConnector");
        Calendar newImportDate = new GregorianCalendar(2017, 1, 25, 12, 33, 54);
        ConnectorModel instance = new ConnectorModel(this.dummyConnectorPlugin, this.dummyConnectorInstance, null);
        instance.setActive(false);
        instance.setId(8);
        instance.setImportFrequency(360);
        instance.setLastImportDate(newImportDate);
        instance.setLastImportMessage("OK");
        instance.setName("New test connector");
        instance.updateDomainConnector(this.dummyConnectorInstance);
        Assert.assertEquals(false, this.dummyConnectorInstance.isActive());
        Assert.assertEquals((long) ConnectorModelTest.DUMMY_CONNECTOR_INSTANCE_ID, (long) this.dummyConnectorInstance.getId());
        Assert.assertEquals("New test connector", this.dummyConnectorInstance.getName());

        // The following properties should not be updated even if they have been changed in the model
        Assert.assertEquals(this.dummyConnectorPlugin.getCode(), this.dummyConnectorInstance.getConnectorCode());
        Assert.assertEquals(this.dummyConnectorPlugin.getLabel(), this.dummyConnectorInstance.getConnectorLabel());
        Assert.assertEquals((long) ConnectorModelTest.DUMMY_CONNECTOR_INSTANCE_ID,
                (long) this.dummyConnectorInstance.getId());
        Assert.assertEquals(ConnectorModelTest.DEFAULT_UPDATE_DATE, this.dummyConnectorInstance.getLastImportDate());
        Assert.assertEquals(ConnectorModelTest.DEFAULT_IMPORT_MESSAGE, this.dummyConnectorInstance.getLastImportMessage());

    }



    /**
     * Test of addRule method, of class ConnectorModel.
     */
    @Test
    public void testAddRule() {
        System.out.println("addRule");
        ConnectorModel instance = new ConnectorModel(this.dummyConnectorPlugin, this.dummyConnectorInstance, null);
        instance.addRule(this.dummyRuleModel);
        Assert.assertTrue(instance.getRules().length == this.dummyConnectorInstance.getRulesCollection().size() + 1);
        RuleModel addedRule = instance.getRules()[instance.getRules().length - 1];
        Assert.assertEquals(ConnectorModelTest.DUMMY_CONNECTOR_INSTANCE_ID, addedRule.getId());
        Assert.assertEquals(this.dummyRuleModel.getProcessId(), addedRule.getProcessId());
        Assert.assertEquals(this.dummyRuleModel.getPosition(), addedRule.getPosition());
        Assert.assertEquals(this.dummyRuleModel.getProcessName(), addedRule.getProcessName());
        Assert.assertEquals(this.dummyRuleModel.getRule(), addedRule.getRule());
    }



    /**
     * Test of removeRule method, of class ConnectorModel.
     */
    @Test
    public void testRemoveRule_int() {
        System.out.println("removeRule");
        ConnectorModel instance = new ConnectorModel(this.dummyConnectorPlugin, this.dummyConnectorInstance, null);
        instance.removeRule(ConnectorModelTest.DUMMY_RULE_INSTANCE_ID);
        Assert.assertTrue(instance.getRules().length == this.dummyConnectorInstance.getRulesCollection().size() - 1);
        Assert.assertNull(instance.getRuleById(ConnectorModelTest.DUMMY_RULE_INSTANCE_ID));
    }



    /**
     * Test of removeRule method, of class ConnectorModel.
     */
    @Test
    public void testRemoveRule_RuleModel() {
        System.out.println("removeRule");
        ConnectorModel instance = new ConnectorModel(this.dummyConnectorPlugin, this.dummyConnectorInstance, null);
        instance.addRule(this.dummyRuleModel);
        instance.removeRule(this.dummyRuleModel);
        Assert.assertTrue(instance.getRules().length == this.dummyConnectorInstance.getRulesCollection().size());
        Assert.assertNull(instance.getRuleById(this.dummyRuleModel.getId()));
    }

}
