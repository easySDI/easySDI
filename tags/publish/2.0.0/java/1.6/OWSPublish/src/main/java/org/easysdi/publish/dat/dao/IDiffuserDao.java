package org.easysdi.publish.dat.dao;

import java.util.List;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.database.GeodatabaseType;
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.diffuser.DiffuserType;


/**
 * Provides action persistance operations.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public interface IDiffuserDao {

    /**
     * Erases an action.
     * 
     * @param   action  the action
     * @return          <code>true</code> if the action was successfully deleted
     */
    void delete(Diffuser diff);



    /**
     * Gets an action type object from its name.
     * 
     * @param   typeName    the name of the action type
     * @return              the action type object if it exists, or<br>
     *                      <code>null</code> otherwise
     */
    DiffuserType getType(String typeName);


    Diffuser getDiffuserFromIdString(String identifyString);
    

    /**
     * Saves an action.
     * 
     * @param   action  the action
     * @return          <code>true</code> if the action was successfully 
     *                  persisted
     */
    void persist(Diffuser diff);

    List<Diffuser> getAllDiffusers();
    
    List<DiffuserType> getAllDiffuserTypes();

    
}
