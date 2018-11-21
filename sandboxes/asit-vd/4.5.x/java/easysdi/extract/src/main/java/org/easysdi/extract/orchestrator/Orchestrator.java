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
package org.easysdi.extract.orchestrator;

import org.apache.commons.lang3.StringUtils;
import org.easysdi.extract.connectors.ConnectorDiscovererWrapper;
import org.easysdi.extract.email.EmailSettings;
import org.easysdi.extract.orchestrator.schedulers.ImportJobsScheduler;
import org.easysdi.extract.orchestrator.schedulers.RequestsProcessingScheduler;
import org.easysdi.extract.persistence.ApplicationRepositories;
import org.easysdi.extract.persistence.SystemParametersRepository;
import org.easysdi.extract.plugins.TaskProcessorDiscovererWrapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.scheduling.config.ScheduledTaskRegistrar;



/**
 * An object that manages the scheduling of all the background tasks of the application.
 *
 * @author Yves Grasset
 */
public final class Orchestrator {

    /**
     * The instantiated orchestrator object.
     */
    private static final Orchestrator INSTANCE = new Orchestrator();

    /**
     * The locale of the language that the application displays messages in.
     */
    private String applicationLangague;

    /**
     * Whether the background tasks related to the connectors are defined.
     */
    private boolean connectorsMonitoringScheduled = false;

    /**
     * The access to the available connector plugins.
     */
    private ConnectorDiscovererWrapper connectorPlugins;

    /**
     * The objects required to create and send e-mail messages.
     */
    private EmailSettings emailSettings;

    /**
     * The objects that keeps track of the planified connector background tasks.
     */
    private ImportJobsScheduler importsScheduler;

    /**
     * The writer to the application logs.
     */
    private final Logger logger = LoggerFactory.getLogger(Orchestrator.class);

    /**
     * The Spring Data objects that link the application data objects with the data source.
     */
    private ApplicationRepositories repositories;

    /**
     * Whether the background tasks related to the processing of the requests are defined.
     */
    private boolean requestsMonitoringScheduled = false;

    /**
     * The object that keeps track of the planifies request processing background tasks.
     */
    private RequestsProcessingScheduler requestsScheduler;

    /**
     * The number of seconds before a scheduled job is launched again.
     */
    private int stepFrequency;

    /**
     * The access to the available task processing plugins.
     */
    private TaskProcessorDiscovererWrapper taskPlugins;

    /**
     * The object that allows to execute task at a given delay.
     */
    private ScheduledTaskRegistrar taskRegistrar;



    /**
     * Creates a new INSTANCE of this orchestrator.
     */
    private Orchestrator() {
        this.logger.info("New instance of the orchestrator created. This should happen only once in the application"
                + " lifetime.");
    }



    /**
     * Gets the orchestrator object that is currently instantiated.
     *
     * @return the orchestrator
     */
    public static Orchestrator getInstance() {
        return Orchestrator.INSTANCE;
    }



    /**
     * Defines the language used by the application to display messages to the users.
     *
     * @param languageCode the locale code of the language
     */
    public void setApplicationLanguage(final String languageCode) {

        if (languageCode == null) {
            throw new IllegalArgumentException("The application language code cannot be null.");
        }

        this.applicationLangague = languageCode;
    }



    /**
     * Defines the access to the available connector plugins.
     *
     * @param connectorPluginsDiscoverer the object that finds the connector plugins
     */
    public void setConnectorPlugins(final ConnectorDiscovererWrapper connectorPluginsDiscoverer) {

        if (connectorPluginsDiscoverer == null) {
            throw new IllegalArgumentException("The connector plugins discoverer object cannot be null.");
        }

        this.connectorPlugins = connectorPluginsDiscoverer;
    }



    /**
     * Defines the objects required to create and send e-mail messages. No (re)scheduling will be done.
     *
     * @param settings the e-mail settings object
     */
    public void setEmailSettings(final EmailSettings settings) {

        if (settings == null) {
            throw new IllegalArgumentException("The e-mail settings object cannot be null.");
        }

        this.emailSettings = settings;
    }



    /**
     * Defines the object required to execute tasks at a given frequency. No (re)scheduling will be done.
     *
     * @param registrar the task registrar
     */
    public void setTaskRegistrar(final ScheduledTaskRegistrar registrar) {

        if (registrar == null) {
            throw new IllegalArgumentException("The scheduled task registrar cannot be null.");
        }

        this.taskRegistrar = registrar;
    }



    /**
     * Defines the links between the various data objects and the data source. No (re)scheduling will be done.
     *
     * @param applicationRepositories the object that assembles the repositories
     */
    public void setRepositories(final ApplicationRepositories applicationRepositories) {

        if (applicationRepositories == null) {
            throw new IllegalArgumentException("The application repositories object cannot be null.");
        }

        this.repositories = applicationRepositories;
    }



    /**
     * Defines the access to the available task processing plugins. No (re)scheduling will be done.
     *
     * @param taskPluginsDiscoverer the object that finds the task plugins
     */
    public void setTaskPlugins(final TaskProcessorDiscovererWrapper taskPluginsDiscoverer) {

        if (taskPluginsDiscoverer == null) {
            throw new IllegalArgumentException("The task plugins discoverer object cannot be null.");
        }

        this.taskPlugins = taskPluginsDiscoverer;
    }



