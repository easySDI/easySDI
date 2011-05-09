package org.easysdi.publish.dat.transformation.ogr;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Map;
import java.util.Properties;
import java.util.logging.Logger;

import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.util.Utils;

public class InputDatasetInfo {
	Logger logger = Logger.getLogger("eu.bauel.publish.transformation.plugin.InputDatasetInfo");
	private List<Dataset> datasetList;

	public InputDatasetInfo(){
		datasetList = new ArrayList<Dataset>();
	}

	public void getInfoForDataset(String pathAndName) throws PublishConfigurationException, DataInputException, IOException{
		String commandLine = null;
		String[] args = null;
		Process p = null;
		InputStream is = null;
		ProcessBuilder pb = null;
		try {
			commandLine = Utils.getShellPrefix()+"ogrinfo -ro "+pathAndName;
			System.out.println(commandLine);
			args = commandLine.split(" ");
			pb = new ProcessBuilder(args);
			pb.redirectErrorStream(true);
			p = pb.start();
			is = p.getInputStream();
			StringBuilder sb = new StringBuilder();
			Integer b;
			boolean isFailure = false;
			while ((b = is.read()) >= 0){
				sb.append(new String(new byte[] {b.byteValue()}));
				//detect failure
				if(sb.toString().contains("FAILURE")){
					isFailure = true;
					continue;
				}
			}
			//Report exceptions from the process
			if(isFailure)
				throw new DataInputException(sb.toString());

			System.out.println(sb.toString());

			//Read all lines
			String[] lines = null;
			if(Utils.isUnix())
				lines = sb.toString().split("\n");
			else
				lines = sb.toString().split("\r\n");
			logger.info("read lines:");
			//read all dataset and feed them to the ArrayList
			for(int i=0; i<lines.length; i++){
				logger.info("line"+i+":"+lines[i]);
				Dataset ds = new Dataset();
				ds.setName("");
				ds.setGeometry("");
				//No exception parsed, read result and set type name and Geometry
				String temp[] = lines[i].split(": ");
				String temp2[];
				String tkn = "";
				if(temp.length > 1 && !temp[0].equalsIgnoreCase("INFO"))
					tkn = temp[1];
				else
					continue;

				//for the dataset name;
				logger.info("token:"+tkn);
				temp = tkn.split(" ");
				if(temp.length < 1)
					throw new DataInputException("No dataset found:"+sb.toString());
				ds.setName(temp[0]);

				//for the geom
				temp2 = tkn.split("\\(");
				if(temp2.length < 2){
					ds.setGeometry("");
				}else{
					if(temp2.length < 1)
						throw new DataInputException("No dataset found:"+sb.toString());
					ds.setGeometry(temp2[1].substring(0, ( temp2[1].length()-1)));
				}
				
				this.datasetList.add(ds);
			}
			logger.info("end lines:");
			//int exitCode = p.exitValue();
			//System.out.println("Exited with code: " + exitCode);
		} catch (IOException e) {
			System.err.println("\nCommand " + Arrays.asList(args) + " reported " + e);
			throw new PublishConfigurationException("Unable to run transformation process:"+
					"\nCommand " + Arrays.asList(args) + " reported " + e);
		} finally{
			if(is != null)
				is.close();
			if(p != null)
				p.destroy();
			pb = null;
		}
	}
	
	public List<Dataset> getDatasets(){
		return this.datasetList;
	}
}
	
