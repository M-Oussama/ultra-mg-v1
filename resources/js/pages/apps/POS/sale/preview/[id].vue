<script setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

// Components
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'

// Store
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";

const saleStore = useSaleStore()
const route = useRoute()
const sale = ref({
  id:1,
  balance: 0,
  total_amount: 0,
  sale_date: null,
  client_id: 1,
  sale_status:{
    id:-1,
    name: ""
  },
  amount_letter: "",
  notes: "",
  client:{
    id: -1,
    name: "",
    surname: "",
    address: "",
    email: "",
    phone: "",
    NRC: "",
    NIF: "",
    NART: "",
    NIS: "",
  },
  sale_items:[],
})
function numberToFrenchWords(n) {
  const units = ['zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
  const teens = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
  const tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];

  if (n < 10) return units[n];
  if (n < 20) return teens[n - 10];
  if (n < 100) {
    if (n % 10 === 0) return tens[Math.floor(n / 10)];
    if (n < 70) return tens[Math.floor(n / 10)] + '-' + units[n % 10];
    if (n < 80) return 'soixante-' + teens[n % 20];
    return 'quatre-vingt-' + (n % 20 === 0 ? '' : teens[n % 20]);
  }
  if (n < 1000) {
    if (n === 100) return 'cent';
    if (n % 100 === 0) return units[Math.floor(n / 100)] + ' cent';
    return units[Math.floor(n / 100)] + ' cent ' + numberToFrenchWords(n % 100);
  }
  if (n < 1000000) {
    if (n === 1000) return 'mille';
    if (n % 1000 === 0) return numberToFrenchWords(Math.floor(n / 1000)) + ' mille';
    return numberToFrenchWords(Math.floor(n / 1000)) + ' mille ' + numberToFrenchWords(n % 1000);
  }
  return n.toString();  // for simplicity, handle numbers above 999999 as strings
}
const show = ref({
  sku:false,
  invoice:false,
  price:true,
  paymentType:'Espece',
  sold: false
})
const invoiceID =ref(12)
const sold = ref(0)
const defaultClient = ref();
let clients = ref([])
const companyVal = ref(1);
const company = ref({
  name:'',
  address:'',
  phone:'',
  email:'',
  NRC:'',
  NIF:'',
  NART:'',
  NIS:'',
  capitale:''
})
let companies = ref([

])
const active = ref(false)
const paymentDetails = ref()
const isAddPaymentSidebarVisible = ref(false)
const isSendPaymentSidebarVisible = ref(false)
const loading = ref({
  isActive: false
})
// 👉 fetchInvoice
saleStore.fetchSale(Number(route.params.id)).then(response => {

  sale.value = response.data.sale
  sold.value = response.data.sold
  defaultClient.value = {...sale.value.client};
  companies = response.data.companies
  clients = response.data.clients
  company.value = companies[0];

  active.value = true
 
  //paymentDetails.value = response.data.paymentDetails
}).catch(error => {
  console.log(error)
})

// 👉 Print Invoice
const printInvoice = () => {
  window.print()
}

watch(companyVal, (value, oldValue, onCleanup)=>{
  company.value = companies[value-1];
  console.log(company)
})
const printClientName = value =>{
  return value.name+' '+ (value.surname ? value.surname : '')
}
const invoiceType = ref("Facture Proforma")
const clientId = ref()

watch(clientId, (value, oldValue, onCleanup)=>{
   if(!value) {
      sale.value.client = defaultClient
   }
  for (let i = 0; i <clients.length ; i++) {
    if(clients[i].id === value){
      sale.value.client = {...clients[i]}

    }
  }

})
</script>

<template>
  <section v-if="active">
    <v-overlay
      :model-value="loading.isActive"
      class="align-center justify-center"
    >
      <v-progress-circular
        color="primary"
        indeterminate
        size="64"
      ></v-progress-circular>
    </v-overlay>
    <VRow>
      <VCol
        class="printed"
        cols="12"
        md="9"
      >
        <VCard>
          <!-- SECTION Header -->
          <VCardText  class=" d-flex flex-wrap justify-space-between flex-column flex-sm-row print-row pb-0">

            <VRow>
              <VCol
              cols="8"
              >
                <!-- 👉 Left Content -->
                <div class="ma-sm-3">

                  <div class="d-flex align-center mb-6">
                    <!-- 👉 Logo -->
