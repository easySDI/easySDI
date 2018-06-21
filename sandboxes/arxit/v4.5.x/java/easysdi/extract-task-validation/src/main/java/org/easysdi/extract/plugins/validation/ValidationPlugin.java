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
package org.easysdi.extract.plugins.validation;

import java.util.Map;
import org.easysdi.extract.plugins.common.ITaskProcessor;
import org.easysdi.extract.plugins.common.ITaskProcessorRequest;
import org.easysdi.extract.plugins.common.ITaskProcessorResult;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;



/**
 * A plugin that marks a data item process to require validation by an operator.
 *
 * @author Florent Krin
 */
public class ValidationPlugin implements ITaskProcessor {

    /**
     * The path to the file that holds the general settings for this plugin.
     */
    private static final String CONFIG_FILE_PATH = "plugins/validation/properties/config.properties";

    /**
     * The name of the file that holds the text explaining how to use this plugin in the language of
     * the user interface.
     */
    private static final String HELP_FILE_NAME = "validationHelp.html";

    /**
     * The writer to the application logs.
     */
    private final Logger logger = LoggerFactory.getLogger(ValidationPlugin.class);

    /**
     * The string that identifies this plugin.
     */
    private final String code = "VALIDATION";

    /**
     * The text that explains how to use this plugin in the language of the user interface.
     */
    private String help = null;

    /**
     * The CSS class of the icon to display to represent this plugin.
     */
    private final String pictoClass = "fa-eye";

    /**
     * The strings that this plugin can send to the user in the language of the user interface.
     */
    private LocalizedMessages messages;

    /**
     * The settings for the execution of this task.
     */
    private Map<String, String> inputs;

    /**
     * The general settings for this plugin.
     */
    private PluginConfiguration config;



    /**
     * Creates a new validation request plugin instance with default settings using the default language.
     */
    public ValidationPlugin() {
        this.config = new PluginConfiguration(ValidationPlugin.CONFIG_FILE_PATH);
        this.messages = new LocalizedMessages();
    }



    /**
     * Creates a new validation request plugin instance with default settings.
     *
     * @param language the string that identifies the language of the user interface
     */
    public ValidationPlugin(final String language) {
        this.config = new PluginConfiguration(ValidationPlugin.CONFIG_FILE_PATH);
        this.messages = new LocalizedMessages(language);
    }



    /**
     * Creates a new validation request plugin instance using the default language.
     *
     * @param taskSettings a map with the settings for the execution of this task
     */
    public ValidationPlugin(final Map<String, String> taskSettings) {
        this();
        this.inputs = taskSettings;
    }



    /**
     * Creates a new validation request plugin instance.
     *
     * @param language     the string that identifies the language of the user interface
     * @param taskSettings a map with the settings for the execution of this task
     */
    public ValidationPlugin(final String language, final Map<String, String> taskSettings) {
        this(language);
        this.inputs = taskSettings;
    }



    @Override
    public final ValidationPlugin newInstance(final String language) {
        return new ValidationPlugin(language);
    }



    @Override
    public final ValidationPlugin newInstance(final String language, final Map<String, String> taskSettings) {
        return new ValidationPlugin(language, taskSettings);
    }



    @Override
    public final String getLabel() {
        return this.messages.getString("plugin.label");
    }



    @Override
    public final String getCode() {
        return this.code;
    }



    @Override
    public final String getDescription() {
        return this.messages.getString("plugin.description");
    }



    @Override
    public final String getHelp() {

        if (this.help == null) {
            this.help = this.messages.getFileContent(ValidationPlugin.HELP_FILE_NAME);
        }

        return this.help;
    }



    @Override
    public final String getPictoClass() {
        return this.pictoClass;
    }



    @Override
    public final String getParams() {
        return "[{}]";
    }



    @Override
    public final ITaskProcessorResult execute(final ITaskProcessorRequest request) {

        final ValidationResult pluginResult = new ValidationResult();
        pluginResult.setMessage(this.messages.getString("messageValidation"));
        pluginResult.setStatus(ValidationResult.Status.STANDBY);
        pluginResult.setRequestData(request);

        return pluginResult;
    }

}