    /**
     * Defines a new delay before background jobs are launched again. No rescheduling will be done.
     *
     * @param seconds the number of seconds to wait between two executions of a background job
     */
    public void setStepFrequency(final int seconds) {
        this.setStepFrequency(seconds, false);
    }



    /**
     * Defines a new delay before background jobs are launched again.
     *
     * @param seconds        the number of seconds to wait between two executions of a background job
     * @param rescheduleJobs <code>true</code> to stop and recreate the background tasks if the frequency is changed.
     *                       If the new value is the same as the existing one, no rescheduling will be done regardless
     *                       of this parameter. Note that if this parameter is set to <code>true</code>, then the
     *                       orchestrator must be properly initialized
     */
    public synchronized void setStepFrequency(final int seconds, final boolean rescheduleJobs) {

        if (seconds < 1) {
            throw new IllegalArgumentException("The number of seconds must be greater that 0.");
        }

        if (seconds != this.stepFrequency) {
            this.stepFrequency = seconds;
            this.logger.info("The orchestrator step frequency is now set to {} second{}.", seconds,
                    (seconds > 1) ? "s" : "");

            if (rescheduleJobs) {
                this.rescheduleMonitoring();
            }
        } else {
            this.logger.debug("The step frequency is unchanged ({} second{}).", seconds, (seconds > 1) ? "s" : "");
        }
    }



    /**
     * Redefines the background tasks execution frequency with the value defined in the data source.
     *
     * @param rescheduleJobs <code>true</code> <code>true</code> to stop and recreate the background tasks if the
     *                       frequency is changed. If the new value is the same as the existing one, no rescheduling
     *                       will be done regardless of this parameter. Note that if this parameter is set to
     *                       <code>true</code>, then the orchestrator must be properly initialized
     */
    public synchronized void updateStepFrequencyFromDataSource(final boolean rescheduleJobs) {
        this.logger.debug("Updating the step frequency with the value in the data source.");

        if (this.repositories == null) {
            throw new IllegalStateException("The application repositories are not configured.");
        }

        final SystemParametersRepository repository = this.repositories.getParametersRepository();

        if (repository == null) {
            throw new IllegalStateException("The system parameters repository is not set.");
        }

        this.setStepFrequency(Integer.valueOf(repository.getSchedulerFrequency()), rescheduleJobs);
    }



    /**
     * Obtains if this orchestrator is in a state that allows it to schedule jobs.
     *
     * @return <code>true</code> if this orchestrator is correctly initialized
     */
    public boolean isInitialized() {

        return /*this.jobSchedulerComponents != null*/ this.taskRegistrar != null && this.repositories != null
                && this.connectorPlugins != null
                && this.taskPlugins != null && this.emailSettings != null && this.stepFrequency > 0
                && StringUtils.isNotBlank(this.applicationLangague);
    }



    /**
     * Defines the objects that are required to schedule jobs. The scheduling frequency will be obtained from the
     * data source through the object provided by the <code>applicationRepositories</code> parameter.
     * No (re)scheduling will be done.
     *
     * @param registrar                  the object that allows to execute tasks at a given frequency
     * @param applicationLanguage        the locale code of the language used by the application to display messages
     * @param applicationRepositories    an object that assembles the links between the data objects and the data source
     * @param connectorPluginsDiscoverer the object that gives access to the currently available connector plugins
     * @param taskPluginsDiscoverer      the object that gives access to the currently available task processing plugins
     * @param smtpSettings               the object that assembles the configuration objects required to create and send
     *                                   an e-mail message
     * @return <code>true</code> if this orchestrator is in a properly initialized state
     */
    public boolean initializeComponents(final ScheduledTaskRegistrar registrar, final String applicationLanguage,
            final ApplicationRepositories applicationRepositories,
            final ConnectorDiscovererWrapper connectorPluginsDiscoverer,
            final TaskProcessorDiscovererWrapper taskPluginsDiscoverer, final EmailSettings smtpSettings) {

        this.logger.debug("Initializing the orchestrator components.");
        this.setTaskRegistrar(registrar);
        this.setApplicationLanguage(applicationLanguage);
        this.setRepositories(applicationRepositories);
        this.setConnectorPlugins(connectorPluginsDiscoverer);
        this.setTaskPlugins(taskPluginsDiscoverer);
        this.setEmailSettings(smtpSettings);
        this.updateStepFrequencyFromDataSource(false);

        return this.isInitialized();
    }



    /**
     * Creates the application background tasks and defines at which frequency they are executed.
     * The orchestrator components must be properly initialized. (You can check with the {@link #isInitialized()}
     * method.) If the jobs are already scheduled, nothing will be done.
     *
     * @throws IllegalStateException the orchestrator components are not properly initialized
     */
    public void scheduleMonitoring() {
        this.logger.debug("Scheduling the monitoring tasks if they are not already.");

        if (!this.isInitialized()) {
            throw new IllegalStateException("The orchestrator components are not correctly initialized.");
        }

        this.scheduleConnectorsMonitoring();
        this.scheduleRequestsMonitoring();
    }



