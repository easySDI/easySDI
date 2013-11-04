package org.easysdi.proxy.integratedmodelling.geospace.gis;

/**
 * NOAA's National Climatic Data Center
 * NOAA/NESDIS/NCDC
 * 151 Patton Ave, Asheville, NC  28801
 * 
 * THIS SOFTWARE AND ITS DOCUMENTATION ARE CONSIDERED TO BE IN THE 
 * PUBLIC DOMAIN AND THUS ARE AVAILABLE FOR UNRESTRICTED PUBLIC USE.  
 * THEY ARE FURNISHED "AS IS." THE AUTHORS, THE UNITED STATES GOVERNMENT, ITS
 * INSTRUMENTALITIES, OFFICERS, EMPLOYEES, AND AGENTS MAKE NO WARRANTY,
 * EXPRESS OR IMPLIED, AS TO THE USEFULNESS OF THE SOFTWARE AND
 * DOCUMENTATION FOR ANY PURPOSE. THEY ASSUME NO RESPONSIBILITY (1)
 * FOR THE USE OF THE SOFTWARE AND DOCUMENTATION; OR (2) TO PROVIDE
 * TECHNICAL SUPPORT TO USERS.
 */

import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.GraphicsEnvironment;
import java.awt.image.BufferedImage;
import java.awt.image.DataBuffer;
import java.awt.image.WritableRaster;
import java.nio.ByteBuffer;

import javax.media.jai.RasterFactory;

import org.geotools.coverage.grid.GridCoverage2D;
import org.geotools.coverage.grid.GridCoverageFactory;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureIterator;
import org.geotools.geometry.jts.ReferencedEnvelope;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.LineString;
import com.vividsolutions.jts.geom.LinearRing;
import com.vividsolutions.jts.geom.MultiLineString;
import com.vividsolutions.jts.geom.MultiPoint;
import com.vividsolutions.jts.geom.MultiPolygon;
import com.vividsolutions.jts.geom.Point;
import com.vividsolutions.jts.geom.Polygon;

/**
 *  Rasterize features onto a WritableRaster object using Java 2D Graphics/BufferedImage.
 *
 * @author     steve.ansari
 * @author Ferdinando Villa
 * @created    March 20, 2008
 */
public class FeatureRasterizer {

	public class FeatureRasterizerException extends Exception {
		   
	  private static final long serialVersionUID = 181880408872058192L;

		/**
	     * Constructor with message argument.
	     *
	     * @param message Reason for the exception being thrown
	     */
	    public FeatureRasterizerException(String message) {
	        super(message);
	    }	    
	 }

    private int height;
    private int width;
    private double noDataValue;
    private WritableRaster raster = null;   
    private BufferedImage bimage = null;
    private Graphics2D graphics = null;

    private java.awt.geom.Rectangle2D.Double bounds;
    private double cellsize;
    private double minAttValue = 999999999;
    private double maxAttValue = -999999999;

    // Declare these as global
    private int[] coordGridX = new int[3500];
    private int[] coordGridY = new int[3500];
    private float value;

    private boolean emptyGrid = false;

    private Geometry extentGeometry;
    private GeometryFactory geoFactory = new GeometryFactory();
    private String attributeName = "value";

	public static GridCoverageFactory rasterFactory = new GridCoverageFactory();
    
    private double xInterval;
    private double yInterval;  

    // Any change in height, width or no_data values will cause 
    // the raster to 'reset' at the next call to .rasterize(...)
    private boolean resetRaster = false;

    /**
     *Constructor for the FeatureRasterizer object
     *
     * @exception  FeatureRasterizerException  Description of the Exception
     */
    public FeatureRasterizer() {
        this(800, 800, -999.0f);
    }

    /**
     * Constructor for the FeatureRasterizer object - will use default 800x800 raster
     *
     * @param  noData                         No Data value for raster
     * @exception  FeatureRasterizerException  Description of the Exception
     */
    public FeatureRasterizer(float noData) {
        this(800, 800, noData);
    }

    /**
     * Constructor for the FeatureRasterizer object.  No Data value defaults to -999.0
     *
     * @param  height                         Height of raster (number of grid cells)
     * @param  width                          Width of raster (number of grid cells)
     */
    public FeatureRasterizer(int height, int width) {
        this(height, width, -999.0f);
    }

