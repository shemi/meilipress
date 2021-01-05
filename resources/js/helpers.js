import _ from "underscore";

export function createErrorsObject(form, deep = 0) {
    let errors = {};

    for (const key of Object.keys(form)) {
        let error = '';


        if(! Array.isArray(form[key]) && _.isObject(form[key]) && deep <= 1) {
            error = createErrorsObject(form[key], deep + 1);
        }

        errors[key] = error;
    }

    return errors;
}