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
package org.easysdi.extract.web.validators;

import org.easysdi.extract.web.model.PluginItemModelParameter;
import org.springframework.util.StringUtils;
import org.springframework.validation.Errors;
import org.springframework.validation.ValidationUtils;



/**
 * An object ensuring that a parameter for the model of an object that uses a plugin contains valid
 * information.
 *
 * @author Yves Grasset
 */
public class PluginItemModelParameterValidator extends BaseValidator {

    /**
     * Determines if a given class can be checked by this validator.
     *
     * @param type the class of the object to validate
     * @return <code>true</code> if the type is supported
     */
    @Override
    public final boolean supports(final Class<?> type) {
        return PluginItemModelParameter.class.equals(type);
    }



    /**
     * Checks the conformity of the parameter information.
     *
     * @param target the parameter to validate
     * @param errors the object that assembles the validation errors for the parameter
     */
    @Override
    public final void validate(final Object target, final Errors errors) {
        final PluginItemModelParameter parameter = (PluginItemModelParameter) target;
        final Object[] nameParams = new Object[]{
            parameter.getName()
        };

        ValidationUtils.rejectIfEmptyOrWhitespace(errors, "label", "parameter.errors.label.empty",
                nameParams);
        ValidationUtils.rejectIfEmptyOrWhitespace(errors, "type", "parameter.errors.type.empty",
                nameParams);

        if (!parameter.getType().equals("boolean") && parameter.getMaxLength() < 1) {
            errors.rejectValue("maxLength", "parameter.errors.maxLength.negative", nameParams,
                    "parameter.errors.generic");
        }

        final Object[] labelParams = new Object[]{
            parameter.getLabel()
        };

        final Object value = parameter.getValue();

        if (parameter.isRequired() && value == null) {
            errors.rejectValue("value", "parameter.errors.required", labelParams,
                    "parameter.errors.generic");
        }

        if (!(value instanceof String) || parameter.getType().equals("boolean")) {
            return;
        }

        final String stringValue = (String) value;

        if (parameter.isRequired() && !StringUtils.hasText(stringValue)) {
            errors.rejectValue("value", "parameter.errors.required", labelParams,
                    "parameter.errors.generic");
        }

        if (stringValue.length() > parameter.getMaxLength()) {
            errors.rejectValue("value", "parameter.errors.tooLong", new Object[]{
                parameter.getLabel(),
                parameter.getMaxLength()
            }, "parameter.errors.generic");
        }

        if (parameter.getType().equals("email") && !parameter.validateUpdatedValue(stringValue)) {
            errors.rejectValue("value", "parameter.errors.invalidEmailString", labelParams, "parameter.errors.generic");
        }
    }

}
