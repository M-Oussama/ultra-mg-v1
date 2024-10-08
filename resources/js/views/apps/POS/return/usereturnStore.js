import { defineStore } from 'pinia'
import axios from '@axios'
import { successMiddleware } from '@/middlewares/successMiddleware';
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

export const useReturnStore = defineStore('ReturnStore', {
  actions: {
    // 👉 Fetch products data
    fetchReturns(params) { return axios.get('/api/returns/list', { params }) },

    fetchBenefits(params) { return axios.get('/api/pos/benefits/list', { params }) },

    getArticlesBenefit(id) { return axios.get('/api/pos/benefits/'+id) },

    getPriceHistory(clientId, productId) { return axios.get('/api/pos/sales/getPriceHistory/'+clientId+'/'+productId) },
    // 👉 Fetch products data
    getLastID(params) { return axios.get('/api/certifyInvoices/getLastID', { params }) },

    // 👉 Fetch products data
    fetchData(params) { return axios.get('/api/returns/getData', { params }) },

    getClientsPerCity(cityId) { return axios.get('/api/clients/getClientsPerCity/'+cityId) },
    // 👉 Add Product
    storeReturn(data) {
      const { client_id, date, amount, payment_type, products} = data;
      return new Promise((resolve, reject) => {
        axios.post('/api/returns/store', {
            data,
        }).then(response => {

          resolve(response)
        })
          .catch(error => {

            reject(error)

          })
      })
    },

    updateReturn(data) {
      const { id} = data;
      return new Promise((resolve, reject) => {
        axios.post('/api/returns/update/'+id, {
            data,
        }).then(response => {

          resolve(response)
        })
          .catch(error => {

            reject(error)

          })
      })
    },

    deleteReturn(sale){

      return new Promise((resolve, reject) => {
        axios.post('/api/returns/delete', {
          sale,
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


    fetchReturn(id) {
      return axios.get(`/api/returns/getReturn/${id}`)
    },

    fetchPayments(params){
      return axios.get('/api/pos/sales/payments/list', { params })
    },
    getReturnData (id) {
      return axios.get(`/api/returns/getReturnData/${id}`)
    },

    // 👉 fetch single product
    deleteProduct(productData) {
      console.log(productData)
      const { id } = productData
      return new Promise((resolve, reject) => {
        axios.delete('/api/certifyInvoices/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    addPayment(payment,saleid){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/payment/create/'+saleid, {
          payment,
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },

    storePayment(payment){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/payment/create', {
          payment,
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },

    updatePayment(payment){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/payment/update', {
          payment,
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },

    deletePayment(payment){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/payment/delete', {
          payment,
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },
    storeBenefit(data) {
      const { month, year} = data;
      return new Promise((resolve, reject) => {
        axios.post('/api/pos/benefits/store', {
          data,
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },
  },



})
