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
package org.easysdi.extract.connectors.easysdiv4;

import org.easysdi.extract.connectors.common.IProduct;



/**
 * A data item request that has been imported.
 *
 * @author Yves Grasset
 */
public class Product implements IProduct {

    /**
     * The description of the order that this request is part of.
     */
    private String orderLabel;

    /**
     * The identifier of the order that this request is part of.
     */
    private String orderGuid;

    /**
     * The identifier of the requested data item.
     */
    private String productGuid;

    /**
     * The description of the requested data item.
     */
    private String productLabel;

    /**
     * The name of the organization that ordered this data item.
     */
    private String organism;

    /**
     * The name of the person that ordered this data item.
     */
    private String client;

    /**
     * Additional information (usually contact information) about the person that ordered this data item.
     */
    private String clientDetails;

    /**
     * The name of the person that this data item was requested on behalf of, if any.
     */
    private String tiers;

    /**
     * Additional information (usually contact information) about the person that this data item was
     * requested on behalf of, if any.
     */
    private String tiersDetails;

    /**
     * The geographical area of the data to extract, as a WKT geometry with WGS84 coordinates.
     */
    private String perimeter;

    /**
     * The size of the extract area in square meters.
     */
    private Double surface;

    /**
     * Additional settings for the processing of this request.
     */
    private String othersParameters;



    @Override
    public final String getOrderLabel() {
        return this.orderLabel;
    }



    /**
     * Defines the description of the order that this product request is part of.
     *
     * @param label the order label
     */
    public final void setOrderLabel(final String label) {
        this.orderLabel = label;
    }



    @Override
    public final String getOrderGuid() {
        return this.orderGuid;
    }



    /**
     * Defines the identifier of the order that this product request is part of.
     *
     * @param guid the order identifier
     */
    public final void setOrderGuid(final String guid) {
        this.orderGuid = guid;
    }



    @Override
    public final String getProductGuid() {
        return this.productGuid;
    }



    /**
     * Defines the identifier of the requested data item.
     *
     * @param guid the product identifier
     */
    public final void setProductGuid(final String guid) {
        this.productGuid = guid;
    }



    @Override
    public final String getProductLabel() {
        return this.productLabel;
    }



    /**
     * Defines the description of the requested data item.
     *
     * @param label the product label
     */
    public final void setProductLabel(final String label) {
        this.productLabel = label;
    }



    @Override
    public final String getOrganism() {
        return this.organism;
    }



    /**
     * Defines the organization that requested this product.
     *
     * @param organismName the name of the organization
     */
    public final void setOrganism(final String organismName) {
        this.organism = organismName;
    }



    @Override
    public final String getClient() {
        return this.client;
    }



    /**
     * Defines the person who requested this data item.
     *
     * @param name the customer's name
     */
    public final void setClient(final String name) {
        this.client = name;
    }



    @Override
    public final String getClientDetails() {
        return this.clientDetails;
    }



    /**
     * Defines additional information (usually contact information) about the person who requested
     * this product.
     *
     * @param details a string with information about the customer
     */
    public final void setClientDetails(final String details) {
        this.clientDetails = details;
    }



    @Override
    public final String getTiers() {
        return this.tiers;
    }



    /**
     * Defines the person that this product was requested on behalf of.
     *
     * @param name the name of the third party, or <code>null</code> if there is not any
     */
    public final void setTiers(final String name) {
        this.tiers = name;
    }



    @Override
    public final String getTiersDetails() {
        return this.tiersDetails;
    }



    /**
     * Defines additional information (usually contact information) about the person that this product
     * was requested on behalf of, if there is any.
     *
     * @param details a string with additional information about the third party, or <code>null</code> if there is
     *                not any
     */
    public final void setTiersDetails(final String details) {
        this.tiersDetails = details;
    }



    @Override
    public final String getPerimeter() {
        return this.perimeter;
    }



    /**
     * Defines the geographical area of the data to extract.
     *
     * @param wktGeometry the extract area as a WKT geometry with WGS84 coordinates
     */
    public final void setPerimeter(final String wktGeometry) {
        this.perimeter = wktGeometry;
    }



    @Override
    public final Double getSurface() {
        return this.surface;
    }



    /**
     * Defines the size of the extract area.
     *
     * @param areaSize the area size in square meters
     */
    public final void setSurface(final Double areaSize) {
        this.surface = areaSize;
    }



    @Override
    public final String getOthersParameters() {
        return this.othersParameters;
    }



    /**
     * Defines additional settings to process this product request.
     *
     * @param parametersJson a string with the parameters and their value in JSON format
     */
    public final void setOthersParameters(final String parametersJson) {
        this.othersParameters = parametersJson;
    }

}
