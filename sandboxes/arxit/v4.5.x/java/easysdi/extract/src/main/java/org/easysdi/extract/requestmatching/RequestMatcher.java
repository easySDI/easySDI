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
package org.easysdi.extract.requestmatching;

import com.google.gson.Gson;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import java.lang.reflect.Field;
import java.util.List;
import java.util.Map;
import java.util.regex.Pattern;
import javax.script.ScriptEngine;
import javax.script.ScriptEngineManager;
import javax.script.ScriptException;
import org.easysdi.extract.domain.Request;
import org.easysdi.extract.domain.Rule;
import org.locationtech.jts.geom.Geometry;
import org.locationtech.jts.geom.GeometryFactory;
import org.locationtech.jts.io.ParseException;
import org.locationtech.jts.io.WKTReader;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.thymeleaf.util.StringUtils;



/**
 * A matcher that associates a request with a process through the rules of the connector that imported it.
 *
 * @author Florent Krin
 */
public class RequestMatcher {

    /**
     * The string that indicates a line return.
     */
    private static final String STRING_NEWLINE = "\r\n";

    /**
     * The string that indicates an AND operator.
     */
    private static final String OPERATOR_AND = " AND ";

    /**
     * The string that indicates an OR operator.
     */
    private static final String OPERATOR_OR = " OR ";

    /**
     * The string representation of the operator that determines if two geometries overlap.
     */
    private static final String GEOM_OPERATOR_INTERSECT = " INTERSECTS ";

    /**
     * The string representation of the operator that determines if one geometry is fully inside another.
     */
    private static final String GEOM_OPERATOR_CONTAINS = " CONTAINS ";

    /**
     * The string representation of the operator that determines if two geometries are fully separated.
     */
    private static final String GEOM_OPERATOR_DISJOINT = " DISJOINT ";

    /**
     * The string representation of the operator that determines if two geometries are the same.
     */
    private static final String GEOM_OPERATOR_EQUALS = " EQUALS ";

    /**
     * The string representation of the operator that determines if one geometry is inside another.
     */
    private static final String GEOM_OPERATOR_WITHIN = " WITHIN ";

    /**
     * The engine that evaluates the rules.
     */
    private ScriptEngine engine;

    /**
     * The writer to the application logs.
     */
    private final Logger logger = LoggerFactory.getLogger(RequestMatcher.class);

    /**
     * The data item request to match with a process.
     */
    private final Request request;



    /**
     * Creates a new request matcher instance.
     *
     * @param importedRequest the request to associate to a process
     */
    public RequestMatcher(final Request importedRequest) {
        this.request = importedRequest;
    }



