<script setup>
import InvoiceProductEdit from '@/views/apps/POS/sales/InvoiceProductEdit.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

const props = defineProps({
  data: {
    type: null,
    required: true,
  },
  loading:{
    type: null,
    required: true,
  }

})
// 👉 Clients
const saleStore = useSaleStore()

const isDialogVisible = ref(false)
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
const clients = ref([])
const companyProfile = ref({
  name:'EURL SETIFIS DETERGENTS',
  address: 'LOT N, 6 GROUPE 51, ZONE INDUSTRIELLE, 34 SECTION, Ksar El Abtal 19220',
  address2: 'KASR EL ABTAL 19220, SETIF, ALGERIE',
  phone1:'+213 0668 15 41 45',
  phone2:'+213 0668 15 41 45',

})

let selectedItem = ref({
  quantity:0,
  price:0,
  total:0,
  product:{
    id: -1,
    name: 'Select a product',
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
  }
})


const defaultSelectedItem = ref({
  quantity:0,
  price:0,
  total:0,
  product:{
    id: -1,
    name: 'Select a product',
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
  }
})

const totalAmount = data => {
  computeTotal()
}

const priceHistory = data => {
console.log(props.data.client.isEmpty)
  if(props.data.client.id !== undefined && props.data.client.id > -1) {

    saleStore.getPriceHistory(data.id,props.data.client.id).then(response => {
      priceHistoryList.value = response.data.products
      console.log(priceHistoryList.value)
      isDialogVisible.value = true

      console.log(response);
    }).catch(err => {
      console.log(err)
    })
  } else {
    errorsMiddleware('Error',' You must select a client first')
  }

}
const priceHistoryList = ref([])
const productFullName = item => {
  return `${item.product.name}`
}

function computeTotal() {
  let totalPrices = props.data.sale_items.map(item => item.price * item.quantity);
  totalInvoice.value = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  props.data.total = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  props.data.total_amount = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  paymentActive()
}

// 👉 Add item function
const addItem = () => {
  if(selectedItem.value !== undefined && selectedItem.value.product !== undefined && selectedItem.value.product.id !== -1){
    console.log(selectedItem.value.product.id)
    const index = props.data.sale_items.findIndex(p =>
      p.product.id ===
      selectedItem.value.product.id);

    if(index ===-1 && selectedItem.value.product.id !== -1) {
      props.data.sale_items.push({...selectedItem.value})
    }

    computeTotal()
  }


}

watch(selectedItem , ()=> {

 // handleProductChange()
})

const removeProduct = Item => {
  const index = props.data.sale_items.findIndex(p => p.product.id === Item.product.id);
  console.log("index: "+index);
  props.data.sale_items.splice(index,1);
  computeTotal()

}

const onChange = item => {

  var client = getItemById(item)

  props.data.client = {...client}
}
const getItemById = (id) => {
  for (let i = 0; i <props.data.clients.length ; i++) {
    if(props.data.clients[i].id == id)
      return props.data.clients[i]
  }
  return null
}
const fullName = item => {

  return `${item.name}  ${item.surname}`
}

const handleProductChange = (value) => {
  addItem()
  selectedItem.value = {...defaultSelectedItem.value}

}

const paymentActive = () => {
  if(props.data.payment) {
    props.data.paymentAmount = totalInvoice.value
  } else {
    props.data.paymentAmount = 0
  }

}

const onCityChanged = (item) => {
  props.data.client_id = null
  saleStore.getClientsPerCity(props.data.city).then(response => {
    props.data.clients = response.data.clients
  }).catch(err => {
    console.log(err)
  })

}

const onProductChanged = (item) => {
  const foundProduct = props.data.products.find(product => product.id === item);
  selectedItem.value = {...foundProduct};
  handleProductChange()
}

const onClearProduct = (item) => {
  console.log(item)
}


</script>

