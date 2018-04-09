/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package org.easysdi.extract.plugins.fmedesktop;

import java.io.BufferedReader;
import java.io.File;
import java.io.FilenameFilter;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Map;
import org.apache.commons.lang3.StringUtils;
import org.easysdi.extract.plugins.common.ITaskProcessor;
import org.easysdi.extract.plugins.common.ITaskProcessorRequest;
import org.easysdi.extract.plugins.common.ITaskProcessorResult;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;



/**
 * A plugin that executes an FME Desktop task.
 *
 * @author Florent Krin
 */
public class FmeDesktopPlugin implements ITaskProcessor {

    /**
     * The path to the file that holds the general settings of the plugin.
     */
    private static final String CONFIG_FILE_PATH = "plugins/fme/properties/configFME.properties";

    /**
     * The writer to the application logs.
     */
    private final Logger logger = LoggerFactory.getLogger(FmeDesktopPlugin.class);

    /**
     * The string that identifies this plugin.
     */
    private final String code = "FME2017";

    /**
     * The class of the icon to use to represent this plugin.
     */
    private final String pictoClass = "fa-cogs";

    /**
     * The stings that the plugin can send to the user in the language of the user interface.
     */
    private LocalizedMessages messages;

    /**
     * The settings for the execution of this particular task.
     */
    private Map<String, String> inputs;

    /**
     * The access to the general settings of the plugin.
     */
    private PluginConfiguration config;



    /**
     * Creates a new FME Desktop plugin instance with default settings and using the default language.
     */
    public FmeDesktopPlugin() {
        this.config = new PluginConfiguration(FmeDesktopPlugin.CONFIG_FILE_PATH);
        this.messages = new LocalizedMessages();
    }



    /**
     * Creates a new FME Desktop plugin instance with default settings.
     *
     * @param language the string that identifies the language to use to send messages to the user
     */
    public FmeDesktopPlugin(final String language) {
        this.config = new PluginConfiguration(FmeDesktopPlugin.CONFIG_FILE_PATH);
        this.messages = new LocalizedMessages(language);
    }



    /**
     * Creates a new FME Desktop plugin instance using the default language.
     *
     * @param taskSettings the settings for the execution of this particular task
     */
    public FmeDesktopPlugin(final Map<String, String> taskSettings) {
        this();
        this.inputs = taskSettings;
    }



    /**
     * Creates a new FME Desktop plugin instance.
     *
     * @param language     the string that identifies the language to use to send messages to the user
     * @param taskSettings the settings for the execution of this particular task
     */
    public FmeDesktopPlugin(final String language, final Map<String, String> taskSettings) {
        this(language);
        this.inputs = taskSettings;
    }



    @Override
    public final FmeDesktopPlugin newInstance(final String language) {
        return new FmeDesktopPlugin(language);
    }



    @Override
    public final FmeDesktopPlugin newInstance(final String language, final Map<String, String> taskSettings) {
        return new FmeDesktopPlugin(language, taskSettings);
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
        return "";
    }



    @Override
    public final String getPictoClass() {
        return this.pictoClass;
    }



    @Override
    public final String getParams() {
        StringBuilder builder = new StringBuilder("[{\"code\" : \"");

        builder.append(this.config.getProperty("paramPath"));
        builder.append("\", \"label\" : \"");
        builder.append(this.messages.getString("paramPath.label"));
        builder.append("\", \"type\" : \"text\", \"req\" : \"true\", \"maxlength\" : 255},{\"code\" : \"");

        builder.append(this.config.getProperty("paramPathFME"));
        builder.append("\", \"label\" : \"");
        builder.append(this.messages.getString("paramPathFME.label"));
        builder.append("\", \"type\" : \"text\", \"req\" : \"true\", \"maxlength\" : 255}]");

        return builder.toString();
    }