    /**
     * Create a JavaScript engine instance with the parameters of the associated request.
     */
    private void initAssignements() {

        ScriptEngineManager factory = new ScriptEngineManager();
        this.engine = factory.getEngineByName("JavaScript");

        Class<Request> requestClass = Request.class;
        try {
            for (Field requestField : requestClass.getDeclaredFields()) {
                if (!java.lang.reflect.Modifier.isStatic(requestField.getModifiers())) {
                    requestField.setAccessible(true);
                    Object fieldValue = requestField.get(this.request);
                    requestField.setAccessible(false);

                    String assignement;

                    if (fieldValue instanceof String) {

                        if (this.isJSONValid(String.valueOf(fieldValue))) {
                            //la valeur du champ est un json : il faut donc faire les assignations pour tous les
                            // param�tres du json
                            Gson gson = new Gson();
                            engine.eval(requestField.getName().toUpperCase() + " = {}");
                            JsonObject jsonObject = gson.fromJson(String.valueOf(fieldValue), JsonObject.class);
                            //set assignements for json keys
                            for (Map.Entry<String, JsonElement> jsonItem : jsonObject.entrySet()) {
                                assignement = String.format("%s.%s = %s", requestField.getName(), jsonItem.getKey(),
                                        jsonItem.getValue());
                                engine.eval(assignement.toUpperCase());
                                System.out.println("set assignement : " + assignement.toUpperCase());
                            }

                        } else {
                            assignement = String.format("%s = \"%s\"", requestField.getName(),
                                    String.valueOf(fieldValue).replaceAll(STRING_NEWLINE, " "));
                            engine.eval(assignement.toUpperCase());
                            System.out.println("set assignement : " + assignement.toUpperCase());
                        }

                    } else if (fieldValue instanceof Integer || fieldValue instanceof Double) {
                        assignement = String.format("%s = %s", requestField.getName(), String.valueOf(fieldValue));
                        engine.eval(assignement.toUpperCase());
                        System.out.println("set assignement : " + assignement.toUpperCase());

                    } else if (fieldValue instanceof Boolean) {
                        assignement = String.format("%s = %s", requestField.getName().toUpperCase(),
                                String.valueOf(fieldValue));
                        engine.eval(assignement);
                        System.out.println("set assignement : " + assignement.toUpperCase());
                    }

                }

            }
        } catch (IllegalAccessException exc) {
            this.logger.error("Could not be access to a field in a request object.", exc);

        } catch (ScriptException exc) {
            this.logger.error("Could not evaluate an assignement for the request.", exc);
        }

    }



    /**
     * Checks if input string is JSON.
     *
     * @param jsonInString the string to check
     * @return <code>true</code> if the input is a valid JSON string
     */
    private boolean isJSONValid(final String jsonInString) {
        Gson gson = new Gson();

        if (jsonInString == null || jsonInString.equals("")) {
            return false;
        }

        try {
            gson.fromJson(jsonInString, JsonObject.class);
            return true;

        } catch (com.google.gson.JsonSyntaxException exception) {
            return false;
        }
    }



    /**
     * Reformats the rule so it can be parsed by the JavaScript engine.
     *
     * @param rule the rule to format
     * @return the formatted rule
     */
    private String reformatRule(final String rule) {

        return rule.replaceAll(OPERATOR_AND.toLowerCase(), " && ").replaceAll(OPERATOR_OR.toLowerCase(), " || ")
                .replaceAll(OPERATOR_AND, " && ").replaceAll(OPERATOR_OR, " || ");
    }



    /**
     * Evaluate the attributes and geographic criteria of a rule.
     *
     * @param rule the rule to evaluate
     * @return <code>true</code> if the request matches the rule
     */
    private Boolean evaluateRule(final String rule) {

        Pattern patternBoolOperator = Pattern.compile(GEOM_OPERATOR_CONTAINS + "|" + GEOM_OPERATOR_DISJOINT + "|"
                + GEOM_OPERATOR_EQUALS + "|" + GEOM_OPERATOR_INTERSECT + "|" + GEOM_OPERATOR_WITHIN);
        //split rule by logical operator
        String[] splittedRule = rule.split(OPERATOR_AND + "|" + OPERATOR_OR + "|" + OPERATOR_AND.toLowerCase() + "|"
                + OPERATOR_OR.toLowerCase());

        try {
            String finalRuleToEvaluate = rule;
            //Loop sub rule
            for (String subrule : splittedRule) {

                if (patternBoolOperator.matcher(subrule.toUpperCase()).find()) { //is an geographic filter
                    Boolean geomRuleMatched = this.evaluateGeographicCondition(subrule.trim().toUpperCase());
                    finalRuleToEvaluate = finalRuleToEvaluate.replace(subrule.trim(), geomRuleMatched.toString());

                } else {
                    Boolean attrRuleMatched = this.evaluateLogicalCondition(subrule.trim().toUpperCase());
                    finalRuleToEvaluate = finalRuleToEvaluate.replace(subrule.trim(), attrRuleMatched.toString());
                }
            }

            Boolean matched = (Boolean) this.engine.eval(this.reformatRule(finalRuleToEvaluate));
            this.logger.info(rule + " => " + matched);

            return matched;

        } catch (ScriptException exc) {
            this.logger.error("Could not match request with rule " + rule, exc);

        } catch (Exception exc) {
            this.logger.error("Could not match request with rule " + rule, exc);
        }

        return false;

    }



