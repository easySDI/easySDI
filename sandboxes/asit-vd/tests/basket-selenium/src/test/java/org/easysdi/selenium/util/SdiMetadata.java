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
public class SdiMetadata {

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getIdentifier() {
        return identifier;
    }

    public void setIdentifier(String identifier) {
        this.identifier = identifier;
    }

    public SdiMetadata(String name, String identifier) {
        this.name = name;
        this.identifier = identifier;
    }
    private String name;
    private String identifier;
    
}
