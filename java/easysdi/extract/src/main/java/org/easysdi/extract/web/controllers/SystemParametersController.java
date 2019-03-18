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
package org.easysdi.extract.web.controllers;

import javax.validation.Valid;
import org.easysdi.extract.domain.SystemParameter;
import org.easysdi.extract.orchestrator.Orchestrator;
import org.easysdi.extract.persistence.SystemParametersRepository;
import org.easysdi.extract.web.Message.MessageType;
import org.easysdi.extract.web.model.SystemParameterModel;
import org.easysdi.extract.web.validators.SystemParameterValidator;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.WebDataBinder;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.InitBinder;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;



/**
 * Web controller that processes requests related to the management of the application users.
 *
 * @author Florent Krin
 */
@Controller
@Scope("session")
@RequestMapping("/parameters")
public class SystemParametersController extends BaseController {

    /**
     * The string that identifies the part of the website that this controller manages.
     */
    private static final String CURRENT_SECTION_IDENTIFIER = "parameters";

    /**
     * The string that identifies the view to display the information about parameters.
     */
    private static final String VIEW_DETAILS = "parameters/details";

    /**
     * The string that tells this controller to redirect the user to the view that shows all the users.
     */
    private static final String REDIRECT_TO_VIEW = "redirect:/parameters";

    /**
     * The parameter value that indicates that the e-mail notifications are disabled.
     */
    private static final String MAIL_ENABLE_OFF_STRING = "false";

    /**
     * The parameter value that indicates that the e-mail notifications are enabled.
     */
    private static final String MAIL_ENABLE_ON_STRING = "true";

    /**
     * The writer to the application logs.
     */
    private final Logger logger = LoggerFactory.getLogger(SystemParametersController.class);

    /**
     * The Spring Data repository that links the user data objects to the data source.
     */
    @Autowired
    private SystemParametersRepository systemParametersRepository;



    /**
     * Defines the links between form data and Java objects.
     *
     * @param binder the object that makes the link between web forms data and Java beans
     */
    @InitBinder("parameters")
    public final void initBinder(final WebDataBinder binder) {
        binder.setValidator(new SystemParameterValidator(this.systemParametersRepository));
    }



    /**
     * Make the modifications to the application settings permanent.
     *
     * @param parameterModel     the model that contains the modifications of the application settings
     * @param bindingResult      the validation results for the modified settings
     * @param model              the data to display in the view
     * @param redirectAttributes the data to pass to the page that the user may be redirected to
     * @return the string that identifies the view to display
     */
    @PostMapping()
    public final String updateParameters(
            @Valid @ModelAttribute("parameters") final SystemParameterModel parameterModel,
            final BindingResult bindingResult, final ModelMap model, final RedirectAttributes redirectAttributes) {

        this.logger.debug("Processing the data to update parameters.");

        if (!this.isCurrentUserAdmin()) {
            return SystemParametersController.REDIRECT_TO_ACCESS_DENIED;
        }

        String[] keys = new String[]{SystemParametersRepository.BASE_PATH_KEY,
            SystemParametersRepository.DASHBOARD_INTERVAL_KEY, SystemParametersRepository.SCHEDULER_FREQUENCY_KEY,
            SystemParametersRepository.SMTP_FROM_MAIL_KEY, SystemParametersRepository.SMTP_FROM_NAME_KEY,
            SystemParametersRepository.SMTP_PASSWORD_KEY, SystemParametersRepository.SMTP_PORT_KEY,
            SystemParametersRepository.SMTP_SERVER_KEY, SystemParametersRepository.SMTP_USER_KEY,
            SystemParametersRepository.SMTP_SSL_KEY, SystemParametersRepository.ENABLE_MAIL_NOTIFICATIONS};

        if (bindingResult.hasErrors()) {
            this.logger.info("Updating the system parameters failed because of invalid data.");

            return this.prepareModelForDetailsView(model);
        }

        boolean success = true;
        String currentKey = keys[0];
        try {
            for (String key : keys) {

                currentKey = key;
                this.logger.debug("Fetching the parameter {} to update.", key);
                SystemParameter systemParameter = this.systemParametersRepository.findByKey(key);
                if (!this.systemParametersRepository.existsByKey(key)) {
                    systemParameter = parameterModel.createDomainObject(key);
                }

                switch (key) {

                    case SystemParametersRepository.BASE_PATH_KEY:
                        systemParameter.setValue(parameterModel.getBasePath());
                        break;

                    case SystemParametersRepository.DASHBOARD_INTERVAL_KEY:
                        systemParameter.setValue(parameterModel.getDashboardFrequency());
                        break;

                    case SystemParametersRepository.SCHEDULER_FREQUENCY_KEY:
                        systemParameter.setValue(parameterModel.getSchedulerFrequency());
                        break;

                    case SystemParametersRepository.SMTP_FROM_MAIL_KEY:
                        systemParameter.setValue(parameterModel.getSmtpFromMail());
                        break;

                    case SystemParametersRepository.SMTP_FROM_NAME_KEY:
                        systemParameter.setValue(parameterModel.getSmtpFromName());
                        break;

                    case SystemParametersRepository.SMTP_PASSWORD_KEY:

                        if (!parameterModel.isPasswordGenericString()) {
                            systemParameter.setValue(parameterModel.getSmtpPassword());
                        }
                        break;

                    case SystemParametersRepository.SMTP_PORT_KEY:
                        systemParameter.setValue(parameterModel.getSmtpPort());
                        break;

                    case SystemParametersRepository.SMTP_SERVER_KEY:
                        systemParameter.setValue(parameterModel.getSmtpServer());
                        break;

                    case SystemParametersRepository.SMTP_USER_KEY:
                        systemParameter.setValue(parameterModel.getSmtpUser());
                        break;

                    case SystemParametersRepository.SMTP_SSL_KEY:
                        systemParameter.setValue(parameterModel.getSslType().name());
                        break;

                    case SystemParametersRepository.ENABLE_MAIL_NOTIFICATIONS:
                        final String mailEnabledValue = (parameterModel.isMailEnabled())
                                ? SystemParametersController.MAIL_ENABLE_ON_STRING
                                : SystemParametersController.MAIL_ENABLE_OFF_STRING;
                        systemParameter.setValue(mailEnabledValue);
                        break;

                    default:
                        throw new Exception(String.format("Unsupported application setting : %s", key));
                }

                this.systemParametersRepository.save(systemParameter);
            }

        } catch (Exception exception) {
            this.logger.error("Could not update parameter with key {}.", currentKey, exception);
            success = false;
        }

        if (!success) {
            this.addStatusMessage(model, "parameters.errors.update.failed", MessageType.ERROR);
            return SystemParametersController.REDIRECT_TO_VIEW;

        }

        this.refreshOrchestratorFrequency();

        this.logger.info("Updating the parameters has succeeded.");
        this.addStatusMessage(redirectAttributes, "parameters.updated", MessageType.SUCCESS);

        return SystemParametersController.REDIRECT_TO_VIEW;
    }



