package org.easysdi.publish.dat.transformation.ogr;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import org.easysdi.publish.dat.transformation.ogr.Dataset;

import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.PublishConfigurationException;

public class InputDatasetInfoTest {

	private static InputDatasetInfo idi = new InputDatasetInfo();
	/**
	 * @param args
	 */
	public static void main(String[] args) {

		String file1 = "C:\\wamp\\www\\testdata\\FME\\GPS\\gps_control.gpx";
		String file2 = "c:\\wamp\\www\\testdata\\FME\\DemoData\\FolderBased\\RoadLine.mif";
		String file3 = "c:\\wamp\\www\\testdata\\gg25_a.shp";
		
		try {
			idi.getInfoForDataset(file1);
			dumbDataSets();
			List<Dataset> lsd = idi.getDatasets();
			System.out.println(idi.getDatasets().get(0).getName());
			
		} catch (PublishConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (DataInputException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
	
	private static void dumbDataSets(){
		for(Dataset ds : idi.getDatasets()){
			System.out.println("Dataset:");
			System.out.println("name:"+ds.getName());
			System.out.println("geometry:"+ds.getGeometry());
			System.out.println("--------------------------------------");
		}
	}

}