    /**
     * Constructor for the FeatureRasterizer object
     *
     * @param  height                         Height of raster (number of grid cells)
     * @param  width                          Width of raster (number of grid cells)
     * @param  noData                         No Data value for raster
     */
    public FeatureRasterizer(int height, int width, float noData) {
        this.height = height;
        this.width = width;
        this.noDataValue = noData;

        raster = RasterFactory.createBandedRaster(DataBuffer.TYPE_FLOAT,
                width, height, 1, null);
        bimage = new BufferedImage(width, height, BufferedImage.TYPE_INT_ARGB);
        bimage.setAccelerationPriority(1.0f);

        GraphicsEnvironment ge = GraphicsEnvironment.getLocalGraphicsEnvironment();

        graphics = bimage.createGraphics();
        graphics.setPaintMode();
        graphics.setComposite(AlphaComposite.Src);


    }
    
    public GridCoverage2D rasterize(String name, FeatureCollection fc, String attributeName, ReferencedEnvelope env) throws FeatureRasterizerException {
    	
    	if (raster == null) {
    	
    		WritableRaster raster = 
    			RasterFactory.createBandedRaster(
    				DataBuffer.TYPE_FLOAT, 
					this.width, 
					this.height, 
					1, 
					null);
		
			setWritableRaster(raster);
    	} 

    	clearRaster();
    	
    	if (env == null) {
    	    		
    		rasterize(fc, attributeName);

    		/*
    		 * Use full envelope from feature collection
    		 * TODO check if we need to use a buffer like in Steve's code above
    		 */
    		env = fc.getBounds();

    	} else {
    		
    		/*
    		 * TODO check if we need to use a buffer like in Steve's code above
    		 */
			 java.awt.geom.Rectangle2D.Double box =
	               new java.awt.geom.Rectangle2D.Double(
	            		   env.getMinX(),
	            		   env.getMinY(), 
	            		   env.getWidth(), 
	            		   env.getHeight());
			 
			 rasterize(fc, box, attributeName);
    	}
    	
		GridCoverage2D coverage = 
			rasterFactory.create(name, raster, env);
		
    	return coverage;
    }

    /**
     *  Gets the raster attribute of the FeatureRasterizer object
     *  Processes data from the FeatureCollection and approximates onto a Raster Grid.
     *
     * @param  fc                             Feature Collection with features to rasterize.
     * @param  attributeName                  Name of attribute from feature collection to provide as the cell value.
     * @exception  FeatureRasterizerException  An error when rasterizing the data
     */
    public void rasterize(FeatureCollection fc, String attributeName)
    throws FeatureRasterizerException {

        // calculate variable resolution bounds that fit around feature collection

        double edgeBuffer = 0.001;
        double x = fc.getBounds().getMinX() - edgeBuffer;
        double y = fc.getBounds().getMinY() - edgeBuffer;
        double width = fc.getBounds().getWidth() + edgeBuffer * 2;
        double height = fc.getBounds().getHeight() + edgeBuffer * 2;
        java.awt.geom.Rectangle2D.Double bounds = new java.awt.geom.Rectangle2D.Double(x, y, width, height);
                
        rasterize(fc, bounds, attributeName);
        
    }

    /**
     *  Gets the raster attribute of the FeatureRasterizer object
     *  Processes data from the FeatureCollection and approximates onto a Raster Grid.
     *
     * @param  fc                             Description of the Parameter
     * @param  bounds                         Description of the Parameter
     * @param  attributeName                  Name of attribute from feature collection to provide as the cell value.
     * @exception  FeatureRasterizerException  An error when rasterizing the data
     */
    public void rasterize(FeatureCollection fc, java.awt.geom.Rectangle2D.Double bounds, String attributeName)
    	throws FeatureRasterizerException {

        this.attributeName = attributeName;
        
        // Check if we need to change the underlying raster
        if (resetRaster) {
            raster = RasterFactory.createBandedRaster(DataBuffer.TYPE_FLOAT,
                    width, height, 1, null);

            bimage = new BufferedImage(width, height, BufferedImage.TYPE_INT_ARGB);
            bimage.setAccelerationPriority(1.0f);
            GraphicsEnvironment ge = GraphicsEnvironment.getLocalGraphicsEnvironment();

            graphics = bimage.createGraphics();
            graphics.setPaintMode();
            graphics.setComposite(AlphaComposite.Src);


            resetRaster = false;


        }
        // initialize raster to NoData value
        clearRaster();
        setBounds(bounds);

        // TODO - change method calls to account for a switch to control if rasterizer should work if vis bounds > feature bounds


        // All the data should start in the lower left corner.  Don't export what we don't need.
        double ratio = bounds.height / bounds.width;
        int ncols;
        int nrows;
        if (ratio < 1) {
            // wider than tall
            nrows = (int) (ratio * height);
            ncols = width;
        }
        else {
            nrows = height;
            ncols = (int) (height / ratio);
        }



        FeatureIterator fci = fc.features();
        Feature feature;

        while (fci.hasNext()) {


            feature = fci.next();

            addFeature(feature);

        }
        close();
    }

