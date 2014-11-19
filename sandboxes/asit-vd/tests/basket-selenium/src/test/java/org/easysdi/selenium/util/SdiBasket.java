/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package org.easysdi.selenium.util;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 *
 * @author xnfybi
 */
public class SdiBasket {

    public SdiBasket(String name, String surface) {
        this.name = name;
        this.surface = surface;
        this.metadatas = new ArrayList<SdiMetadata>();
        this.thridparty = null;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getSurface() {
        return surface;
    }

    public void setSurface(String surface) {
        this.surface = surface;
    }
    private String name;
    public List<SdiMetadata> metadatas;

    public SdiOrganism getThridparty() {
        return thridparty;
    }

    public void setThridparty(SdiOrganism thridparty) {
        this.thridparty = thridparty;
    }
    private String totalPrice;
    private String surface;
    private SdiOrganism thridparty;
    
}