    /**
     * Prevents the ulterior execution of the background tasks.
     */
    public void unscheduleMonitoring() {
        this.logger.debug("Unscheduling the monitoring jobs.");
        this.unscheduleConnectorsMonitoring();
        this.unscheduleRequestsMonitoring();
        this.logger.info("The monitoring jobs have been unscheduled.");
    }



    /**
     * Prevents future execution of the background tasks and recreates them with the current configuration.
     * The orchestrator components must be properly initialized. (You can check with the {@link #isInitialized()}
     * method.)
     *
     * @throws IllegalStateException the orchestrator components are not properly initialized
     */
    public void rescheduleMonitoring() {
        this.logger.debug("Rescheduling the monitoring jobs.");

        if (!this.isInitialized()) {
            throw new IllegalStateException("The orchestrator components are not correctly initialized.");
        }

        this.unscheduleMonitoring();
        this.scheduleMonitoring();
    }



    /**
     * Instantiates and starts the background processes related to the connectors state.
     */
    private synchronized void scheduleConnectorsMonitoring() {
        assert this.isInitialized() : "The orchestrator components are not initialized";

        this.logger.debug("Attempting to configure the connectors monitoring task");

        if (this.isConnectorsMonitoringScheduled()) {
            this.logger.debug("The connectors monitoring tasks are already scheduled.");
            return;
        }

        this.importsScheduler = new ImportJobsScheduler(this.taskRegistrar, this.repositories, this.connectorPlugins,
                this.emailSettings, this.applicationLangague);
        this.importsScheduler.setSchedulingStep(this.stepFrequency);
        this.importsScheduler.scheduleJobs();

        this.setConnectorsMonitoringScheduled(true);
    }



    /**
     * Stops the recurrence of the background processes related to the connectors state.
     */
    private synchronized void unscheduleConnectorsMonitoring() {
        this.logger.debug("Unscheduling the connectors monitoring tasks.");

        if (!this.isConnectorsMonitoringScheduled() || this.importsScheduler == null) {
            this.logger.debug("The connectors monitoring tasks are not scheduled, so nothing done.");
            return;
        }

        this.importsScheduler.unscheduleJobs();
        this.setConnectorsMonitoringScheduled(false);
        this.logger.debug("The connectors monitoring tasks have been unscheduled.");
    }



    /**
     * Instantiates and starts the background processes related to the requests state.
     */
    private synchronized void scheduleRequestsMonitoring() {
        assert this.isInitialized() : "The orchestrator components are not initialized";

        this.logger.debug("Attempting to Configure the requests export monitoring task");

        if (this.isRequestsMonitoringScheduled()) {
            this.logger.debug("The requests monitoring tasks are already scheduled.");
            return;
        }

        this.requestsScheduler = new RequestsProcessingScheduler(/*this.jobSchedulerComponents,*/this.taskRegistrar,
                this.repositories, this.connectorPlugins, this.taskPlugins, this.emailSettings,
                this.applicationLangague);
        this.requestsScheduler.setSchedulingStep(this.stepFrequency);
        this.requestsScheduler.scheduleJobs();

        this.setRequestsMonitoringScheduled(true);
    }



    /**
     * Stops the recurrence of the background processes related to the requests state.
     */
    private synchronized void unscheduleRequestsMonitoring() {
        this.logger.debug("Unscheduling the requests monitoring tasks.");

        if (!this.isRequestsMonitoringScheduled() || this.requestsScheduler == null) {
            this.logger.debug("The requests monitoring tasks are not scheduled, so nothing done.");
            return;
        }

        this.requestsScheduler.unscheduleJobs();
        this.setRequestsMonitoringScheduled(false);
        this.logger.debug("The requests monitoring tasks have been unscheduled.");

    }



    /**
     * Obtains whether the background tasks related to the connectors are defined.
     *
     * @return <code>true</code> if the tasks have already been defined
     */
    private synchronized boolean isConnectorsMonitoringScheduled() {
        return this.connectorsMonitoringScheduled;
    }



    /**
     * Defines whether the background tasks related to the connectors are defined.
     *
     * @param isConnectorsMonitoringScheduled <code>true</code> if the tasks have already been defined
     */
    private synchronized void setConnectorsMonitoringScheduled(final boolean isConnectorsMonitoringScheduled) {
        this.connectorsMonitoringScheduled = isConnectorsMonitoringScheduled;
    }



    /**
     * Obtains whether the background tasks related to the request processing are defined.
     *
     * @return <code>true</code> if the tasks have already been defined
     */
    private synchronized boolean isRequestsMonitoringScheduled() {
        return this.requestsMonitoringScheduled;
    }



    /**
     * Defines whether the background tasks related to the request processing are defined.
     *
     * @param isRequestsMonitoringScheduled <code>true</code> if the tasks have already been defined
     */
    private synchronized void setRequestsMonitoringScheduled(final boolean isRequestsMonitoringScheduled) {
        this.requestsMonitoringScheduled = isRequestsMonitoringScheduled;
    }

}
