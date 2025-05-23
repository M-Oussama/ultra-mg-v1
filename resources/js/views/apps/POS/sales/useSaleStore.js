import { defineStore } from 'pinia'
import axios from '@axios'
import { successMiddleware } from '@/middlewares/successMiddleware';
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

export const useSaleStore = defineStore('SaleStore', {
  actions: {
    // 👉 Fetch products data
    fetchSales(params) { return axios.get('/api/pos/sales/list', { params }) },

    fetchBenefits(params) { return axios.get('/api/pos/benefits/list', { params }) },

    getArticlesBenefit(id) { return axios.get('/api/pos/benefits/'+id) },

    getPriceHistory(clientId, productId) { return axios.get('/api/pos/sales/getPriceHistory/'+clientId+'/'+productId) },
    // 👉 Fetch products data
    getLastID(params) { return axios.get('/api/certifyInvoices/getLastID', { params }) },

    // 👉 Fetch products data
    fetchData(params) { return axios.get('/api/pos/sales/getData', { params }) },

    getClientsPerCity(cityId) { return axios.get('/api/clients/getClientsPerCity/'+cityId) },
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

    deleteSale(sale){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/delete', {
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


    fetchSale(id) {
      return axios.get(`/api/pos/sale/getSale/${id}`)
    },

    fetchPayments(params){
      return axios.get('/api/pos/sales/payments/list', { params })
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
    storePaymentWithInvoices(payment, notpaidInvoices, paidInvoices){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/payment/create', {
          payment,notpaidInvoices,paidInvoices
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
    updatePaymentWithInvoices(payment, paidInvoices){

      return new Promise((resolve, reject) => {
        axios.post('/api/pos/sales/payment/update', {
          payment,
          paidInvoices
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

  updatePickup(sale, isChecked){

      return new Promise((resolve, reject) => {
        axios.post(`/api/sales/${sale.id}/toggle-pickup`, {
            picked_up: isChecked
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
    updateBenefit(benefit) {
      return new Promise((resolve, reject) => {
        axios.post('/api/pos/benefits/charges/update/'+benefit.id, {
          benefit
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },
    updateProfit(data , benefit, id) {
      return new Promise((resolve, reject) => {
        axios.post('/api/pos/benefits/update/'+id, {
          data,
          benefit
        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },
    refreshData(id) {
      return new Promise((resolve, reject) => {
        axios.get('/api/pos/benefits/refresh/'+id, {

        }).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },

    deleteBenefit(id){

      return new Promise((resolve, reject) => {
        axios.delete('/api/pos/benefits/delete/'+id).then(response => {

          resolve(response)
        })
            .catch(error => {

              reject(error)

            })
      })
    },
    getClientInvoices(id){ return axios.get('/api/pos/client/'+id+'/sales') },

    getPaidInvoices(id, payment_id){ return axios.get('/api/pos/client/'+id+'/sales/'+payment_id+'/paid') },
  },



})