<!--                    <VNodeRenderer-->
<!--                      :nodes="themeConfig.app.logo"-->
<!--                      class="me-3"-->
<!--                    />-->

                    <!-- 👉 Title -->
                    <h6 class="font-weight-bold text-xl">
                      {{ company.name }}
                    </h6>
                  </div>

                  <!-- 👉 Address -->

                  <h4 class="mb-0">
                    {{ company.address }}
                  </h4>
                  <h4 class="mb-0">
                    <!--               {{company.address2}}-->
                    KASR EL ABTALE, 19000, SETIF
                  </h4>
                  <h4 class="mb-0" v-if="!show.invoice">
                    {{company.email}}
                  </h4>
                  <h4 class="mb-0" v-if="!show.invoice">
                    {{ company.phone }}
                  </h4>
                </div>
              </VCol>

              <VCol
              cols="4"
              v-if="!show.invoice"
              >
                <!-- 👉 Right Content -->
                <div class="mt-4 ma-sm-4">
                  <!-- 👉 Invoice ID -->
                  <h6 class="font-weight-medium text-xl mb-2">
                    Bon Livraison #{{ sale.id }}
                  </h6>

                  <!-- 👉 Issue Date -->
                  <h4 class="mb-2">
                    <span>Date : </span>
                    <span class="font-weight-semibold">{{ sale.sale_date }}</span>
                  </h4>
                  <h4 class="mb-2" v-if="show.price">
                    <span>Payment: </span>
                    <span class="font-weight-semibold">{{ show.paymentType }}</span>
                  </h4>
                </div>
              </VCol>
              <VCol
              cols="4"
              class="mt-5"
              v-if="show.invoice"
              >
                <!-- 👉 Right Content -->
                <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row custom-white-border" v-if="company.NRC">
                  <div class=" v-col-md-3 text-sm-subtitle-2 border-right">
                    N°RC
                  </div>
                  <div    class=" v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font">
                    {{ company.NRC }}
                  </div>
                </div>
                <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border" v-if="company.NIF">
                  <div    class=" v-col-md-3 text-sm-subtitle-2 border-right">
                    N°IF
                  </div>
                  <div    class=" v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font">
                    {{ company.NIF }}
                  </div>
                </div>

                <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border" v-if="company.NIS">
                  <div    class=" v-col-md-3 text-sm-subtitle-2 border-right ">
                    N°IS
                  </div>
                  <div    class=" v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font">
                    {{ company.NIS }}
                  </div>
                </div>
                <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border" v-if="company.NART">
                  <div    class=" v-col-md-3 text-sm-subtitle-2 border-right padding-right-0">
                  <span>
                     N°ART
                  </span>

                  </div>
                  <div    class=" v-col-md-8 text-sm-subtitle-2 padding-8 border-left data-font">
                    {{ company.NART }}
                  </div>
                </div>

              </VCol>

            </VRow>



          </VCardText>
          <!-- !SECTION -->



          <VDivider v-if="show.invoice" />



          <VRow class="ma-3 mb-0 text-center" v-if="show.invoice">
            <VCol cols="4">

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-4 text-sm-subtitle-2 border-right padding-right-0">
                  <span>
                        {{ invoiceType }}
                  </span>

                </div>
                <div    class=" v-col-md-6 text-sm-subtitle-2 padding-8 border-left data-font">
                  #{{ invoiceID }}
                </div>
              </div>
            </VCol>
            <VCol cols="4">
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-3 text-sm-subtitle-2 border-right padding-right-0">
                  <span>
                        Payment:
                  </span>

                </div>
                <div    class=" v-col-md-8 text-sm-subtitle-2 padding-8 border-left data-font">
                  {{ show.paymentType }}
                </div>
              </div>

            </VCol>

            <VCol cols="4">

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-3 text-sm-subtitle-2 border-right padding-right-0">
                  <span>
                        Date :
                  </span>

                </div>
                <div    class=" v-col-md-8 text-sm-subtitle-2 padding-8 border-left data-font">
                  {{ sale.sale_date }}
                </div>
              </div>
            </VCol>


          </VRow>



          <VCardText class=" invoiceGeneral d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row ">
            <!-- 👉 Left Content -->
            <div class="v-col-md-6">
              <!-- 👉 Invoice ID -->
              <h4 class="font-weight-bold   text-sm-h6 mb-4 text-color-black  text-center ">

              </h4>

              <!-- 👉 Issue Date -->

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  Client
                </div>
                <div    class=" v-col-md-10  border-left padding-8 data-font  text-weight-bold">
                  {{ sale.client.name }} {{sale.client.surname}}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  Address
                </div>
                <div    class=" v-col-md-10 border-left padding-8 data-font  text-weight-bold">
                  {{ sale.client.address }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center  custom-white-border">
                <div    class=" v-col-md-2 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  Phone
                </div>
                <div    class=" v-col-md-10 border-left padding-8 data-font  text-weight-bold">
                  {{ sale.client.phone }}
                </div>
              </div>

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center  custom-white-border">
                <div    class=" v-col-md-2 text-sm-subtitle-2  border-right text-h4 text-weight-bold">
                  Email
                </div>
                <div    class=" v-col-md-10 border-left padding-8 data-font  text-weight-bold">
                  {{ sale.client.email }}
                </div>
              </div>
            </div>
            <div class="v-col-md-2"></div>
            <div class="v-col-md-4 " v-if="show.invoice">
              <!-- 👉 Invoice ID -->
              <h4 class="font-weight-bold text-sm-h6 mb-4 text-color-black text-center text-h4 align-center">

              </h4>

              <!-- 👉 Issue Date -->

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row custom-white-border" v-if="sale.client.NRC">
                <div class=" v-col-md-3 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  N°RC
                </div>
                <div    class=" v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold" >
                  {{ sale.client.NRC }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border" v-if="sale.client.NIF">
                <div    class=" v-col-md-3 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  N°IF
                </div>
                <div    class=" v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ sale.client.NIF }}
                </div>
              </div>

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border" v-if="sale.client.NIS">
                <div    class=" v-col-md-3 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  N°IS
                </div>
                <div    class=" v-col-md-8 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ sale.client.NIS }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border" v-if="sale.client.NART">
                <div    class=" v-col-md-3 text-sm-subtitle-2 border-right padding-right-0 text-h4 text-weight-bold">
                  <span>
                     N°ART
                  </span>

                </div>
                <div    class=" v-col-md-8 text-sm-subtitle-2 padding-8 border-left data-font text-h4 text-weight-bold">
                  {{ sale.client.NART }}
                </div>
              </div>

            </div>

            <div class="v-col-md-4 " v-if="!show.invoice">
              <!-- 👉 Invoice ID -->
              <h4 class="font-weight-bold text-sm-h6 mb-4 text-color-black text-center align-center">

              </h4>

              <!-- 👉 Issue Date -->

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row custom-white-border">
                <div class=" v-col-md-5 text-sm-subtitle-2 border-right  text-h4 text-weight-bold">
                  Facture
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  #{{ sale.id }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  Payment Type
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ show.paymentType }}
                </div>
              </div>

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  Date
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ sale.sale_date }}
                </div>
              </div>


            </div>
          </VCardText>



          <!-- 👉 Table -->
          <VDivider />

          <VTable>
            <thead>
              <tr>
                <th class="text-sm-caption text-h4 text-weight-bold" scope="col">
                  ID
                </th>
                <th class="text-sm-caption text-h4 text-weight-bold" scope="col" v-if="show.sku">
                  SKU
                </th>
                <th class="text-sm-caption text-h4 text-weight-bold" scope="col">
                  name
                </th>
                <th
                  scope="col"
                  class="text-center text-sm-caption text-h4 text-weight-bold"
                  v-if="show.price"
                >
                  Price
                </th>
                <th
                  scope="col"
                  class="text-center text-sm-caption text-h4 text-weight-bold"
                >
                  QTY
                </th>
                <th
                  scope="col"
                  class="text-center text-sm-caption text-h4 text-weight-bold"
                  v-if="show.price"
                >
                  TOTAL
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(item,index) in sale.sale_items"
                :key="item.name"
              >
                <td class="text-no-wrap text-sm-caption text-h4 text-weight-bold">
                  {{ index+1 }}
                </td>
                <td class="text-no-wrap text-sm-caption text-h4 text-weight-bold"  v-if="show.sku">
                  {{ item.product.SKU }}
                </td>
                <td class="text-no-wrap">
                  <h4 class="text-sm-caption mb-0 text-h4 text-weight-bold">{{ item.product.name }}</h4>


                </td>
                <td class="text-center text-sm-caption text-h4 text-weight-bold" v-if="show.price">
                  {{ item.price }} DZD
                </td>
                <td class="text-center text-sm-caption text-h4 text-weight-bold">
                  {{ item.quantity }}
                </td>
                <td class="text-center text-sm-caption text-h4 text-weight-bold" v-if="show.price">
                  {{ item.total_price }} DZD
                </td>
              </tr>
            </tbody>
          </VTable>

          <VDivider class="my-2" v-if="show.price"/>

          <!-- Total -->
          <VCardText class="d-flex justify-space-between flex-column flex-sm-row print-row pt-0" v-if="show.price">
            <div class="my-2 mx-sm-5 v-col-md-6">

              <h5 v-if="!show.invoice">La Facture est arretée à la somme de : {{numberToFrenchWords(sale.total_amount) }}</h5>
              <h5 v-else>La Facture est arretée à la somme de : {{numberToFrenchWords(sale.total_amount*1.19) }}</h5>
              <div v-if="show.sold" class="mt-10 d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  Ancien solde
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ parseFloat(sold).toFixed(2) }} DZD
                </div>
              </div>


              <div v-if="show.sold" class=" d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-success text-h4 text-weight-bold" v-if="show.price">
                  Nouveau
                </div>
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold" v-if="!show.price">
                  Nouveau
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-success text-h4 text-weight-bold" v-if="show.price">
                  {{ parseFloat(sold) > 0 ? (parseFloat(sale.total_amount) + parseFloat(sold)).toFixed(2) : (-parseFloat(sale.total_amount) + parseFloat(sold)).toFixed(2)}} DZD
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold" v-if="!show.price">
                  {{ parseFloat(sold) > 0 ? (parseFloat(sale.total_amount) + parseFloat(sold)).toFixed(2) : (-parseFloat(sale.total_amount) + parseFloat(sold)).toFixed(2)}} DZD
                </div>
              </div>
            </div>

            <div class="my-2 mx-sm-4 v-col-md-4">

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                   {{show.invoice? 'MONTANT HT' : 'TOTAL '}}
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ (sale.total_amount) }} DZD
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold">
                  {{show.invoice? 'TVA 19% ': 'Paiement'}}
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold">
                  {{ show.invoice? (parseFloat((sale.total_amount)*0.19)).toFixed(2): (parseFloat((sale.total_amount)-sale.balance)).toFixed(2) }} DZD

                </div>
              </div>

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center custom-white-border">
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-h4 text-weight-bold"  v-if="show.sold">
                  {{show.invoice? 'MONTANT TTC': 'NOUVEAU'}}
                </div>
                <div    class=" v-col-md-5 text-sm-subtitle-2 border-right text-success text-h4 text-weight-bold" v-if="!show.sold">
                  {{show.invoice? 'MONTANT TTC ': 'NOUVEAU'}}
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-success text-h4 text-weight-bold" v-if="!show.sold">
                  {{ show.invoice?(parseFloat(sale.total_amount * 1.19)).toFixed(2):  (parseFloat(sale.balance)).toFixed(2) }} DZD
                </div>
                <div    class=" v-col-md-7 text-sm-subtitle-2 border-left padding-8 data-font text-h4 text-weight-bold" v-if="show.sold">
                  {{ show.invoice? (parseFloat(sale.total_amount * 1.19)).toFixed(2): (parseFloat(sale.balance)).toFixed(2) }} DZD
                </div>
              </div>






            </div>
          </VCardText>

          <VDivider />


          <VCardText>
            <div class="d-flex mx-sm-4">
              <h6 class="text-sm font-weight-semibold me-1">
                Note:
              </h6>
              <span>Merci pour votre règlement rapide. Nous apprécions votre collaboration </span>
            </div>
          </VCardText>

        </VCard>


      </VCol>

      <VCol
        cols="12"
        md="3"
        class="d-print-none"
      >
        <VCard>
          <VCardText>
            <!-- 👉 Send Invoice Trigger button -->
            <VBtn
              block
              prepend-icon="tabler-send"
              class="mb-2"
              @click="isSendPaymentSidebarVisible = true"
            >
              Send Invoice
            </VBtn>
            <VBtn
              block
              variant="tonal"
              color="secondary"
              class="mb-2"
              @click="printInvoice"
            >
              Print
            </VBtn>

            <VBtn
              block
              color="secondary"
              variant="tonal"
              class="mb-2"
              :to="{ name: 'apps-POS-sale-edit-id', params: { id: route.params.id } }"
            >
              Edit Invoice
            </VBtn>

            <VBtn
              block
              prepend-icon="tabler-currency-dollar"
              class="mb-2"
              @click="isAddPaymentSidebarVisible = true"
            >
              Add Payment
            </VBtn>
          </VCardText>
        </VCard>
        <!-- 👉 Payment Terms -->

        <VCard class="mt-7">
          <VCardText>
            <div class="d-flex align-center justify-space-between mt-5">
              <VLabel for="sold">
                Sold
              </VLabel>
              <div>
                <VSwitch
                  id="sku"
                  v-model="show.sold"
                />
              </div>
            </div>
            <div class="d-flex align-center justify-space-between">
              <VLabel for="price">
                SKU
              </VLabel>
              <div>
                <VSwitch
                  id="sku"
                  v-model="show.sku"
                />
              </div>
            </div>
            <div class="d-flex align-center justify-space-between">
              <VLabel for="price">
                Facture
              </VLabel>
              <div>
                <VSwitch
                  id="sku"
                  v-model="show.invoice"
                />
              </div>
            </div>
            <div class="d-flex align-center justify-space-between">
              <VLabel for="price">
                Price
              </VLabel>
              <div>
                <VSwitch
                  id="price"
                  v-model="show.price"
                />
              </div>
            </div>




            <div >

              <div>
                <VLabel for="payment-terms" class="mb-1 mt-1">
                  Payment
                </VLabel>
                <VTextField
                  v-model="show.paymentType"
                  placeholder="Thanks for your business"
                />
              </div>
            </div>
            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Invoice Type
              </VLabel>
              <VTextField
                v-model="invoiceType"
                placeholder="Put the Invoice Type"
              />
            </div>
            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Invoice ID
              </VLabel>
              <VTextField
                v-model="invoiceID"
                type="number"
                placeholder="Put the Invoice ID"
              />
            </div>

            <!--  Company --->
            <div >
              <VLabel  class="mb-4 mt-1">
                Company
              </VLabel>

              <VAutocomplete
                v-model="companyVal"
                :items="companies"
                item-value="id"
                class="text-sm-subtitle-2"
                item-title="name"
                label="Company"

              />

            </div>
            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Company NRC
              </VLabel>
              <VTextField
                v-model="company.NRC"

                placeholder="Put the company NRC"
              />
            </div>


            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Company NIF
              </VLabel>
              <VTextField
                v-model="company.NIF"

                placeholder="Put the company NIF"
              />
            </div>

            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Company NIS
              </VLabel>
              <VTextField
                v-model="company.NIS"

                placeholder="Put the company NIS"
              />
            </div>

            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Company NART
              </VLabel>
              <VTextField
                v-model="company.NART"

                placeholder="Put the company NART"
              />
            </div>
            <!--  CLIENT --->
            <div>
              <VLabel  class="mb-4 mt-1 ">
                Client
              </VLabel>

              <VAutocomplete
                clearable
                v-model="clientId"
                :items="clients"
                item-value="id"
                :item-title="printClientName"
                label="Client"
              />

            </div>
            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Client NRC
              </VLabel>
              <VTextField
                v-model="sale.client.NRC"

                placeholder="Put the Client NRC"
              />
            </div>


            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Client NIF
              </VLabel>
              <VTextField
                v-model="sale.client.NIF"

                placeholder="Put the Client NIF"
              />
            </div>

            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Client NIS
              </VLabel>
              <VTextField
                v-model="sale.client.NIS"

                placeholder="Put the Client NIS"
              />
            </div>

            <div v-if="show.invoice">
              <VLabel for="payment-terms" class="mb-1 mt-1">
                Client NART
              </VLabel>
              <VTextField
                v-model="sale.client.NART"

                placeholder="Put the Client NART"
              />
            </div>
          </VCardText>

        </VCard>


      </VCol>
    </VRow>


    <!-- 👉 Add Payment Sidebar -->
    <InvoiceAddPaymentDrawer :data="sale" :loading="loading" v-model:isDrawerOpen="isAddPaymentSidebarVisible" />

    <!-- 👉 Send Invoice Sidebar -->
    <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendPaymentSidebarVisible" />
  </section>
