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
package org.easysdi.extract.domain;

import java.io.Serializable;
import java.util.Arrays;
import java.util.Collection;
import javax.persistence.Basic;
import javax.persistence.CascadeType;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.ForeignKey;
import javax.persistence.GeneratedValue;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.JoinTable;
import javax.persistence.ManyToMany;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;
import javax.persistence.OneToMany;
import javax.persistence.OrderBy;
import javax.persistence.Table;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.easysdi.extract.persistence.RequestsRepository;
import org.hibernate.annotations.SortNatural;



/**
 * A series of tasks that executes an order.
 *
 * @author Florent Krin
 */
@Entity
@Table(name = "Processes")
@NamedQueries({
    @NamedQuery(name = "Process.getProcessOperatorsAddresses",
            query = "SELECT u.email FROM Process p JOIN p.usersCollection u WHERE p.id = :processId"),
    @NamedQuery(name = "Process.getProcessOperatorsIds",
            query = "SELECT u.id FROM Process p JOIN p.usersCollection u WHERE p.id = :processId ORDER BY u.id")
})
@XmlRootElement
public class Process implements Serializable {

    private static final long serialVersionUID = 1L;

    /**
     * The integer that uniquely identifies this process in the application.
     */
    @Id
    @Basic(optional = false)
    @GeneratedValue
    @NotNull
    @Column(name = "id_process")
    private Integer id;

    /**
     * The string that identifies this process in a user-friendly fashion.
     */
    @Size(max = 255)
    @Column(name = "name")
    private String name;

    /**
     * The operators that supervise this process.
     */
    @JoinTable(name = "processes_users",
            joinColumns = {
                @JoinColumn(name = "id_process", referencedColumnName = "id_process",
                        foreignKey = @ForeignKey(name = "FK_PROCESSES_USERS_PROCESS")
                )
            },
            inverseJoinColumns = {
                @JoinColumn(name = "id_user", referencedColumnName = "id_user",
                        foreignKey = @ForeignKey(name = "FK_PROCESSES_USERS_USER")
                )
            }
    )
    @ManyToMany
    private Collection<User> usersCollection;

    /**
     * The processing tasks that make up this process.
     */
    @OneToMany(mappedBy = "process", cascade = CascadeType.REMOVE)
    @OrderBy("position ASC")
    @SortNatural
    private Collection<Task> tasksCollection;

    /**
     * The ordered products that are treated (or have been treated) with this process.
     */
    @OneToMany(mappedBy = "process")
    private Collection<Request> requestsCollection;

    /**
     * The criteria that bind products with this process.
     */
    @OneToMany(mappedBy = "process")
    private Collection<Rule> rulesCollection;



    /**
     * Creates a new process instance.
     */
    public Process() {
    }



    /**
     * Creates a new process instance.
     *
     * @param identifier the integer that identifies the process
     */
    public Process(final Integer identifier) {
        this.id = identifier;
    }



    /**
     * Obtains the integer that uniquely identify this process.
     *
     * @return the identifier
     */
    public Integer getId() {
        return id;
    }



    /**
     * Defines the integer that uniquely identify this process.
     *
     * @param identifier the identifier of the process
     */
    public void setId(final Integer identifier) {
        this.id = identifier;
    }



    /**
     * Obtains the string that identifies this process in a user-friendly fashion.
     *
     * @return the name of this process
     */
    public String getName() {
        return name;
    }



    /**
     * Defines the string that identifies this process in a user-friendly fashion.
     *
     * @param processName the name of this process
     */
    public void setName(final String processName) {
        this.name = processName;
    }



    /**
     * Obtains the users that supervise this process.
     *
     * @return a collection that contains the operators
     */
    @XmlTransient
    public Collection<User> getUsersCollection() {
        return usersCollection;
    }



    /**
     * Defines the users that supervise this process.
     *
     * @param users a collection that contains the operators for this process
     */
    public void setUsersCollection(final Collection<User> users) {
        this.usersCollection = users;
    }



    /**
     * Obtains the processing items that make up this process.
     *
     * @return a map of tasks with their sequence order of the item as the key
     */
    @XmlTransient
    public Collection<Task> getTasksCollection() {
        return tasksCollection;
    }



    /**
     * Defines the processing items that make up this process.
     *
     * @param tasks a map of tasks with their sequence order of the item as the key
     */
    public void setTasksCollection(final Collection<Task> tasks) {
        this.tasksCollection = tasks;
    }



    /**
     * Obtains the order products that are (or were) extracted by this process.
     *
     * @return a collection of requests
     */
    @XmlTransient
    public Collection<Request> getRequestsCollection() {
        return requestsCollection;
    }



    /**
     * Defines the order products that are (or were) extracted by this process.
     *
     * @param requests a collection of requests
     */
    public void setRequestsCollection(final Collection<Request> requests) {
        this.requestsCollection = requests;
    }



    /**
     * Obtains the criteria that match a product with this process.
     *
     * @return a collection of rules
     */
    @XmlTransient
    public Collection<Rule> getRulesCollection() {
        return rulesCollection;
    }



