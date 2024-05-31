<script setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

// Components
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import {useVacationStore} from "@/views/apps/employee/vacation/useVacationStore";

// Store
const route = useRoute()

const props = defineProps({
  employee: {
    type: Object,
  },
  name:{
    type:String
  }
})
const loading = ref({
  isActive: false
})
const store = useVacationStore()
const object = ref({
  id: "",
  start_date: "",
  end_date: "",
  count: "",
  employee_id: "",
  employee_career_id: "",
  employee: {
    id: 1,
    name: "",
    surname: "",
    birthdate: "",
    birthplace: "",
    email: "",
    address: "",
    phone: "",
    NIN: "",
    NCN: "",
    CNAS: "",
    card_issue_date: "",
    card_issue_place: "",
    active: "",
    created_at: "",
    updated_at: "",
    card_issued_city: {
      name: '',
      name_ar: ''
    },
    birth_city: {
      name: '',
      name_ar: ''
    }
  },
  employee_career: {
    id: 2,
    position: "",
    position_ar:""
  }
})
const fetchRecord = (id) => {
  loading.isActive = true;
  store.fetchRecord(id).then(response => {
    loading.isActive = false;
    object.value = response.data.vacation
    console.log(object)
  }).catch(error => {

    loading.isActive = false;
    console.error(error)

  })
}
const format = (date) => {
  const day = date.getDate();
  const month = date.getMonth() + 1;
  const year = date.getFullYear();

  return `${year}-${leadingZero(month,2)}-${leadingZero(day)}`;
}

watchEffect(fetchRecord(route.params.id))
// 👉 Print Invoice
const printInvoice = () => {
  window.print()
}
function leadingZero (str) {
  return parseInt(str) < 10 ? "0"+str : str;
}


const getFullYear = (date) =>{
  const d = new Date(date);
  return  d.getFullYear();
}
const getNextDate = (date) =>{
  let nextDay = new Date(date);
  return format(new Date(nextDay.setDate(nextDay.getDate() + 1)));
}
</script>

