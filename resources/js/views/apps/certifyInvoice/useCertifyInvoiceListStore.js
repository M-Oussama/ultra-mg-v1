import { defineStore } from 'pinia'
import axios from '@axios'

export const useCertifyInvoiceListStore = defineStore('CertifyInvoiceListStore', {
  actions: {
    // 👉 Fetch products data
    fetchCertifyInvoices(params) { return axios.get('/api/certifyInvoices/list', { params }) },

    // 👉 Add Product
    addCertifyInvoice(invoiceData) {
      const { client_id, date, amount, payment_type, products} = invoiceData;
      return new Promise((resolve, reject) => {
        axios.post('/api/certifyInvoices/store', {
          client_id,
          date,
          amount,
          payment_type,
          products,
        }).then(response => resolve(response))
          .catch(error => reject(error))
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


    fetchProduct(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/apps/certifyInvoices/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
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
