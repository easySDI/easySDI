/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

package org.easysdi.proxy.core;

import java.util.Collection;
import java.util.List;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;

import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.policy.PolicySet;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;


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

    public Policy getPolicy (String user, HttpServletRequest req)
    {

    	if (user !=null)
	    {
			/*
			 * Find the first policy where the user or the role is matching
			 */	
			for (int i=0 ; i<policyList.size();i++)
			{
			    Policy p = policyList.get(i);
			    
			    if(p.getSubjects()==null)
			    {
			    	continue;
			    }
			    	    
			    /*If one user is matching, then we can return the policy*/
			    List<String> userList = p.getSubjects().getUser();
		    	for (int j =0;j<userList.size();j++)
		    	{
		    		if (userList.get(j).equals(user))
		    		{
		    			return p;
		    		}
		    	}
			}
			  
			/*If the user is not matching , then try if  a role is matching then returns the policy*/
			Authentication  principal = SecurityContextHolder.getContext().getAuthentication();
			if ((principal == null)) {
			 return null;
	        }
	        Collection<GrantedAuthority> authorities = (Collection<GrantedAuthority>)principal.getAuthorities();
	        if (authorities != null) 
	        {
	        	for (int i=0 ; i<policyList.size();i++)
				{
				    Policy p = policyList.get(i);
				    if(p.getSubjects()==null)
				    {
				    	continue;
				    }
				    
				   List<String> roleList = p.getSubjects().getRole();
		    		for (int j =0;j<roleList.size();j++)
		    		{		 
		    			for (GrantedAuthority grantedAuthority : authorities) {
		    	            if (roleList.get(j).equals(grantedAuthority.getAuthority())) {
		    	            	return p;
		    	            }
		    	        }
		    		}
			    }
		    }
	    }
	
		/*If the user is not matching and the role is not matching either, we try to return the first policy with a 'subjects' attribute 'all' set*/
		for (int i=0 ; i<policyList.size();i++)
		{
			Policy p = policyList.get(i);
		    
		    if(p.getSubjects()==null)
		    {
		    	continue;
		    }
			 
		    if (p.getSubjects().isAll()) 
		    {					
		    	return p;
		    }
		}

		/*We found neither user nor role matching the rule 
		 * Then it returns null 
		 */ 
	
		return null;
    }




}