</template>

<style lang="scss">

@media print {
  * {
    border-color: rgb(0 0 0 / 30%) !important;
  }
  * {
    color: black !important;
  }
.layout-vertical-nav{
  display: none !important;
}

  .v-application {
    background: none !important;
  }

  @page { margin: 0; size: B0; }

  .layout-page-content,
  .v-row,
  .v-col-md-9 {
    padding: 0;
    margin: 0;
  }

  .product-buy-now {
    display: none;
  }

  .v-navigation-drawer,
  .layout-vertical-nav,
  .app-customizer-toggler,
  .layout-footer,
  .layout-navbar,
  .layout-navbar-and-nav-container {
    display: none;
  }

  .v-col-md-6 {
    flex: 0 0 50% !important;
    max-width: 50% !important;
  }
  .v-col-md-10 {
    flex: 0 0 83.3333333333%;
    max-width: 83.3333333333%;
  }

  .v-col-md-2 {
    flex: 0 0 16.6666666667%;
    max-width: 16.6666666667%;
  }
  .v-col-md-8 {
    flex: 0 0 66.6666666667%;
    max-width: 66.6666666667%;
  }
  .v-col-md-7 {
    flex: 0 0 58.3333333333% !important;
    max-width: 58.3333333333% !important;
  }
  .v-col-md-5 {
    flex: 0 0 41.6666666667% !important;
    max-width: 41.6666666667% !important;
  }
  .v-col-md-4 {
    flex: 0 0 33.3333333333%;
    max-width: 33.3333333333%;
  }
  .v-col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
  .v-col-md-2 {
    flex: 0 0 16.6666666667%;
    max-width: 16.6666666667%;
  }

  .v-card {
    box-shadow: none !important;

    .print-row {
      flex-direction: row !important;
    }
  }

  .layout-content-wrapper {
    padding-inline-start: 0 !important;
  }
  .text-color-black{
    color: black !important;
  }

  .invoiceGeneral{
    background-color: white !important;
    border-radius: 10px !important;
    padding: 0px !important;
  }
  .display-center{
    display: inline-grid
  }

  .custom-border{
    padding: 10px 0 10px 15px;
    background-color: #f3f2f4;
    border-radius: 19px;
    margin-right: 6px;
    margin-bottom: 6px;
  }
  .custom-white-border{
    height: 30px;
    background-color: white;
    border-radius: 19px;
    margin-right: 12px;
    border: 1px solid #e4e4e4;
    margin-bottom: 6px;
  }
  .border-right{
    font-size: 10px !important;
    height: 22px;
    padding: 0 8px 0;
    border-right: solid 1px #dbdbdb ;
  }
  .text-sm-caption{
    font-size: 11px !important;
    font-weight: bold;
  }
  .text-sm-subtitle-2{
    font-weight: bold;
  }

  .padding-8{
    padding: 1px 8px 0;
  }
  .padding-right-0{
    padding-right: 0;
  }
  .text-sm-subtitle-2 {
    font-size: 0.875rem !important;
    font-weight: 500;
    line-height: 1.375rem;
    letter-spacing: 0.0071428571em !important;
    font-family: "Public Sans", sans-serif, -apple-system, blinkmacsystemfont, "Segoe UI", roboto, "Helvetica Neue", arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    text-transform: none !important;
  }
  .data-font{
    font-size: 12px !important;
    font-weight: bold;
  }
  .border-left{

  }
}
.text-color-black{
  color: black !important;
}
.invoiceGeneral{
  background-color: white !important;
  border-radius: 10px !important;
  padding: 0 !important;

}
.display-center{
  display: inline-grid
}

.custom-border{
  padding: 10px 0 10px 15px;
  background-color: #f3f2f4;
  border-radius: 19px;
  margin-right: 6px;
  margin-bottom: 6px;
}
.custom-white-border{
  height: 23px;
  background-color: white;
  border-radius: 19px;
  margin-right: 12px;
  border: 1px solid #e4e4e4;
  margin-bottom: 6px;
}

.border-right{
  font-size: 10px !important;
  height: 22px;
  padding: 0 8px 0;
  border-right: solid 1px #dbdbdb;
}
.border-left{

}
.text-success{
  color:green;
  font-weight:bold;
}
.padding-8{
  padding: 1px 8px 0;
}
.padding-right-0{
  padding-right: 0;
}

.data-font{
  font-size: 10px !important;
  font-weight: bold;
}
.v-autocomplete__selection-text{
  font-size: small;
}
table > tbody > tr > td, table > thead > tr > th{
  height: 23px !important;
}
.text-weight-bold{
  font-weight: bold;
  line-height: 1.5rem;
}
</style>
