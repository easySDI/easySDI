package org.easysdi.publish.dat.dao;

import java.util.List;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.database.GeodatabaseType;
import org.easysdi.publish.biz.diffuser.DiffuserType;
import org.springframework.dao.DataAccessException;


/**
 * Provides action persistance operations.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public interface IGeodatabaseDao {

    /**
     * Erases an action.
     * 
     * @param   action  the action
     * @return          <code>true</code> if the action was successfully deleted
     */
    void delete(Geodatabase geodb) throws DataAccessException;



    /**
     * Gets an action type object from its name.
     * 
     * @param   typeName    the name of the action type
     * @return              the action type object if it exists, or<br>
     *                      <code>null</code> otherwise
     */
    GeodatabaseType getType(long id);


    Geodatabase getGeodatabaseFromIdString(String identifyString);
    

    /**
     * Saves an action.
     * 
     * @param   action  the action
     * @return          <code>true</code> if the action was successfully 
     *                  persisted
     */
    void persist(Geodatabase geodb) throws DataAccessException;
    
    List<GeodatabaseType> getAllGeodatabaseTypes();

}
