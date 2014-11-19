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
public class SdiUser {

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getLogin() {
        return login;
    }

    public void setLogin(String login) {
        this.login = login;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public SdiUser(String name, String login, String password) {
        this.name = name;
        this.login = login;
        this.password = password;
    }
    private String name;
    private String login;
    private String password;
    
}
