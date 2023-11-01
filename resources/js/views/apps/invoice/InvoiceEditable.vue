<script setup>
import InvoiceProductEdit from './InvoiceProductEdit.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'

const props = defineProps({
  data: {
    type: null,
    required: true,
  },
})

const certifyInvoiceListStore = useCertifyInvoiceListStore()

// 👉 Clients
const products = ref([])
let availableproducts = ref([])
let placeholder_value = ref('Search for a Product')
const  added_products = ref([])
const selectedProducts = ref([])
let totalInvoice = ref(0)
const product = ref({
  id: -1,
  name: '',
  brand: '',
  description: '',
  product_code: '',
  SKU: '',
  price: 0,
  stockable: false,
  tax_rate: 0,
  product_stock: {
    quantity : 0,
  }
})
const client = ref([])
const companyProfile = ref({
  name:'EURL SETIFIS DETERGENTS',
  address: 'LOT N, 6 GROUPE 51, ZONE INDUSTRIELLE, 34 SECTION, Ksar El Abtal 19220',
  address2: 'KASR EL ABTAL 19220, SETIF, ALGERIE',
  phone1:'+213 0668 15 41 45',
  phone2:'+213 0668 15 41 45',

})
const selectedItem = ref({
  id: -1,
  name: '',
  brand: '',
  description: '',
  product_code: '',
  SKU: '',
  price: 0,
  stockable: false,
  tax_rate: 0,
  product_stock: {
    quantity : 0,
  }
})

// 👉 fetchClients
certifyInvoiceListStore.fetchData().then(response => {
  products.value = response.data.products
  //companyProfile.value = response.data.companyProfile

  availableproducts.value = Array.from(products.value);
}).catch(err => {
  console.log(err)
})

function findIndexById(selectedProducts, selectedItemId) {
  for (let i = 0; i < selectedProducts.value.length; i++) {
    if (selectedProducts.value[i].value ? selectedProducts.value[i].value.id : selectedProducts.value[i]._rawValue.id=== selectedItemId) {
      return i; // Return the index if the item is found
    }
  }
  return -1; // Return -1 if the item is not found
}

const totalAmount = data => {
  updateValueInsideArray(data)
  computeTotal()
}

function updateValueInsideArray(data) {
  const index = findIndexById(added_products, data._value.id);

  if(index !== -1) {
    added_products._value[index]._value.price = data._value.price
  }
}

function computeTotal() {
// Calculate total price for each product (price * quantity)
  let totalPrices = added_products._value.map(product => product._value.price * product._value.product_stock.quantity);

// Calculate the overall total by summing up the total prices using reduce
  totalInvoice.value = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
}

// 👉 Add item function
const addItem = () => {

  const index_new_selected = findIndexById(selectedProducts, selectedItem.value.id);
  const index_available_selected = availableproducts.value.findIndex(p => p.id === selectedItem.value.id)

  if(products.value.length >= added_products.value.length && selectedItem.value.id !== -1 && index_new_selected ===-1) {
    added_products.value.push({...selectedItem});
    selectedProducts.value.push({...selectedItem});
    if(index_available_selected !== -1){
      availableproducts.value.splice(index_available_selected,1);
      placeholder_value.value = "Search for a Product";
    }

    computeTotal()

  }
}

watch(selectedItem , ()=> {
  placeholder_value.value =  selectedItem.value.name ;
})

const removeProduct = Item => {
  const index_available_selected = availableproducts.value.findIndex(p => p.id === Item._rawValue.id);
  const index_selected_selected = findIndexById(selectedProducts, Item._rawValue.id);
  const index_added_product = findIndexById(added_products, Item._rawValue.id);

  if(index_available_selected === -1) {
    availableproducts.value.push({...Item._rawValue});
  }
  if(index_selected_selected !== -1) {
    selectedProducts.value.splice(index_selected_selected,1);
  }
  if(index_added_product !== -1) {
    added_products.value.splice(index_added_product,1);
    computeTotal()
  }
}

const fullName = item => {
  return `${item.name}  ${item.surname}`
}

const productfullName = item => {
  return `${item.name}`
}

</script>