    /**
     * Processes a request to display all the application users.
     *
     * @param model the data to display in the next view
     * @return the string that identifies the next view to display
     */
    @GetMapping
    public final String viewPage(final ModelMap model) {

        if (!this.isCurrentUserAdmin()) {
            return SystemParametersController.REDIRECT_TO_ACCESS_DENIED;
        }

        SystemParameterModel systemParameterModel = new SystemParameterModel();
        systemParameterModel.setBasePath(systemParametersRepository.getBasePath());
        systemParameterModel.setDashboardFrequency(systemParametersRepository.getDashboardRefreshInterval());
        systemParameterModel.setSchedulerFrequency(systemParametersRepository.getSchedulerFrequency());
        systemParameterModel.setSmtpFromMail(systemParametersRepository.getSmtpFromMail());
        systemParameterModel.setSmtpFromName(systemParametersRepository.getSmtpFromName());
        systemParameterModel.setSmtpPassword(SystemParameterModel.PASSWORD_GENERIC_STRING);
        systemParameterModel.setSmtpPort(systemParametersRepository.getSmtpPort());
        systemParameterModel.setSmtpServer(systemParametersRepository.getSmtpServer());
        systemParameterModel.setSmtpUser(systemParametersRepository.getSmtpUser());
        systemParameterModel.setSslType(systemParametersRepository.getSmtpSSL());
        final String mailEnabledValue = systemParametersRepository.isEmailNotificationEnabled();
        systemParameterModel.setMailEnabled(SystemParametersController.MAIL_ENABLE_ON_STRING.equals(mailEnabledValue));

        return this.prepareModelForDetailsView(model, systemParameterModel);
    }



    /**
     * Defines the generic model attributes that ensure a proper display of the system parameters details
     * view. <b>Important:</b> This method does not set the attribute for the parameters themselves. This must be set
     * separately, if necessary.
     *
     * @param model the data to display in the view
     * @return the string that identifies the details view
     */
    private String prepareModelForDetailsView(final ModelMap model) {
        return this.prepareModelForDetailsView(model, null);
    }



    /**
     * Defines the generic model attributes that ensure a proper display of the system parameters details
     * view.
     *
     * @param model          the data to display in the view
     * @param parameterModel the model that represents the system parameters to display in the details view, or
     *                       <code>null</code> not to define any parameters specifically (because it is set elsewhere,
     *                       for example)
     * @return the string that identifies the details view
     */
    private String prepareModelForDetailsView(final ModelMap model, final SystemParameterModel parameterModel) {
        assert model != null : "The model must be set.";

        this.addJavascriptMessagesAttribute(model);
        this.addCurrentSectionToModel(SystemParametersController.CURRENT_SECTION_IDENTIFIER, model);

        if (parameterModel != null) {
            model.addAttribute("parameters", parameterModel);
        }

        return SystemParametersController.VIEW_DETAILS;
    }



    /**
     * Informs the background tasks orchestrator that system parameters may have been updated.
     */
    private void refreshOrchestratorFrequency() {
        Orchestrator orchestrator = Orchestrator.getInstance();

        if (!orchestrator.isInitialized()) {
            this.logger.warn("The orchestrator is not initialized. The frequency will not be updated. Please check if"
                    + " there were errors when application was started.");
            return;
        }

        orchestrator.updateStepFrequencyFromDataSource(true);
    }

}
