import { defineStore } from 'pinia'
import axios from '@axios'
import { successMiddleware } from '@/middlewares/successMiddleware';
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

export const useCertifyInvoiceListStore = defineStore('CertifyInvoiceListStore', {
  actions: {
    // 👉 Fetch products data
    fetchCertifyInvoices(params) { return axios.get('/api/certifyInvoices/list', { params }) },
    // 👉 Fetch products data
    getLastID(params) { return axios.get('/api/certifyInvoices/getLastID', { params }) },

    // 👉 Fetch products data
    fetchData(params) { return axios.get('/api/certifyInvoices/getData', { params }) },

    // 👉 Add Product
    addCertifyInvoice(invoiceData) {
      const { client_id, date, amount, payment_type, products} = invoiceData;
      return new Promise((resolve, reject) => {
        axios.post('/api/certifyInvoices/store', {
          invoiceData,
        }).then(response => {

          resolve(response)
        })
          .catch(error => {

            reject(error)

          })
      })
    },

    // 👉 Update Product
    updateProduct(productData) {
      const { id, name, brand, description, product_code, SKU, min_stock_level, price, stockable, tax_rate } = productData;
      return new Promise((resolve, reject) => {
        let requestData = {
          name,
          brand,
          description,
          product_code,
          SKU,
          min_stock_level,
          price,
          stockable,
          tax_rate
        };
        axios.post('/api/certifyInvoices/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchInvoice(id) {
      return axios.get(`/api/certifyInvoices/${id}`)

    },

    // 👉 fetch single product
    deleteProduct(productData) {
      console.log(productData)
      const { id } = productData
      return new Promise((resolve, reject) => {
        axios.delete('/api/certifyInvoices/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },


})
