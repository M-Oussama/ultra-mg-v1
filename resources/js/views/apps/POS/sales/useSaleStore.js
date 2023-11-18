import { defineStore } from 'pinia'
import axios from '@axios'
import { successMiddleware } from '@/middlewares/successMiddleware';
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

export const useSaleStore = defineStore('SaleStore', {
  actions: {
    // 👉 Fetch products data
    fetchSales(params) { return axios.get('/api/pos/sales/list', { params }) },
    // 👉 Fetch products data
    getLastID(params) { return axios.get('/api/certifyInvoices/getLastID', { params }) },

    // 👉 Fetch products data
    fetchData(params) { return axios.get('/api/pos/sales/getData', { params }) },

    // 👉 Add Product
    storeSale(data) {
      const { client_id, date, amount, payment_type, products} = data;
      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/store', {
            data,
        }).then(response => {

          resolve(response)
        })
          .catch(error => {

            reject(error)

          })
      })
    },

    updateSale(data) {
      const { id} = data;
      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/update/'+id, {
            data,
        }).then(response => {

          resolve(response)
        })
          .catch(error => {

            reject(error)

          })
      })
    },

    // 👉 Update Product
    updateCertifyInvoice(invoiceData) {
      const { id } = invoiceData;
      return new Promise((resolve, reject) => {
        axios.post('/api/certifyInvoices/update/' + id,{invoiceData} )
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchSale(id) {
      return axios.get(`/api/pos/sale/getSale/${id}`)
    },
    getSaleData(id) {
      return axios.get(`/api/pos/sale/getSaleData/${id}`)
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