<template>
  <VCard>

    <VDialog
      v-model="isDialogVisible"
      persistent
      class="v-dialog-sm"
    >
      <!-- Dialog Activator -->
      <template #activator="{ props }">
        <VBtn v-bind="props">
          Open Dialog
        </VBtn>
      </template>

      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />

      <!-- Dialog Content -->
      <VCard title="Price History ">
        <VTable class="text-no-wrap pt-5">
          <thead>
          <tr>
            <th class="text-uppercase">
              Product
            </th>
            <th class="text-uppercase">
              Date
            </th>
            <th class="text-uppercase">
              Quantity
            </th>
            <th class="text-uppercase">
              Price
            </th>

          </tr>
          </thead>

          <tbody>
          <tr
            v-for="item in priceHistoryList"
            :key="item.id"
          >
            <td>
              {{ item.product.name }}
            </td>
            <td>
              {{ item.sale_date }}
            </td>
            <td>
              {{ item.quantity }}
            </td>
            <td>
              {{ item.price }} DZD
            </td>

          </tr>
          </tbody>
        </VTable>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">

          <VBtn @click="isDialogVisible = false">
            Close
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
    <!-- SECTION Header -->
    <!--  eslint-disable vue/no-mutating-props -->
    <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row">
      <!-- 👉 Left Content -->
      <VRow>
        <VCol
          cols="12"
          md="7">
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
        </VCol>
        <VCol
          cols="12"
          md="5">
          <div class="mt-4 ma-sm-4">
            <!-- 👉 Invoice Id -->
            <h6 class="d-flex align-center font-weight-medium justify-sm-end text-xl mb-3">
              <span class="me-3">Invoice</span>

              <span>
            <VTextField
              v-model="props.data.id"
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
              v-model="props.data.sale_date"
              density="compact"
              placeholder="YYYY-MM-DD"
              style="width: 8.9rem;"
              :config="{ position: 'auto right' }"

            />
          </span>
            </p>

          </div>
        </VCol>
      </VRow>


      <!-- 👉 Right Content -->

    </VCardText>
    <!-- !SECTION -->

    <VDivider />

    <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row">

      <VRow>
        <VCol
          cols="12"
          md="12"
        >
          <VRow>

            <VCol
              cols="12"
              md="6">
              <div class="align-center mb-6">
                <h6 class="text-sm font-weight-medium mb-3">
                  City:
                </h6>
                <VAutocomplete
                  clearable
                  v-model="props.data.city"
                  :items="props.data.cities"
                  item-value="id"
                  item-title="name"
                  label="City"
                  @update:modelValue="onCityChanged"
                />

              </div>
            </VCol>
            <VCol
              cols="12"
              md="6">
              <div class="align-center mb-6">
                <h6 class="text-sm font-weight-medium mb-3">
                  Invoice To:
                </h6>
                <VAutocomplete
                  clearable
                  v-model="props.data.client_id"
                  :items="props.data.clients"
                  item-value="id"
                  item-title="full_name"
                  label="Client"
                  @update:modelValue="onChange"
                >
                </VAutocomplete>


              </div>
            </VCol>
          </VRow>


        </VCol>

      </VRow>



    </VCardText>

    <VDivider />

    <!-- 👉 Add purchased products -->
    <VCardText class="add-products-form">
      <div class="mt-4 ma-sm-4">
        <VRow>
          <VCol
            cols="12"
            md="12"
          >

            <span class="text-sm-caption mb-5">Products</span>
<!--            <model-list-select-->
<!--              :list="props.data.products"-->
<!--              v-model="selectedItem"-->
<!--              option-value="id"-->
<!--              :custom-text="productFullName"-->
<!--              placeholder="Select Product"-->
<!--            >-->

<!--            </model-list-select>-->

            <VAutocomplete
              class="mt-4"
              clearable
              v-model="selectedItem"
              :items="props.data.products"
              item-value="id"
              :item-title="productFullName"
              label="Products"

              @update:modelValue="onProductChanged"
            >
            </VAutocomplete>

          </VCol>
          <VCol
            cols="12"
            md="2"
          >
            <!--            <VBtn @click="addItem">-->
            <!--              Add Item-->
            <!--            </VBtn>-->
          </VCol>

        </VRow>
      </div>
      <div class="mt-7 ma-sm-7">

      </div>
      <div
        v-for="(_product, index) in props.data.sale_items"
        :key="index"
        class="ma-sm-4"
      >
        <InvoiceProductEdit
          :id="_product.id"
          :data="_product"
          @remove-product="removeProduct"
          @total-amount="totalAmount"
          @price-history="priceHistory"
        />
      </div>
    </VCardText>

    <VDivider />

    <!-- 👉 Total Amount -->
    <VCardText class="d-flex justify-space-between flex-wrap flex-column flex-sm-row">

      <VRow>
        <VCol cols="12"
              md="5">

          <div  class="mx-sm-4 my-2">
            <div class="align-center mb-4">
              <div class="d-flex">
                  <span class="text-sm font-weight-semibold me-2 mb-2 pt-2">
                    Payments:

                  </span>
                <VSwitch
                  v-model="props.data.payment"
                  :true-value="1"
                  :false-value="0"
                  @change="paymentActive"
                />
              </div>

              <VTextField

                v-if="props.data.payment"
                v-model="props.data.paymentAmount"
                type="number"

              />
            </div>
          </div>
        </VCol>
        <VCol
          cols="12"
          md="3"
        >


        </VCol>

        <VCol cols="12"
              md="4">


          <div class="my-2 mx-sm-4 display-center">
            <table>
              <tr>
                <td>
                  <div class="me-5 text-start">
                    <p class="mb-2 text-sm">
                      Total Invoice
                    </p>
                    <p class="mb-2 text-sm">
                      Payment:
                    </p>
                    <p class="mb-2 text-sm">
                      Rest:
                    </p>

                  </div>
                </td>

                <td class="font-weight-semibold">
                  <p class="mb-2 text-sm">
                    {{totalInvoice.toFixed(2)}} DZD
                  </p>
                  <p class="mb-2 text-sm">
                    {{props.data.paymentAmount ? props.data.paymentAmount : 0.00}} DZD
                  </p>
                  <p class="mb-2 text-sm">
                    {{((props.data.paymentAmount ? props.data.paymentAmount : 0) - totalInvoice).toFixed(2)}} DZD
                  </p>

                </td>
              </tr>
            </table>
          </div>
        </VCol>
      </VRow>



    </VCardText>

    <VDivider />
    <VCol
      cols="12"
      md="12">
      <div class="align-center mb-6">
        <h6 class="text-sm font-weight-medium mb-3">
          Driver:
        </h6>
        <VAutocomplete
          clearable
          v-model="props.data.driver_id"
          :items="props.data.drivers"
          item-value="id"
          item-title="full_name"
          label="Driver"

        >
        </VAutocomplete>


      </div>
    </VCol>
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
.ant-select-clear{
  top: 40% !important;
  font-size:larger !important;
}
.selection{
  border-radius: 6px !important;
  padding: 11px !important;
  background-color:#ffffff87 !important;
  padding-bottom: 14px !important;
}
.display-center {
  display: grid;
  text-align: center;
}
</style>