<template>
  <section >
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
        <VCard >
          <VCardText  class=" print-row pb-0 pt-0">
            <div>
              <div ref="html2Pdf" class="text-right" dir="rtl">
                <div class=" text-center">
                  <h6 class="header-font-size">
                   <u>EURL SETIFIS DETERGENTS</u>

                  </h6>
                  <h4 class="text-left header-font-size">
                    <u>Adresse</u>: LOTISSEMENT 34 SECT 6 GROUPE N° 51 KASR EL ABTAL

                  </h4>
                  <h6 class="text-left header-font-size">
                    <u> Activité</u> : FABRICATION DE PRODUITS DE BLANCHISEMENTS ET PRODUITS DE LA MAINT


                  </h6>
                  <h6 class="text-left header-font-size" >
                    <u>Numéro employeur</u> : 1960328646 M.F. : 001319010024074

                  </h6>
                  <VDivider class="mt-5 mb-5"/>
                </div>
                <h6 class="text-right">رقم : .................</h6>
                <h1 class="text-center mb-8 mt-8 high-size " >
                  <span class="border high-size pl-8 pr-8 mr-5 ml-5 pt-2 pb-2 border-radius">   سنــــــد عطلــــــة سنويـــــــــــــــــــــــــــــة </span>

                </h1>

                <div class="text-right center-text">
                  <h4 class="text-font-size">الاســـــــــــــم و اللقــــــب :................................................ </h4>
                  <h4 class="text-font-size">الوظيفــــــــــــــة :......................................................... </h4>
                  <h4 class="text-font-size">يستفيد من عطلة :...............................، عدد الأيام:..................... </h4>
                  <h4 class="text-font-size">من :................................ الى :................................. مدرج </h4>
                  <h4 class="text-font-size">يستأنـــــــــــــــف عملـــــــــه يـــــــــــوم  :.................................. </h4>

                </div>


                <h1 class="text-center mb-8 mt-8 high-size " >
                  <span class="border  pl-8 pr-8 mr-5 ml-5 pt-2 pb-2 border-radius">  يستفيد بهذا السند لاستعماله في الإطار المسموح به شرعا. </span>

                </h1>
                <VRow>
                  <VCol cols="6">
                    <h6>
                      توقيع المعني: .......................................
                  </h6>
                  </VCol>

                </VRow>


              </div>
            </div>
          </VCardText>

        </VCard>


        <div class="page-break pt-5">
          <VCard >
            <VCardText  class=" print-row pb-0 pt-0">
              <div>
                <div ref="html2Pdf" class="text-right" dir="rtl">
                  <div class=" text-center">
                    <h6 class="header-font-size">
                      <u>EURL SETIFIS DETERGENTS</u>

                    </h6>
                    <h4 class="text-left header-font-size">
                      <u>Adresse</u>: LOTISSEMENT 34 SECT 6 GROUPE N° 51 KASR EL ABTAL

                    </h4>
                    <h6 class="text-left header-font-size">
                      <u> Activité</u> : FABRICATION DE PRODUITS DE BLANCHISEMENTS ET PRODUITS DE LA MAINT


                    </h6>
                    <h6 class="text-left header-font-size" >
                      <u>Numéro employeur</u> : 1960328646 M.F. : 001319010024074

                    </h6>
                    <VDivider class="mt-5 mb-5"/>
                  </div>
                  <h6 class="text-right">رقم : .................</h6>
                  <h1 class="text-center mb-8 mt-8 high-size " >
                    <span class="border high-size pl-8 pr-8 mr-5 ml-5 pt-2 pb-2 border-radius">   سنــــــد عطلــــــة سنويـــــــــــــــــــــــــــــة </span>

                  </h1>

                  <div class="text-right center-text">
                    <h4 class="text-font-size">الاســـــــــــــم و اللقــــــب :{{object.employee.name_ar}} {{object.employee.surname_ar}}. </h4>
                    <h4 class="text-font-size">الوظيفــــــــــــــة :{{object.employee_career.position_ar}}. </h4>
                    <h4 class="text-font-size">يستفيد من عطلة :

                      <span class="text-font-size">{{getFullYear(object.start_date)}}</span>

                      /  <span class="text-font-size">{{getFullYear(object.end_date)}}</span>

                      ، عدد الأيام:{{object.count}}. </h4>
                    <h4 class="text-font-size">من :{{object.start_date}}. الى :{{object.end_date}}. مدرج </h4>
                    <h4 class="text-font-size">يستأنـــــــــــــــف عملـــــــــه يـــــــــــوم  : {{getNextDate(object.end_date)}} . </h4>

                  </div>


                  <h1 class="text-center mb-8 mt-8 high-size " >
                    <span class="border  pl-8 pr-8 mr-5 ml-5 pt-2 pb-2 border-radius">  يستفيد بهذا السند لاستعماله في الإطار المسموح به شرعا. </span>

                  </h1>
                  <VRow>
                    <VCol cols="6">
                      <h6>
                        توقيع المعني: .......................................
                      </h6>
                    </VCol>

                  </VRow>


                </div>
              </div>
            </VCardText>

          </VCard>
        </div>
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
              @click="printInvoice"
            >
              Print
            </VBtn>



          </VCardText>
        </VCard>
        <!-- 👉 Payment Terms -->




      </VCol>
    </VRow>

    <!-- 👉 Add Payment Sidebar -->

    <!-- 👉 Send Invoice Sidebar -->
  </section>
</template>

<style lang="scss">
li{
  padding:7px
}
*{
  font-size: 16px;
}
.header-font-size{
  font-size: 15px !important;
}
.medium-size{
  font-size: 25px !important;
}
.high-size{
  font-size: 37px;
}
.page-break {
  page-break-before: always; /* or page-break-after: always; */
}
@media print {


  * {
    border-color: rgb(0 0 0 / 30%) !important;
  }
  * {
    color: black !important;
  }
  .layout-vertical-nav{
    display: none;
  }

  .v-application {
    background: none !important;
  }

  @page { margin: 0; size: auto; }

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
  .center-text{
    padding-right: 0% !important;
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
.border-radius{
  border-radius: 10px;
  padding-left: 80px !important;
  padding-right: 80px !important;
}
.text-font-size{
  font-size: 20px;
  margin-bottom: 10px;
}
.center-text{
  padding-right: 10% !important;
}
</style>
