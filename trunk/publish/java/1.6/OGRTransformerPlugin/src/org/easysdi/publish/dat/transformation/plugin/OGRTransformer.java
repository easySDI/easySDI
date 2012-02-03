/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & Remy Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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
package org.easysdi.publish.dat.transformation.plugin;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import org.deegree.services.wps.ProcessletExecutionInfo;
import org.easysdi.publish.dat.transformation.ogr.InputDatasetInfo;

import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DataSourceNotFoundException;
import org.easysdi.publish.exception.DataSourceWrongFormatException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.transformation.ITransformerAdapter;
import org.easysdi.publish.util.Utils;

public class OGRTransformer implements ITransformerAdapter {

	Logger logger = Logger.getLogger("org.easysdi.publish.transformation.plugin.OGRTransformer");
	String m_executionPath = "";
	float progress = 0f;
	InputDatasetInfo idi = new InputDatasetInfo();
	String sourceFileDir = null;
	String mainFileName = null;
	ProcessletExecutionInfo info = null;
	
	public void transformDataset( ProcessletExecutionInfo info, String postgisOutputTableName, String sourceFileDir, List<String> URLs, String dbhost, String dbport, String dbname,
			String dbusername, String dbpassword, String dbschema, String epsgProj, String dataset) throws IOException, DataSourceWrongFormatException, PublishConfigurationException, DataSourceNotFoundException, TransformationException {
        
		this.info = info;
		//look for the files in the temp dir.
		this.sourceFileDir = sourceFileDir;
		String fileName = null;
		String extension = null;
		for(int i=0; i<URLs.size(); i++){
			String url = URLs.get(i);
			String[] tempStr = url.split("/");
			int length =tempStr.length;
			fileName = url.split("/")[tempStr.length - 1];
			String[] arrfileName = fileName.split("\\.");
			if(arrfileName.length < 2){
				System.out.println(url);
				throw new DataSourceWrongFormatException(arrfileName[0]);
			}
			//fetch the main files of the supplied collection, this file
			//will be passed to the script
			String candidateExt = arrfileName[1].toLowerCase();
			if(
					candidateExt.equals("shp")
					||candidateExt.equals("gml")
					||candidateExt.equals("mif")
					||candidateExt.equals("tab")
			){
				mainFileName =  fileName;
				extension = arrfileName[1].toLowerCase();
				break;
			}else{
				//catch the first one if no candidate found
				if(i == (URLs.size() - 1)){
					mainFileName =  fileName;
					extension = arrfileName[1].toLowerCase();
					break;
				}
			}
			
		}
		if(mainFileName == null)
			throw new DataSourceWrongFormatException(fileName);

		//Look for the datasets contained into the supplied file
		try {
			idi.getInfoForDataset(sourceFileDir+mainFileName);
		} catch (DataInputException e) {
			throw new TransformationException(e.getMessage());
		}	
		//limitation: transform ONLY the FIRST dataset found in the supplied files if no supplied
        if(dataset.equals(""))
        	dataset = idi.getDatasets().get(0).getName();
        
        //Build the command
		List<String>arguments = new ArrayList<String>();
		arguments.add("-progress");
		arguments.add("-a_srs");
		arguments.add(epsgProj);
		arguments.add("-f");
		arguments.add("PostgreSQL");
		if(Utils.isWindows()){
			arguments.add("PG:\"dbname="+dbname);
			arguments.add("host="+dbhost);
			arguments.add("port="+dbport);
			arguments.add("user="+dbusername);
			arguments.add("password="+dbpassword+"\"");
		}else{
			arguments.add("PG:dbname="+dbname+" host="+dbhost+" port="+dbport+" user="+dbusername+" password="+dbpassword);
		}
		
		arguments.add("-nln");
		arguments.add(postgisOutputTableName);
        arguments.add(sourceFileDir+mainFileName);
        arguments.add(dataset);

		//String commandLine = Utils.getShellPrefix()+"ogr2ogr "+arguments+" "+sourceFileDir+mainFileName+" "+dataset;
        arguments.add(0, "ogr2ogr");
        if(Utils.isWindows()){
        	//arguments.add(0, "/c");
        	//arguments.add(0, "cmd");
        }else if(Utils.isUnix()){
        	//arguments.add(0, "sh");
        }
        
		//starting the transformation process for all datasets found in the supplied file.
        runTransformProcess(arguments);
		
		}
	
	
	private void runTransformProcess(List<String> arguments) throws IOException, DataSourceWrongFormatException, PublishConfigurationException, DataSourceNotFoundException, TransformationException{
		Process p = null;
		InputStream is = null;
		ProcessBuilder pb = null;
		try {
			pb = new ProcessBuilder(arguments);
			logger.info("Command is:"+OGRTransformer.commandToString(pb));
			//read system environment variables
			Map<String, String> env = pb.environment();
			env.put("PGCLIENTENCODING", "LATIN1");
			pb.redirectErrorStream(true);
			p = pb.start();
			is = p.getInputStream();
			StringBuilder sb = new StringBuilder();
			Integer b;
			boolean isFailure = false;
			boolean isWarn = false;
			boolean isError = false;
			logger.info("Begin to read...");
			while ((b = is.read()) >= 0){
				System.out.print(new String(new byte[] {b.byteValue()}));
				sb.append(new String(new byte[] {b.byteValue()}));
				//detect failure
				if(sb.toString().contains("FAILURE")){
					isFailure = true;
					continue;
				}
				//detect warning
				if(sb.toString().contains("Warnin")){
					isWarn = true;
					continue;
				}
				//detect error
				if(sb.toString().contains("ERROR")){
					isError = true;
					continue;
				}
				//detect progress by waiting a first pattern and set it.
				if(sb.toString().contains("0..")){
					parseAndSetProgress(sb.toString());
					System.out.println(sb.toString());
				}
				//detect if progress non available
				if(sb.toString().contains("Progress turned off")){
					this.setProgress(-1f);
					System.out.println(sb.toString());
				}
			}
			logger.info("End reading...");
			
			try {
				//Wait for process termination
				p.waitFor();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			
			logger.info("Reporting detected problems...");
			//Report exceptions from the process
			if(isFailure){
				System.out.println("FAILURE REPORTED: "+sb.toString());
				throw new TransformationException(sb.toString());
			}
			else if(isWarn){
				System.out.println("WARNING REPORTED: "+sb.toString());
				//Sometimes the OGR library fails to guess the good geometry type for SHAPE.
				//Specially it would give "polygon" instead of "Multipolygon"
				//Then the features could not be inserted into PostGIS
				//We try once again with the "Multi" prefix
				if(sb.toString().contains("Geometry to be inserted is of type")){
					try {
						idi.getInfoForDataset(sourceFileDir+mainFileName);
						String geometry = idi.getDatasets().get(0).getGeometry();
						if(geometry.equalsIgnoreCase("polygon"))
							geometry="MULTIPOLYGON";
						if(geometry.equalsIgnoreCase("point"))
							geometry="MULTIPOINT";
						if(geometry.equalsIgnoreCase("line"))
							geometry="MULTILINESTRING";
						//try once again forcing the geometry to "multi" and overwrite the table
						arguments.add("-nlt");
						arguments.add(geometry);
						arguments.add("-overwrite");
						logger.info("Command is:"+OGRTransformer.commandToString(pb));
						runTransformProcess(arguments);
					} catch (DataInputException e) {
						throw new TransformationException(e.getMessage());
					}
				}else{
					throw new TransformationException(sb.toString());
				}
			}
			else if(isError){
				System.out.println("ERROR REPORTED: "+sb.toString());
				throw new TransformationException(sb.toString());
			}else{
				logger.info("NO ERRORS OR WARNINGS");
			}
				
			logger.info("Ended reporting exceptions...");

		} catch (IOException e) {
			System.err.println("\nCommand " + OGRTransformer.commandToString(pb) + " reported " + e);
			throw new PublishConfigurationException("Unable to run transformation process:"+
					"\nCommand " + OGRTransformer.commandToString(pb) + " reported " + e);
		} 
		finally{
			if(is != null)
				is.close();
			if(p != null)
				p.destroy();
			pb = null;
		}
	}
	
	private static String commandToString(ProcessBuilder pb){
		List<String>args = pb.command();
		StringBuilder sb = new StringBuilder();
		for(String arg:args){
			sb.append(arg+" ");
		}
		return sb.toString();
	}
	
	public void setLocation( String path )
	{
		m_executionPath = path;
	}

	@Override
	public float getProgress() {
		return progress;
	}

	public void setProgress(Float progress) {
		this.progress = progress;
		if(this.info != null)
			this.info.setPercentCompleted(progress.intValue());
	}

	public void parseAndSetProgress(String s){
		if(s.equalsIgnoreCase("0."))
			this.setProgress(2.5f);
		else if(s.equalsIgnoreCase("0.."))
			this.setProgress(5f);
		else if(s.equalsIgnoreCase("0..."))
			this.setProgress(7.5f);
		else if(s.equalsIgnoreCase("0...10"))
			this.setProgress(10f);
		else if(s.equalsIgnoreCase("0...10...20"))
			this.setProgress(20f);
		else if(s.equalsIgnoreCase("0...10...20...30"))
			this.setProgress(30f);
		else if(s.equalsIgnoreCase("0...10...20...30...40"))
			this.setProgress(40f);
		else if(s.equalsIgnoreCase("0...10...20...30...40...50"))
			this.setProgress(50f);
		else if(s.equalsIgnoreCase("0...10...20...30...40...50...60"))
			this.setProgress(60f);
		else if(s.equalsIgnoreCase("0...10...20...30...40...50...60...70"))
			this.setProgress(70f);
		else if(s.equalsIgnoreCase("0...10...20...30...40...50...60...70...80"))
			this.setProgress(80f);
		else if(s.equalsIgnoreCase("0...10...20...30...40...50...60...70...80...90"))
			this.setProgress(90f);
		else if(s.equalsIgnoreCase("0...10...20...30...40...50...60...70...80...90...100 - done."))
			this.setProgress(100f);	
	}
}