    @Override
    public final ITaskProcessorResult execute(final ITaskProcessorRequest request) {

        final FmeDesktopResult result = new FmeDesktopResult();
        FmeDesktopResult.Status resultStatus = FmeDesktopResult.Status.ERROR;
        String resultMessage = "";
        String resultErrorCode = "-1";

        try {

            this.logger.debug("Start FME extraction");

            final String fmwPath = this.inputs.get(this.config.getProperty("paramPath"));
            final String fmeExePath = this.inputs.get(this.config.getProperty("paramPathFME"));
            final String productId = request.getProductGuid();
            final String perimeter = request.getPerimeter();
            final String parameters = request.getParameters();
            final String folderOut = request.getFolderOut();

            final File fmwScript = new File(fmwPath);

            if (!fmwScript.exists() || !fmwScript.canRead() || !fmwScript.isFile()) {
                result.setStatus(FmeDesktopResult.Status.ERROR);
                result.setErrorCode("-1");
                result.setMessage(this.messages.getString("fme.script.notfound"));
                result.setRequestData(request);

                return result;
            }

            final File dirWorkspace = fmwScript.getParentFile();
            final File fmeExecutable = new File(fmeExePath);

            if (!fmeExecutable.exists() || !fmeExecutable.canRead() || !fmeExecutable.isFile()) {
                result.setStatus(FmeDesktopResult.Status.ERROR);
                result.setErrorCode("-1");
                result.setMessage(this.messages.getString("fme.executable.notfound"));
                result.setRequestData(request);

                return result;
            }

            //execute batch
            this.logger.debug("Executing FME batch : {}", fmwPath);
            final String command = String.format("\"%s\" \"%s\" --%s \"%s\" --%s \"%s\" --%s \"%s\" --%s %s",
                    fmeExePath, fmwPath, this.config.getProperty("paramRequestPerimeter"), perimeter,
                    this.config.getProperty("paramRequestProduct"), productId,
                    this.config.getProperty("paramRequestFolderOut"), folderOut,
                    this.config.getProperty("paramRequestParameters"), this.formatJsonParametersQuotes(parameters));

            final Process fmeTaskProcess = Runtime.getRuntime().exec(command, null, dirWorkspace);
            fmeTaskProcess.waitFor();

            int retValue = fmeTaskProcess.exitValue();

            if (retValue != 0) {
                resultStatus = FmeDesktopResult.Status.ERROR;
                resultErrorCode = "-1";
                final InputStream error = fmeTaskProcess.getErrorStream();
                final BufferedReader reader = new BufferedReader(new InputStreamReader(error));
                final StringBuilder messageBuilder = new StringBuilder();
                String line;

                while ((line = reader.readLine()) != null) {
                    messageBuilder.append(line);
                    messageBuilder.append("\r\n");
                }

            } else {
                final File dirFolderOut = new File(folderOut);
                final FilenameFilter resultFilter = new FilenameFilter() {

                    @Override
                    public boolean accept(final File dir, final String name) {
                        return (name != null);
                    }

                };
                final File[] resultFiles = dirFolderOut.listFiles(resultFilter);
                this.logger.debug("folder out {} contains {} file(s)", dirFolderOut.getPath(), resultFiles.length);

                if (resultFiles.length > 0) {
                    this.logger.debug("FME task succeeded");
                    resultStatus = FmeDesktopResult.Status.SUCCESS;
                    resultErrorCode = "";
                    resultMessage = this.messages.getString("fmeresult.message.success");
                } else {
                    this.logger.debug("Result folder is empty or not exists");
                    resultMessage = this.messages.getString("fmeresult.error.folderout.empty");
                }
            }

            this.logger.debug("End of FME extraction");

        } catch (Exception exception) {
            final String exceptionMessage = exception.getMessage();
            this.logger.error("The FME workbench has failed", exceptionMessage);
            resultMessage = String.format(this.messages.getString("fme.executing.failed"), exceptionMessage);

        }

        result.setStatus(resultStatus);
        result.setErrorCode(resultErrorCode);
        result.setMessage(resultMessage);
        result.setRequestData(request);

        return result;

    }



    /**
     * Ensures that the quotes in a JSON parameters string are correctly formatted to be passed as a
     * parameter to FME Desktop.
     *
     * @param json the JSON parameter string
     * @return a properly quoted JSON string
     */
    private String formatJsonParametersQuotes(final String json) {

        if (StringUtils.isEmpty(json)) {
            return json;
        }

        return String.format("\"%s\"", json.replaceAll("\"", "\"\""));
    }

}