    /**
     * Implementation the StreamingProcess interface.  Rasterize a single feature and 
     * update current WriteableRaster using the current settings.
     * 
     * @param  feature     The feature to rasterize and add to current WritableRaster
     */   
    public void addFeature(Feature feature) {


    	
        try {

            value = Float.parseFloat(feature.getAttribute(attributeName).toString());               

            if (value > maxAttValue) { maxAttValue = value; }
            if (value < minAttValue) { minAttValue = value; }

        } catch (Exception e) {	        
            e.printStackTrace();	        
            System.err.println("THE FEATURE COULD NOT BE RASTERIZED BASED ON THE '"+attributeName+
                    "' ATTRIBUTE VALUE OF '"+feature.getAttribute(attributeName).toString()+"'");	        
            return;	        
        }

        int rgbVal = floatBitsToInt(value);

        graphics.setColor(new Color(rgbVal, true));

        // Extract polygon and rasterize!
        Geometry geometry = feature.getDefaultGeometry();
        if (geometry.intersects(extentGeometry)) {
     //  if (extentGeometry.contains(geometry)) {
            
            if (geometry.getClass().equals(MultiPolygon.class)) {
                MultiPolygon mp = (MultiPolygon)geometry;
                for (int n=0; n<mp.getNumGeometries(); n++) {
                    drawGeometry(mp.getGeometryN(n));
                }
            }
            else if (geometry.getClass().equals(MultiLineString.class)) {
                MultiLineString mp = (MultiLineString)geometry;
                for (int n=0; n<mp.getNumGeometries(); n++) {
                    drawGeometry(mp.getGeometryN(n));
                }
            }
            else if (geometry.getClass().equals(MultiPoint.class)) {
                MultiPoint mp = (MultiPoint)geometry;
                for (int n=0; n<mp.getNumGeometries(); n++) {
                    drawGeometry(mp.getGeometryN(n));
                }
            }
            else {
                drawGeometry(geometry);
            }
       }
    }

    /**
     * Implementation the StreamingProcess interface - this copies values from BufferedImage RGB to WritableRaster of floats.
     */
    public void close() {
        for (int i = 0; i < width; i++) {
            for (int j = 0; j < height; j++) {
                double val = Float.intBitsToFloat(bimage.getRGB(i, j));
                raster.setSample(i, j, 0, val);
            }
        }
    }

    private void drawGeometry(Geometry geometry) {

        Coordinate[] coords = geometry.getCoordinates();

        // enlarge if needed
        if (coords.length > coordGridX.length) {
            coordGridX = new int[coords.length];
            coordGridY = new int[coords.length];
        }

        // Clear Array
        for (int i = 0; i < coords.length; i++) {
            coordGridX[i] = -1;
        }
        for (int i = 0; i < coords.length; i++) {
            coordGridY[i] = -1;
        }

        // Go through coordinate array in order received (clockwise)
        for (int n = 0; n < coords.length; n++) {
            coordGridX[n] = (int) (((coords[n].x - bounds.x) / xInterval));
            coordGridY[n] = (int) (((coords[n].y - bounds.y) / yInterval));
            coordGridY[n] = bimage.getHeight() - coordGridY[n]; 
        }


        if (geometry.getClass().equals(Polygon.class)) {
            graphics.fillPolygon(coordGridX, coordGridY, coords.length);
        }
        else if (geometry.getClass().equals(LinearRing.class)) {
            graphics.drawPolyline(coordGridX, coordGridY, coords.length);
        }
        else if (geometry.getClass().equals(LineString.class)) {
            graphics.drawPolyline(coordGridX, coordGridY, coords.length);
        }
        else if (geometry.getClass().equals(Point.class)) {
            graphics.drawPolyline(coordGridX, coordGridY, coords.length);
        }
    }

