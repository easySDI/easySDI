package org.easysdi.monitor.gui.webapp.controllers;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.monitor.biz.job.Job;
import org.easysdi.monitor.gui.webapp.MonitorInterfaceException;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.servlet.ModelAndView;

/**
 * Processes the requests concerning the alerts collection for a job.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
@Controller
@RequestMapping({ "/jobs/{idString}/alerts", "/adminJobs/{idString}/alerts" })
public class AlertsCollectionController extends AbstractMonitorController {
    
    /**
     * Creates a new controller.
     */
    public AlertsCollectionController() {
        
    }
    
    

    /**
     * Show the alerts for a given job.
     * 
     * @param   request                     the request that asked for 
     *                                      displaying the alerts
     * @param   response                    the response to the request
     * @param   idString                    the string that identifies the
     *                                      parent job. It can contain its
     *                                      identifier or its name
     * @return                              an object containing the alerts
     *                                      collection and which view must be
     *                                      used to display it
     * @throws  MonitorInterfaceException   <ul>
     *                                      <li>the job doesn't exist</li>
     *                                      <li>the job isn't public and the
     *                                      user hasn't sufficient rights to
     *                                      view its alerts</li>
     *                                      </ul>
     */
    @RequestMapping(method = RequestMethod.GET)
    public ModelAndView show(HttpServletRequest request,
                             HttpServletResponse response, 
                             @PathVariable String idString)
        throws MonitorInterfaceException {
        
        final ModelAndView result = new ModelAndView("alertsCollectionJson");
        final Job job = this.getJobProtected(idString, request, response);
        final String altValue = request.getParameter("alt");
        boolean onlyRss = false;

        if (null != altValue && altValue.equals("rss")) {
            result.setViewName("alertsCollectionRss");
            result.addObject("jobId", job.getJobId());
            onlyRss = true;
        }

        result.addObject("message", "alertsCollection.details.success");
        result.addObject("alertsCollection", job.getAlerts(onlyRss));

        return result;
    }
}
