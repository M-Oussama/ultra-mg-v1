import { defineStore } from 'pinia'
import axios from '@axios'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";

export const useProductListStore = defineStore('ProductListStore', {
  actions: {
    // 👉 Fetch products data
    fetchProducts(params) { return axios.get('/api/products/list', { params }) },

    // 👉 Add Product
    addProduct(productData) {
      const { name, brand, description, product_code, SKU, min_stock_level, price, stockable, tax_rate } = productData;
      return new Promise((resolve, reject) => {
        axios.post('/api/products/store', {
          name,
          brand,
          description,
          product_code,
          SKU,
          min_stock_level,
          price,
          stockable,
          tax_rate
        }).then(response => {

          successMiddleware('requests.product.success')
          resolve(response)
        })
          .catch(error => {
            errorsMiddleware(error);
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
        axios.post('/api/products/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchProduct(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/apps/products/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
    // 👉 fetch single product
    deleteProduct(productData) {
      console.log(productData)
      const { id } = productData
      return new Promise((resolve, reject) => {
        axios.delete('/api/products/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },

})
