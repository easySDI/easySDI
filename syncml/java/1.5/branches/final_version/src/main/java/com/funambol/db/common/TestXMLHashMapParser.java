/*
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * Copies of this file are distributed by Funambol as part of server-side
 * programs (such as Funambol Data Synchronization Server) installed on a
 * server and also as part of client-side programs installed on individual
 * devices.
 *
 * The following license notice applies to copies of this file that are
 * distributed as part of server-side programs:
 *
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the Honest Public License, as published by
 * Funambol, either version 1 or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY, TITLE, NONINFRINGEMENT or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the Honest Public License for more details.
 *
 * You should have received a copy of the Honest Public License
 * along with this program; if not, write to Funambol,
 * 643 Bair Island Road, Suite 305 - Redwood City, CA 94063, USA
 *
 * The following license notice applies to copies of this file that are
 * distributed as part of client-side programs:
 *
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY, TITLE, NONINFRINGEMENT or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307  USA
 */

package com.funambol.db.common;

import java.util.*;

/**
 *
 *
 * @version $Id: TestXMLHashMapParser.java,v 1.6 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class TestXMLHashMapParser {
    public static void main(String[] args) {
      testCompleto();
      //testToMap();
    }


    private static void testToMap() {
      String xml = "    <PRIMA_LETTERA>E</PRIMA_LETTERA>\n" +
  "    <CAP>20060</CAP>\n" +
  "    <CODICE CHARSET=\"UTF-8\">8</CODICE>\n" +
  "    <DEPOSITO_PREFERENZIALE>!!OBJECT_NULL!!</DEPOSITO_PREFERENZIALE>\n" +
  "    <FAX>02 950031</FAX>\n" +
  "    <PI>123450690</PI>\n" +
  "    <SITOWEB/>\n" +
  "    <INDIRIZZO CHARSET=\"UTF-8\">545, v. Il Caravaggio</INDIRIZZO>\n" +
  "    <EMAIL>info@azienda.it</EMAIL>\n" +
  "    <ID_SEGMENTO>0</ID_SEGMENTO>\n" +
  "    <CREDITO>0</CREDITO>\n" +
  "    <TELEFONO>02 950031</TELEFONO>\n" +
  "    <UP_DATE>2004-03-31 16:00:46.405</UP_DATE>\n" +
  "    <NOTE/>\n" +
  "    <ID_TIPO_PAGAMENTO>bonifico</ID_TIPO_PAGAMENTO>\n" +
  "    <NAZIONE>italia</NAZIONE>\n" +
  "    <RAGSOC>ELSIST srl</RAGSOC>\n" +
  "    <PROVINCIA>MI</PROVINCIA>\n" +
  "    <ID_COND_TRASPORTO>Porto Assegnato</ID_COND_TRASPORTO>\n" +
  "    <LOCALITA>Pozzuolo Martesana</LOCALITA>\n";


      try {
        Map map = XMLHashMapParser.toMap(xml);
        printMap(map);
      } catch (ParseException ex) {
        ex.printStackTrace();
      }
    }

    private static void testCompleto() {
      HashMap ht1 = new HashMap();
      HashMap ht1_1 = new HashMap();
      HashMap ht1_2 = new HashMap();

      Vector vctr = new Vector();

      ht1_1.put("campo_1_1", null);
      ht1_1.put("campo_2_1", null);
      ht1_1.put("campo_3_1", null);
      ht1_1.put("campo_4_1", "14\r\n31231");
      vctr.addElement(ht1_1);

      ht1_2.put("campo_1_2", "");
      ht1_2.put("campo_2_2", null);
      ht1_2.put("campo_3_2", "23   ");
      ht1_2.put("campo_4_2", "24 42");
      vctr.addElement(ht1_2);

      ht1.put("campo_1", "1");
      ht1.put("campo_2", "2");
      ht1.put("campo_3", "3");
      ht1.put("campo_4", "");
      ht1.put("campo_5_cplx", vctr);
      ht1.put("campo_6", "6");


      String sResult2 = XMLHashMapParser.toXML(ht1);

      System.out.println("Risultato primo toXML: \n" + sResult2);

      try {

        int numIt = 1;
        Map mp = null;
        long start = System.currentTimeMillis();
        for (int i = 0; i < numIt; i++) {
          mp = XMLHashMapParser.toMap(sResult2);
        }
        long end = System.currentTimeMillis();
        long exec = end - start;
        double media = exec / (double)numIt;

        System.out.println("Exec toMap " + numIt + " volte: " + exec + ", media: " + (media));

      } catch (Exception ex) {
        ex.printStackTrace();
      }

      String newXml = null;
      try {
        Map htResult = XMLHashMapParser.toMap(sResult2);

        int numIt = 1;
        Map mp = null;
        long start = System.currentTimeMillis();
        for (int i = 0; i < numIt; i++) {
          newXml = XMLHashMapParser.toXML(htResult);
        }
        long end = System.currentTimeMillis();
        long exec = end - start;
        double media = exec / (double)numIt;

        System.out.println("Exec toXML " + numIt + " volte: " + exec + ", media: " + (media));

      } catch (ParseException ex1) {
        ex1.printStackTrace();
      }

// confronto il primo xml con quello dopo la conversione
      if (sResult2.equals(newXml)) {
        System.out.println("Xml uguali");
      } else {
        System.out.println("Xml diversi!!!!");
      }

    }

    public static void printMap(Map ht) {
      Set keys = ht.keySet();
      Iterator it = keys.iterator();
      System.out.println("Contenuto hashMap\n----------------------------");
      while (it.hasNext()) {
        String key = (String)it.next();
        Object value = ht.get(key);
        System.out.println("key: " + key + ", value: " + value);
      }
      System.out.println("----------------------------");

    }

}
