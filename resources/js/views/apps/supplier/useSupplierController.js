import { defineStore } from 'pinia'
import axios from '@axios'
import API_ENDPOINTS from "@/utils/constants/api";
import {flattenParams} from "@/utils/utils";

export const useSupplierController = defineStore('useSupplierController', {
  actions: {
    // 👉 Fetch users data
    getSuppliers(params) {return axios.post(API_ENDPOINTS.GET_SUPPLIERS, flattenParams(params))},

    createSupplier(params) {return axios.post(API_ENDPOINTS.CREATE_SUPPLIER, flattenParams(params))},

    updateSupplier(params) {return axios.post(API_ENDPOINTS.UPDATE_SUPPLIER, flattenParams(params))},

    deleteSupplier(params) {return axios.post(API_ENDPOINTS.DELETE_SUPPLIER, flattenParams(params))},
  },
})
