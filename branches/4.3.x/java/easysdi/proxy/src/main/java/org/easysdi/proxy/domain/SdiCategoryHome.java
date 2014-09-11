/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package org.easysdi.proxy.domain;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 *
 * @author mbattaglia
 */
@Transactional
@Repository
public class SdiCategoryHome {

    private static final Log log = LogFactory.getLog(SdiCategoryHome.class);

    @Autowired
    private SessionFactory sessionFactory;

    public SdiCategory findById(Integer id) {

        log.debug("getting SdiCategory instance with id: " + id);

        try {
            SdiCategory instance = (SdiCategory) sessionFactory.getCurrentSession().get(SdiCategory.class, id);

            log.debug("get successful");
            return instance;
        } catch (RuntimeException re) {
            log.error("get failed", re);
            throw re;
        }

    }

    public void save(SdiCategory transientInstance) {
        log.debug("save SdiCategory instance");
        try {
            sessionFactory.getCurrentSession().save(transientInstance);
            log.debug("save successful");
        } catch (RuntimeException re) {
            log.error("save failed", re);
            throw re;
        }
    }

    public void saveOrUpdate(SdiCategory transientInstance) {

        log.debug("saveOrUpdate SdiCategory instance");
        try {
            sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
            log.debug("saveOrUpdate successful");
        } catch (RuntimeException re) {
            log.error("saveOrUpdate failed", re);
            throw re;
        }
    }

    public void update(SdiCategory transientInstance) {
        log.debug("update SdiCategory instance");
        try {
            sessionFactory.getCurrentSession().update(transientInstance);
            log.debug("update successful");
        } catch (RuntimeException re) {
            log.error("update failed", re);
            throw re;
        }
    }

    public void delete(SdiCategory transientInstance) {
        log.debug("delete SdiOrganism instance");
        try {
            sessionFactory.getCurrentSession().delete(transientInstance);
            log.debug("delete successful");
        } catch (RuntimeException re) {
            log.error("delete failed", re);
            throw re;
        }
    }

    public void merge(SdiCategory transientInstance) {
        log.debug("merge SdiOrganism instance");
        try {
            sessionFactory.getCurrentSession().merge(transientInstance);
            log.debug("merge successful");
        } catch (RuntimeException re) {
            log.error("merge failed", re);
            throw re;
        }
    }

    public SessionFactory getSessionFactory() {
        return sessionFactory;
    }

    public void setSessionFactory(SessionFactory sessionFactory) {
        this.sessionFactory = sessionFactory;
    }

}
