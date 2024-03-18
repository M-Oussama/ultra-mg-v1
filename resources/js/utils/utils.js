import {defineStore} from "pinia/dist/pinia";

// Define a helper function to remove nesting from params object
export function flattenParams(params) {
    const flattenedParams = {};
    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            flattenedParams[key] = params[key];
        }
    }
    return flattenedParams;
}
