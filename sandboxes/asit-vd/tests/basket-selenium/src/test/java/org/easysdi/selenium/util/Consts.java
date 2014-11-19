/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package org.easysdi.selenium.util;

/**
 *
 * @author xnfybi
 */
public class Consts {

    public static final String SHOP_COMPONENT = "easysdi_shop";
    public static final String CATALOG_COMPONENT = "easysdi_catalog";

    private Consts() {
    //this prevents even the native class from 
        //calling this ctor as well :
        throw new AssertionError();
    }

}
