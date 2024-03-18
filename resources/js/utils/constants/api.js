// api.js

const baseUrl = "http://127.0.0.1:8000";

const apiURl = baseUrl + '/api';

// Define your API endpoints as constants
const API_ENDPOINTS = {
    GET_SUPPLIERS: `${apiURl}/suppliers/list`,
    CREATE_SUPPLIER: `${apiURl}/suppliers/create`,
    UPDATE_SUPPLIER: `${apiURl}/suppliers/update`,
    DELETE_SUPPLIER: `${apiURl}/suppliers/delete`,

};

// Export the API endpoints array
export default API_ENDPOINTS;