    /**
     * Evaluate a logical criterion.
     *
     * @param condition the logical expression to evaluate
     * @return <code>true</code> if the request matches the logical criterion
     */
    private Boolean evaluateLogicalCondition(final String condition) {

        try {
            Boolean matched = (Boolean) this.engine.eval(condition.toUpperCase());
            this.logger.info(condition + " => " + matched);

            return matched;

        } catch (ScriptException exc) {
            this.logger.error("Could not match request with rule " + condition, exc);

        } catch (Exception exc) {
            this.logger.error("Could not match request with rule " + condition, exc);
        }

        return false;
    }



    /**
     * Evaluate a geographic criterion.
     *
     * @param condition the geographic expression to evaluate
     * @return <code>true</code> if the request matches the geographic criterion
     */
    private Boolean evaluateGeographicCondition(final String condition) {

        Boolean matched = false;

        try {
            final GeometryFactory fact = new GeometryFactory();
            final WKTReader wktReader = new WKTReader(fact);
            final String[] splittedRule = condition.split(GEOM_OPERATOR_CONTAINS + "|" + GEOM_OPERATOR_DISJOINT + "|"
                    + GEOM_OPERATOR_EQUALS + "|" + GEOM_OPERATOR_INTERSECT + "|" + GEOM_OPERATOR_WITHIN);

            if (splittedRule.length == 2) {
                this.logger.info("Check matching with rule {}.", condition);
                final String fieldRule = splittedRule[0].trim();
                final String geomFilter = splittedRule[1].trim();
                final String fieldRuleValue = (String) this.engine.eval(fieldRule);
                final Geometry requestGeometry = wktReader.read(fieldRuleValue);
                final Geometry conditionGeometry = wktReader.read(geomFilter);

                if (condition.contains(GEOM_OPERATOR_CONTAINS)) {
                    matched = requestGeometry.contains(conditionGeometry);
                } else if (condition.contains(GEOM_OPERATOR_DISJOINT)) {
                    matched = requestGeometry.disjoint(conditionGeometry);
                } else if (condition.contains(GEOM_OPERATOR_EQUALS)) {
                    matched = requestGeometry.equals(conditionGeometry);
                } else if (condition.contains(GEOM_OPERATOR_INTERSECT)) {
                    matched = requestGeometry.intersects(conditionGeometry);
                } else if (condition.contains(GEOM_OPERATOR_WITHIN)) {
                    matched = requestGeometry.within(conditionGeometry);
                }

                if (matched) {
                    this.logger.info(condition + " => " + matched);
                }

            } else {
                this.logger.error("The syntaxe of the fowllowing rule is incorrect : " + condition);
            }

        } catch (ScriptException | ParseException exception) {
            this.logger.error("Could not match request with rule " + condition, exception);
        }

        return matched;
    }



    /**
     * Check if request match with at least one rule.
     *
     * @param rules a list of rules to match against the associated request
     * @return the first rule that matches the request, or <code>null</code> if no rule matches
     */
    public final Rule matchRequestWithRules(final List<Rule> rules) {

        this.logger.info("check request matching with rules");

        this.initAssignements();

        for (Rule rule : rules) {

            if (StringUtils.isEmpty(rule.getRule()) || !rule.isActive()) {
                continue;
            }
            //String condition = this.reformatRule(rule.getRule());
            this.logger.info("Check matching with rule at position {}.", rule.getPosition());

            if (this.evaluateRule(rule.getRule()).equals(Boolean.TRUE)) {
                this.logger.info("Request match with rule {}.", rule.getRule());
                return rule;
            }
        }

        return null;
    }

}
