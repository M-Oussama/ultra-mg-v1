<script setup>
import InvoiceProductEdit from '@/views/apps/POS/sales/InvoiceProductEdit.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
import JQuery from 'jquery'
let $ = JQuery
const content = ref('')
const readonly = ref(true)
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
  if(selectedItem.value !== undefined){
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

  handleProductChange()
})

const removeProduct = Item => {
  const index = props.data.sale_items.findIndex(p => p.product.id === Item.product.id);
  props.data.sale_items.splice(index,1);
  computeTotal()

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

const tableJSON = ref('')
const table = ref({

  thead: [
    'ID',
    'Product',
    'Price',
    'Quantity',
    'Total',
    'Actions',
  ],
  tbody: [
    ['ID',
      'Product',
      'Price',
      'Quantity',
      'Total',
      'Actions',],

  ],
  tfoot: [

  ],

})

const mountInit = () =>{
  var _this = this;
  updateTableJSON();

  $('#content-editable-table').on('change', '[type="text"]', function() {
    updateTableJSON();
  });
}
const updateTableJSON = ()=> {
  tableJSON.value = JSON.stringify(table);
}

const addColumn  = ()=> {
  table.thead.push('Heading ' + (this.table.thead.length + 1));

  for(var i = 0, length = this.table.tbody.length; i < length; i++) {
    table.tbody[i].push('R:' + (i + 1) + ' V:' + table.thead.length);
  }

  table.tfoot.push('Footer ' + this.table.thead.length);

  updateTableJSON();
}

const addRow = ()=> {
  var newRow = [];


  for(var i = 0, length = table.value.thead.length; i < length; i++) {
    newRow.push('R:' + (table.value.tbody.length + 1) + ' V:' + (i + 1))
  }

  table.value.tbody.push(newRow);

  updateTableJSON();
}
onMounted(()=>{
  mountInit()
})


</script>

<template>
  <VCard>
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

              </div>
            </VCol>
            <VCol
              cols="12"
              md="6">
              <div class="align-center mb-6">
                <h6 class="text-sm font-weight-medium mb-3">
                  Invoice To:
                </h6>
                <model-list-select
                  :list="props.data.clients"
                  v-model="props.data.client"
                  option-value="id"
                  :custom-text="fullName"
                  :hideSelectedOptions="true"
                  placeholder="Select Client">
                </model-list-select>
              </div>
            </VCol>
          </VRow>


        </VCol>
        <!--        <VCol-->
        <!--          cols="12"-->
        <!--          md="5"-->
        <!--        >-->
        <!--          <div class="mt-4 ma-sm-4" >-->
        <!--            <h6 class="text-sm font-weight-medium mb-3">-->
        <!--              Bill To:-->
        <!--            </h6>-->

        <!--            <table>-->
        <!--              <tbody>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  Invoice:-->
        <!--                </td>-->
        <!--                <td class="font-weight-semibold">-->
        <!--                  #{{ props.data.id }}-->
        <!--                </td>-->
        <!--              </tr>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  Payment Method:-->
        <!--                </td>-->
        <!--                <td class="font-weight-semibold">{{ props.data.sale_status.name }}</td>-->
        <!--              </tr>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  Client:-->
        <!--                </td>-->
        <!--                <td class="font-weight-semibold">-->
        <!--                  {{ props.data.client.name }} {{props.data.client.surname}}-->
        <!--                </td>-->
        <!--              </tr>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  Address:-->
        <!--                </td>-->
        <!--                <td>{{ props.data.client.address }}</td>-->
        <!--              </tr>-->


        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  N°RC:-->
        <!--                </td>-->
        <!--                <td>{{ props.data.client.NRC }}</td>-->
        <!--              </tr>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  N°IF:-->
        <!--                </td>-->
        <!--                <td>{{ props.data.client.NIF }}</td>-->
        <!--              </tr>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  N°IS:-->
        <!--                </td>-->
        <!--                <td>{{ props.data.client.NIS }}</td>-->
        <!--              </tr>-->
        <!--              <tr>-->
        <!--                <td class="pe-6">-->
        <!--                  N°ART:-->
        <!--                </td>-->
        <!--                <td>{{ props.data.client.NART }}</td>-->
        <!--              </tr>-->
        <!--              </tbody>-->
        <!--            </table>-->
        <!--          </div>-->
        <!--        </VCol>-->
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

            <span class="text-sm-caption mb-2">Products</span>
            <model-list-select
              :list="props.data.products"
              v-model="selectedItem"
              option-value="id"
              :custom-text="productFullName"
              placeholder="Select Product"
            >

            </model-list-select>

            <div id="content-editable-table" class="container">

              <table class="table table-striped editable-table">
                <thead v-if="table.thead.length">
                <tr>
                  <th v-for="(heading, index) in table.thead">
                    {{table.thead[index]}}
                  </th>


                </tr>
                </thead>

                <tbody>
                <tr v-for="row in table.tbody">
                  <td v-for="(value, index) in row">
                    <textarea  type="text" v-model="row[index]"  onfocus="this.select()" :readonly="readonly" @mousedown="enableEditing" @keydown.enter.prevent="confirmContent"/>
                  </td>

                </tr>

                </tbody>


              </table>


            </div>


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

.container {
  padding: 30px;
  width: 100%;
}

.editable-table textarea {
  background: none;
  border: none;
  display: block;
  width: 100%;
  resize: none;
  overflow: hidden;
}
.editable-table{
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  animation: float 5s infinite;
  width: 100% !important;
  border-collapse: collapse;
  overflow-x: auto;

}
#content-editable-table{

  padding: 30px 0 0 0;
}

.output {
  white-space: normal;
}
td{
  border-right: 1px solid #cccccc;
  padding: 10px;
  transition: all 0.2s;
  border-bottom: 1px solid #cccccc;
}
tr {
  border-bottom: 1px solid #cccccc;
}

th{
  border-right: 1px solid #cccccc;
  padding: 10px;
  transition: all 0.2s;
  border-bottom: 1px solid #cccccc;
}
textarea{
  outline: none;
}
</style>
