<script setup>
import InvoiceProductEdit from '@/views/apps/certifyInvoice/InvoiceProductEdit.vue'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
import { requiredValidator } from '@validators'
import {loading} from "@/views/demos/forms/form-elements/file-input/demoCodeFileInput";


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

const certifyInvoiceListStore = useCertifyInvoiceListStore()

// 👉 Clients

let totalInvoice = ref(0)
let previous_date = ref(props.data.date)
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


watch(props.data , ()=> {
  if(previous_date.value !== props.data.date ) {
    previous_date.value = props.data.date;

    props.loading.isActive = true;
    // 👉 get The Last ID
    certifyInvoiceListStore.getLastID({
      date: props.data.date,
  }
    ).then(response => {
      props.data.id = response.data.id
      props.loading.isActive = false;
    }).catch(err => {
      props.loading.isActive = false;
      console.log(err)
    })
  }
})

const totalAmount = data => {
  computeTotal()
}

const productFullName = item => {
  console.log("item :")
  console.log(item)
  return `${item.product.name}`
}

function computeTotal() {
  let totalPrices = props.data.certify_invoice_products.map(item => item.price * item.quantity);
  totalInvoice.value = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  props.data.total = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  props.data.amount = totalPrices.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
}

// 👉 Add item function
const addItem = () => {
  if(selectedItem.value !== undefined){
    console.log(selectedItem.value.product.id)
    const index = props.data.certify_invoice_products.findIndex(p =>
      p.product.id ===
      selectedItem.value.product.id);

    if(index ===-1 && selectedItem.value.product.id !== -1) {
      props.data.certify_invoice_products.push({...selectedItem.value})
    }

    computeTotal()
  }


}

watch(selectedItem , ()=> {

  handleProductChange()
})

const removeProduct = Item => {
  const index = props.data.certify_invoice_products.findIndex(p => p.product.id === Item.product.id);
  props.data.certify_invoice_products.splice(index,1);
  computeTotal()
}

const fullName = item => {
  return `${item.name}  ${item.surname}`
}

const handleProductChange = (value) => {
  addItem()
  selectedItem.value = {...defaultSelectedItem.value}
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
              v-model="props.data.date"
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

    <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row">

      <VRow>
        <VCol
          cols="12"
          md="7"
        >
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
          <div class="align-center mb-6">
            <h6 class="text-sm font-weight-medium mb-3">
              City:
            </h6>

          </div>
        </VCol>
        <VCol
          cols="12"
          md="5"
        >
          <div class="mt-4 ma-sm-4" >
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
                  FAJ/2023/{{ props.data.id }}
                </td>
              </tr>
              <tr>
                <td class="pe-6">
                  Payment Method:
                </td>
                <td class="font-weight-semibold">{{ props.data.payment_type }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  Client:
                </td>
                <td class="font-weight-semibold">
                  {{ props.data.client.name }} {{props.data.client.surname}}
                </td>
              </tr>
              <tr>
                <td class="pe-6">
                  Address:
                </td>
                <td>{{ props.data.client.address }}</td>
              </tr>


              <tr>
                <td class="pe-6">
                  N°RC:
                </td>
                <td>{{ props.data.client.NRC }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  N°IF:
                </td>
                <td>{{ props.data.client.NIF }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  N°IS:
                </td>
                <td>{{ props.data.client.NIS }}</td>
              </tr>
              <tr>
                <td class="pe-6">
                  N°ART:
                </td>
                <td>{{ props.data.client.NART }}</td>
              </tr>
              </tbody>
            </table>
          </div>
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

            <span class="text-sm-caption mb-2">Products</span>
            <model-list-select
              :list="props.data.products"
              v-model="selectedItem"
              option-value="id"
              :custom-text="productFullName"
              placeholder="Select Product"
            >

            </model-list-select>


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
        v-for="(_product, index) in props.data.certify_invoice_products"
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
.ant-select-clear{
  top: 40% !important;
  font-size:larger !important;
}
.selection{
  border-radius: 20px !important;

}
</style>