<template>
  <VCard>
    <!-- SECTION Header -->
    <!--  eslint-disable vue/no-mutating-props -->
    <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row">
      <!-- 👉 Left Content -->
      <div class="ma-sm-4">
        <div class="d-flex align-center mb-6">
          <!-- 👉 Logo -->
          <VNodeRenderer
            :nodes="themeConfig.app.logo"
            class="me-3"
          />

          <!-- 👉 Title -->
          <h6 class="font-weight-bold text-xl">
            {{ companyProfile.name }}
          </h6>
        </div>

        <!-- 👉 Address -->
        <p class="mb-0">
          {{ companyProfile.address }}
        </p>
        <p class="mb-0">
          {{ companyProfile.address2 }}
        </p>

        <p class="mb-0">
          {{companyProfile.phone1}} , {{ companyProfile.phone2 }}
        </p>
      </div>

      <!-- 👉 Right Content -->
      <div class="mt-4 ma-sm-4">
        <!-- 👉 Invoice Id -->
        <h6 class="d-flex align-center font-weight-medium justify-sm-end text-xl mb-3">
          <span class="me-3">Invoice</span>

          <span>
            <VTextField
              v-model="props.data.invoice.id"
              disabled
              prefix="#"
              density="compact"
              style="width: 8.9rem;"
            />
          </span>
        </h6>

        <!-- 👉 Issue Date -->
        <p class="d-flex align-center justify-sm-end mb-3">
          <span class="me-3">Date Issued</span>

          <span>
            <AppDateTimePicker
              v-model="props.data.invoice.date"
              density="compact"
              placeholder="YYYY-MM-DD"
              style="width: 8.9rem;"
              :config="{ position: 'auto right' }"
            />
          </span>
        </p>

      </div>
    </VCardText>
    <!-- !SECTION -->

    <VDivider />

    <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row gap-4">
      <div
        class="ma-sm-6"
        style="width:45%;"
      >
        <h6 class="text-sm font-weight-medium mb-3">
          Invoice To:
        </h6>
        <model-list-select
          :list="props.data.clients"
           v-model="props.data.invoice.client"
           option-value="id"
           :custom-text="fullName"
           placeholder="Select Client">
        </model-list-select>
      </div>

      <div class="ma-sm-2" style="width:45%;">
        <h6 class="text-sm font-weight-medium mb-3">
          Bill To:
        </h6>

        <table>
          <tbody>
          <tr>
            <td class="pe-6">
              Invoice:
            </td>
            <td class="font-weight-semibold">
              FAJ/2023/{{ props.data.invoice.id }}
            </td>
          </tr>
          <tr>
            <td class="pe-6">
              Payment Method:
            </td>
            <td>{{ props.data.invoice.selectedPaymentMethod }}</td>
          </tr>
            <tr>
              <td class="pe-6">
               Client:
              </td>
              <td class="font-weight-semibold">
                {{ props.data.invoice.client.name }} {{props.data.invoice.client.surname}}
              </td>
            </tr>
            <tr>
              <td class="pe-6">
                Address:
              </td>
              <td>{{ props.data.invoice.client.address }}</td>
            </tr>


            <tr>
              <td class="pe-6">
                N°RC:
              </td>
              <td>{{ props.data.invoice.client.NRC }}</td>
            </tr>
             <tr>
              <td class="pe-6">
                N°IF:
              </td>
              <td>{{ props.data.invoice.client.NIF }}</td>
            </tr>
          <tr>
              <td class="pe-6">
                N°IS:
              </td>
              <td>{{ props.data.invoice.client.NIS }}</td>
            </tr>
          <tr>
              <td class="pe-6">
                N°ART:
              </td>
              <td>{{ props.data.invoice.client.NART }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </VCardText>

    <VDivider />

    <!-- 👉 Add purchased products -->
    <VCardText class="add-products-form">
      <div class="mt-4 ma-sm-4">
        <VRow>
          <VCol
            cols="12"
            md="10"
          >
            <model-list-select
              :list="availableproducts"
              v-model="selectedItem"
              option-value="name"
              :custom-text="productfullName"
              :placeholder="placeholder_value">
            </model-list-select>
          </VCol>
          <VCol
            cols="12"
            md="2"
          >
            <VBtn @click="addItem">
              Add Item
            </VBtn>
          </VCol>

        </VRow>
      </div>
      <div class="mt-7 ma-sm-7">

      </div>
      <div
        v-for="(_product, index) in added_products"
        :key="index"
        class="ma-sm-4"
      >


        <InvoiceProductEdit
          :id="_product.id"
          :data="_product"
          @remove-product="removeProduct"
          @total-amount="totalAmount"
        />
      </div>


    </VCardText>

    <VDivider />

    <!-- 👉 Total Amount -->
    <VCardText class="d-flex justify-space-between flex-wrap flex-column flex-sm-row">
      <div class="mx-sm-4 my-2">
        <div class="d-flex align-center mb-4">
          <h6 class="text-sm font-weight-semibold me-2">
            Salesperson:
          </h6>
          <VTextField
            v-model="props.data.salesperson"
            style="width: 10rem;"
          />
        </div>

        <VTextField
          v-model="props.data.thanksNote"
          placeholder="Thanks for your business"
        />
      </div>

      <div class="my-2 mx-sm-4">
        <table>
          <tr>
            <td class="text-end">
              <div class="me-5">
                <p class="mb-2">
                  Montant HT:
                </p>
                <p class="mb-2">
                  TVA 19%:
                </p>
                <p class="mb-2">
                  Montant TTC:
                </p>

              </div>
            </td>

            <td class="font-weight-semibold">
              <p class="mb-2">
                {{totalInvoice.toFixed(2)}} DZD
              </p>
              <p class="mb-2">
                {{(totalInvoice*0.19).toFixed(2)}} DZD
              </p>
              <p class="mb-2">
                {{(totalInvoice*1.19).toFixed(2)}} DZD
              </p>

            </td>
          </tr>
        </table>
      </div>
    </VCardText>

    <VDivider />

    <VCardText class="mx-sm-4">
      <p class="font-weight-semibold mb-2">
        Note:
      </p>
      <VTextarea
        v-model="props.data.note"
        :rows="2"
      />
    </VCardText>
  </VCard>
</template>
<style>
select option:first-child {
  font-size: 7pt;
}
.ui.dropdown {
  font-size: large;
}
.ui.dropdown .menu>.item {
  font-size: large;
}
.ui.search.selection.dropdown>input.search {
  font-size: large;
}
</style>
