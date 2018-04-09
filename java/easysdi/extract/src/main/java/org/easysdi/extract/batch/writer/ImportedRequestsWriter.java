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
package org.easysdi.extract.batch.writer;

import java.util.List;
import org.easysdi.extract.domain.Request;
import org.easysdi.extract.persistence.RequestsRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.batch.item.ItemWriter;
import org.springframework.context.annotation.Scope;



/**
 * An object that saves a set of requests that have just been created and updates the related information
 * of the connector instance that they have been imported through.
 *
 * @author Yves Grasset
 */
@Scope("step")
public class ImportedRequestsWriter implements ItemWriter<Request> {

    /**
     * The connector object that has imported the requests to save.
     */
    private int connectorId;

    /**
     * The writer to the application logs.
     */
    private final Logger logger = LoggerFactory.getLogger(ImportedRequestsWriter.class);

    /**
     * The Spring Data object that link the request data objects with the data source.
     */
    private RequestsRepository repository;



    /**
     * Creates a new instance of this writer.
     *
     * @param connectorIdentifier the number that identifies the instance containing the connector parameters used to
     *                            import the requests
     * @param requestsRepository  the Spring Data object that link the request data objects with the data source
     */
    public ImportedRequestsWriter(final int connectorIdentifier, final RequestsRepository requestsRepository) {

        if (connectorIdentifier < 1) {
            throw new IllegalArgumentException("The connector identifier must be greater than 0.");
        }

        if (requestsRepository == null) {
            throw new IllegalArgumentException("The requests repository cannot be null.");
        }

        this.connectorId = connectorIdentifier;
        this.repository = requestsRepository;
    }



    /**
     * Saves the imported requests to the data source.
     *
     * @param requestsList a list that contains the imported requests to save
     */
    @Override
    public final void write(final List<? extends Request> requestsList) {

        try {

            if (requestsList == null) {
                throw new IllegalStateException("The requests list cannot be null.");
            }

            for (Request request : requestsList) {

                if (request.getConnector().getId() != this.connectorId) {
                    this.logger.warn("A request in the collection to persist is not related to the current connector"
                            + " and has been ignored.");
                    continue;
                }

                this.repository.save(request);
            }

        } catch (Exception exception) {
            this.logger.error("Could not save the imported requests.", exception);
            throw exception;
        }
    }

}
