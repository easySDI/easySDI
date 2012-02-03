/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

package ch.depth.proxy.core;

import java.util.List;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;

import ch.depth.proxy.policy.Policy;
import ch.depth.proxy.policy.PolicySet;

/**
 * @author Administrateur
 *
 */
public class PolicyHelpers {


    List<Policy> policyList = new Vector<Policy>();

    /**
     * Constructor of the Policy helpers.
     *  
     * @param ps PolicySet
     * @param configId the id of the current config
     */
    public PolicyHelpers(PolicySet ps,String configId) {
	super();
	List<Policy> tempPolicyList = ps.getPolicy();
	for (int i=0 ; i<tempPolicyList.size();i++){
	    Policy p = tempPolicyList.get(i);
	    if (p.getConfigId()!=null){
	    if (p.getConfigId().equals(configId)){
		policyList.add(p);
	    }
	    }
	}
    }
    /***
     * returns the Policy
     * @param user the user. May be null
     * @param req the request to find which user belong to wich role. May be null 
     * @return the policy
     */

    public Policy getPolicy (String user, HttpServletRequest req){

	/*
	 * Find the first policy where the user or the role is matching
	 */	
	for (int i=0 ; i<policyList.size();i++){
	    Policy p = policyList.get(i);

	    
	    if(p.getSubjects()==null){
		return null;
	    }
	    /*If attribute All is true, then we can returns the operations*/ 
	    if (p.getSubjects().isAll()) {					
		return p;
	    }
	    
	    /*If one user is matching, then we can returns the operations*/
	    if (user !=null){
		List<String> userList = p.getSubjects().getUser();
		for (int j =0;j<userList.size();j++){
		    if (userList.get(j).equals(user)){
			return p;
		    }
		}
	    }
	    /*If the user is not matching , then try if  a role is matching then returns the operations*/
	    if (user !=null){
		if (req !=null){
		    List<String> roleList = p.getSubjects().getRole();
		    for (int j =0;j<roleList.size();j++){		    
			if (req.isUserInRole(roleList.get(j))){
			    return p;
			}
		    }
		}	    
	    }
	}
	
	 

	/*We found neither user nor role matching the rule 
	 * Then it returns null 
	 */ 

	return null;
    }




}