    /**
     *  Gets the emptyGrid attribute of the FeatureRasterizer object
     *
     * @return    The emptyGrid value
     */
    public boolean isEmptyGrid() {
        return emptyGrid;
    }


    /**
     *  Gets the writableRaster attribute of the FeatureRasterizer object
     *
     * @return    The writableRaster value
     */
    public WritableRaster getWritableRaster() {
        return raster;
    }

    /**
     *  Sets the writableRaster attribute of the FeatureRasterizer object
     *
     * @param  raster  The new writableRaster value
     */
    public void setWritableRaster(WritableRaster raster) {
        this.raster = raster;
    }

    /**
     *  Gets the bounds attribute of the FeatureRasterizer object
     *
     * @return    The bounds value
     */
    public java.awt.geom.Rectangle2D.Double getBounds() {
        return bounds;
    }

    /**
     *  Sets the bounds for the Rasterizer
     *
     * @return    The bounds value
     */
    public void setBounds(java.awt.geom.Rectangle2D.Double bounds) {
        this.bounds = bounds;

        xInterval = bounds.width / (double) width;
        yInterval = bounds.height / (double) height;


// Debug tb 03.08.2009
// Cr�e des erreurs de masquage lorsqu'utilis� par le proxy si les proportions hauteur/largeurs diff�rent entre bbox et image
// Ne vois d'aileurs pas une utilit� quelconque m�me dans un autre contexte!
//        if (xInterval > yInterval) {
//            yInterval = xInterval;
//        }
//        if (yInterval > xInterval) {
//            xInterval = yInterval;
//        }
// Fin de Debug

        cellsize = yInterval;

        // Clip geometries to the provided bounds      
        // Create extent geometry  
        Envelope env = new Envelope(
                bounds.getX(), 
                bounds.getX() + bounds.getWidth(),
                bounds.getY(),
                bounds.getY() + bounds.getHeight()
        );
        extentGeometry = geoFactory.toGeometry(env);        
    }

    /**
     *  Sets the entire raster to NoData
     */
    public void clearRaster() {

      
        minAttValue = 999999999;
        maxAttValue = -999999999;

        // initialize raster to NoData value
        for (int i = 0; i < width; i++) {
            for (int j = 0; j < height; j++) {
                raster.setSample(i, j, 0, noDataValue);
                bimage.setRGB(i, j, floatBitsToInt((float)noDataValue));
            }
        }
    }

    
    public BufferedImage getBimage() {
        return bimage;
    }

    /**
     *  Get the current attribute to use as the grid cell values.
     */
    public String getAttName() {
        return attributeName;
    }

    /**
     *  Sets the current attribute to use as the grid cell values.
     */
    public void setAttName(String attName) {
        this.attributeName = attName;
    }

    /**
     *  Gets the cellsize attribute of the FeatureRasterizer object
     *
     * @return    The cellsize value
     */
    public double getCellsize() {
        return cellsize;
    }


    public double getNoDataValue() {
        return noDataValue;
    }

    public void setNoDataValue(double noData) {
        if (noData != noDataValue) {
            resetRaster = true;
        }
        this.noDataValue = noData;
    }

    public int getHeight() {
        return height;
    }

    public void setHeight(int height) {
        if (height != height) {
            resetRaster = true;
        }
        this.height = height;
    }

    public int getWidth() {
        return width;
    }

    public void setWidth(int width) {
        if (width != width) {
            resetRaster = true;
        }
        this.width = width;
    }

    public double getMinAttValue() {
        return minAttValue;
    }

    public double getMaxAttValue() {
        return maxAttValue;
    }

    private static int floatBitsToInt(float f) {
        ByteBuffer conv = ByteBuffer.allocate(4);
        conv.putFloat(0, f);
        return conv.getInt(0);
    }

    public String toString() {
        return "FEATURE RASTERIZER: WIDTH="+width+" , HEIGHT="+height+" , NODATA="+noDataValue;
    }

}