    /**
     * Defines the criteria that match a product with this process.
     *
     * @param rules a collection of rules
     */
    public void setRulesCollection(final Collection<Rule> rules) {
        this.rulesCollection = rules;
    }



    /**
     * Obtains whether at least one of the products extracted by this process is not finished.
     * <p>
     * <i><b>IMPORTANT:</b> This method parses the requests collection. It can then be very (very!) slow if there are
     * a lot of finished requests. In this case, it is advised to use the
     * {@link #hasActiveRequests(org.easysdi.extract.persistence.RequestsRepository)} method.</i>
     *
     * @return <code>true</code> if at least one product is still active
     */
    public final boolean hasActiveRequests() {

        for (Request request : this.getRequestsCollection()) {

            if (request.isActive()) {
                return true;
            }
        }

        return false;
    }



    /**
     * Obtains whether at least one of the products extracted by this process is not finished.
     *
     * @param requestsRepository the Spring Data object that links the request data objects with the data source
     * @return <code>true</code> if at least one product is still active
     */
    public final boolean hasActiveRequests(final RequestsRepository requestsRepository) {

        if (requestsRepository == null) {
            throw new IllegalArgumentException("The requests repository cannot be null.");
        }

        return !requestsRepository.findByStatusNotAndProcessIn(Request.Status.FINISHED, Arrays.asList(this)).isEmpty();
    }



    /**
     * Obtains whether at least one of the products extracted by this process currently running.
     * <p>
     * <i><b>IMPORTANT:</b> This method parses the requests collection. It can then be very (very!) slow if there are
     * a lot of finished requests. In this case, it is advised to use the
     * {@link #hasOngoingRequests(org.easysdi.extract.persistence.RequestsRepository)} method.</i>
     *
     * @return <code>true</code> if at least one product is running
     */
    public final boolean hasOngoingRequests() {

        for (Request request : this.getRequestsCollection()) {

            if (request.isOngoing()) {
                return true;
            }
        }

        return false;
    }



    /**
     * Obtains whether at least one of the products extracted by this process currently running.
     *
     * @param requestsRepository the Spring Data object that links the request data objects with the data source
     * @return <code>true</code> if at least one product is running
     */
    public final boolean hasOngoingRequests(final RequestsRepository requestsRepository) {

        if (requestsRepository == null) {
            throw new IllegalArgumentException("The requests repository cannot be null.");
        }

        return !requestsRepository.findByStatusAndProcessIn(Request.Status.ONGOING, Arrays.asList(this)).isEmpty();
    }



    /**
     * Obtains whether product-matching criteria are bound to this process.
     *
     * @return <code>true</code> if at least one rule sends requests to this process
     */
    public final boolean hasRulesAssigned() {
        Collection<Rule> rules = this.getRulesCollection();

        return rules != null && rules.size() > 0;
    }



    /**
     * Obtains whether this process can be removed from the data source.
     * <p>
     * <i><b>IMPORTANT:</b> This method parses the requests collection. It can then be very (very!) slow if there are
     * a lot of finished requests. In this case, it is advised to use the
     * {@link #canBeDeleted(org.easysdi.extract.persistence.RequestsRepository)} method.</i>
     *
     *
     * @return <code>true</code> if this process can be deleted
     */
    public final boolean canBeDeleted() {
        return !this.hasActiveRequests() && !this.hasRulesAssigned();
    }



    /**
     * Obtains whether this process can be removed from the data source.
     *
     * @param requestsRepository the Spring Data object that links the request data objects with the data source
     * @return <code>true</code> if this process can be deleted
     */
    public final boolean canBeDeleted(final RequestsRepository requestsRepository) {

        if (requestsRepository == null) {
            throw new IllegalArgumentException("The requests repository cannot be null.");
        }

        return !this.hasActiveRequests(requestsRepository) && !this.hasRulesAssigned();
    }



    /**
     * Obtains whether the parameters of this process can be modified.
     * <p>
     * <i><b>IMPORTANT:</b> This method parses the requests collection. It can then be very (very!) slow if there are
     * a lot of finished requests. In this case, it is advised to use the
     * {@link #canBeEdited(org.easysdi.extract.persistence.RequestsRepository)} method.</i>
     *
     * @return <code>true</code> if the content of this process can be edited
     */
    public final boolean canBeEdited() {
        return !this.hasOngoingRequests();
    }



    /**
     * Obtains whether the parameters of this process can be modified.
     *
     * @param requestsRepository the Spring Data object that links the request data objects with the data source
     * @return <code>true</code> if the content of this process can be edited
     */
    public final boolean canBeEdited(final RequestsRepository requestsRepository) {

        if (requestsRepository == null) {
            throw new IllegalArgumentException("The requests repository cannot be null.");
        }

        return !this.hasOngoingRequests(requestsRepository);
    }



    @Override
    public final int hashCode() {
        int hash = 0;
        hash += this.id.hashCode();

        return hash;
    }



    @Override
    public final boolean equals(final Object object) {

        if (object == null || !(object instanceof Process)) {
            return false;
        }

        final Process other = (Process) object;
        return this.id.equals(other.id);
    }



    @Override
    public final String toString() {
        return String.format("org.easysdi.extract.Process[ idProcess=%d ]", this.id);
    }

}
