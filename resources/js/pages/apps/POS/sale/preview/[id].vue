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
const active = ref(false)
const paymentDetails = ref()
const isAddPaymentSidebarVisible = ref(false)
const isSendPaymentSidebarVisible = ref(false)

// 👉 fetchInvoice
saleStore.fetchSale(Number(route.params.id)).then(response => {
  console.log(response)

  sale.value = response.data.sale
  active.value = true
 
  //paymentDetails.value = response.data.paymentDetails
}).catch(error => {
  console.log(error)
})


// 👉 Print Invoice
const printInvoice = () => {
  window.print()
}
</script>

<template>
  <section v-if="active">
    <VRow>
      <VCol
        cols="12"
        md="9"
      >
        <VCard>
          <!-- SECTION Header -->
          <VCardText  class=" d-flex flex-wrap justify-space-between flex-column flex-sm-row print-row">
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
                  {{ themeConfig.app.title }}
                </h6>
              </div>

              <!-- 👉 Address -->
              <p class="mb-0">
                Office 149, 450 South Brand Brooklyn
              </p>
              <p class="mb-0">
                San Diego County, CA 91905, USA
              </p>
              <p class="mb-0">
                +1 (123) 456 7891, +44 (876) 543 2198
              </p>
            </div>

            <!-- 👉 Right Content -->
            <div class="mt-4 ma-sm-4">
              <!-- 👉 Invoice ID -->
              <h6 class="font-weight-medium text-xl mb-6">
                Facture #{{ sale.id }}
              </h6>

              <!-- 👉 Issue Date -->
              <p class="mb-2">
                <span>Date Issued: </span>
                <span class="font-weight-semibold">{{ sale.sale_date }}</span>
              </p>
            </div>
          </VCardText>
          <!-- !SECTION -->

          <VDivider />

          <VCardText class=" invoiceGeneral d-flex flex-wrap justify-space-between flex-column flex-sm-row print-row ">
            <!-- 👉 Left Content -->
            <div class="mt-4 ma-sm-4  text-center">
              <!-- 👉 Invoice ID -->
              <p class="font-weight-bold mb-4 text-color-black text-center">
                Invoice
              </p>

              <!-- 👉 Issue Date -->
              <p class="mb-2">

                <span class="font-weight-semibold">#{{ sale.id }}</span>
              </p>
            </div>
            <div class="mt-4 ma-sm-4 text-center">
              <!-- 👉 Invoice ID -->
              <p class="font-weight-bold  mb-4 text-color-black">
                Payment Method
              </p>

              <!-- 👉 Issue Date -->
              <p class="mb-2 ">

                <span class="font-weight-semibold">{{ sale.sale_status.name }}</span>
              </p>
            </div>
            <!-- 👉 Right Content -->
            <div class="mt-4 ma-sm-4 text-center">
              <!-- 👉 Invoice ID -->
              <p class="font-weight-bold  mb-4 text-color-black">
                Date
              </p>

              <!-- 👉 Issue Date -->
              <p class="mb-2">

                <span class="font-weight-semibold">{{ sale.sale_date }}</span>
              </p>
            </div>
          </VCardText>

          <VDivider />

          <VCardText class=" invoiceGeneral d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row ">
            <!-- 👉 Left Content -->
            <div class="v-col-md-6  ">
              <!-- 👉 Invoice ID -->
              <p class="font-weight-bold   text-sm-h6 mb-4 text-color-black  text-center ">
                Invoice To:
              </p>


              <!-- 👉 Issue Date -->

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border text-sm-subtitle-2">
                  Client
                </div>
                <div    class=" v-col-md-7 custom-white-border ">
                  {{ sale.client.name }} {{sale.client.surname}}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  Address
                </div>
                <div    class=" v-col-md-7 custom-white-border">
                  {{ sale.client.address }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  Phone
                </div>
                <div    class=" v-col-md-7 custom-white-border">
                  {{ sale.client.phone }}
                </div>
              </div>

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  Email
                </div>
                <div    class=" v-col-md-7 custom-white-border ">
                  {{ sale.client.email }}
                </div>
              </div>
            </div>
            <div class="v-col-md-6 ">
              <!-- 👉 Invoice ID -->
              <p class="font-weight-bold  text-sm-h6 mb-4 text-color-black text-center align-center">
                Identifiers:
              </p>

              <!-- 👉 Issue Date -->

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  N°RC
                </div>
                <div    class=" v-col-md-7 custom-white-border">
                  {{ sale.client.NRC }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  N°IF
                </div>
                <div    class=" v-col-md-7 custom-white-border">
                  {{ sale.client.NIF }}
                </div>
              </div>

              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  N°IS
                </div>
                <div    class=" v-col-md-7 custom-white-border">
                  {{ sale.client.NIS }}
                </div>
              </div>
              <div class="d-flex flex-wrap justify-md-start flex-column flex-sm-row print-row align-center">
                <div    class=" v-col-md-4 custom-border">
                  N°ART
                </div>
                <div    class=" v-col-md-7 custom-white-border">
                  {{ sale.client.NART }}
                </div>
              </div>

            </div>

          </VCardText>



          <!-- 👉 Table -->
          <VDivider />

          <VTable>
            <thead>
              <tr>
                <th scope="col">
                  ID
                </th>
                <th scope="col">
                  SKU
                </th>
                <th scope="col">
                  name
                </th>
                <th scope="col">
                  Description
                </th>
                <th
                  scope="col"
                  class="text-center"
                >
                  Price
                </th>
                <th
                  scope="col"
                  class="text-center"
                >
                  QTY
                </th>
                <th
                  scope="col"
                  class="text-center"
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
                <td class="text-no-wrap">
                  {{ index+1 }}
                </td>
                <td class="text-no-wrap">
                  {{ item.product.SKU }}
                </td>
                <td class="text-no-wrap">
                  <p class="text-sm-caption mb-0">{{item.product.brand}}</p>
                  {{ item.product.name }}

                </td>
                <td class="text-no-wrap">
                  {{ item.product.description }}
                </td>
                <td class="text-center">
                  {{ item.unit_price }} DZD
                </td>
                <td class="text-center">
                  {{ item.quantity }}
                </td>
                <td class="text-center">
                  {{ item.total_price }} DZD
                </td>
              </tr>
            </tbody>
          </VTable>

          <VDivider class="my-2" />

          <!-- Total -->
          <VCardText class="d-flex justify-space-between flex-column flex-sm-row print-row">
            <div class="my-2 mx-sm-4 v-col-md-6">

              <h5>Invoice's Final Amount : {{ sale.amount_letter }}</h5>
            </div>

            <div class="my-2 mx-sm-4 v-col-md-4">
              <table>
                <tr>
                  <td class="text-end">
                    <div class="me-5">
                      <p class="mb-2">
                        Invoice TOTAL:
                      </p>
                      <p class="mb-2">
                        payment:
                      </p>
                      <p class="mb-2">
                        New Balance :
                      </p>
                    </div>
                  </td>

                  <td class="font-weight-semibold">
                    <p class="mb-2">
                      {{ (sale.total_amount) }} DZD
                    </p>
                    <p class="mb-2">
                      {{ (sale.payment_total) }} DZD
                    </p>
                    <p class="mb-2">
                      {{ (sale.balance) }} DZD
                    </p>

                  </td>
                </tr>
              </table>
            </div>
          </VCardText>

          <VDivider />

          <VCardText>
            <div class="d-flex mx-sm-4">
              <h6 class="text-sm font-weight-semibold me-1">
                Note:
              </h6>
              <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance projects. Thank You!</span>
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
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- 👉 Add Payment Sidebar -->
    <InvoiceAddPaymentDrawer v-model:isDrawerOpen="isAddPaymentSidebarVisible" />

    <!-- 👉 Send Invoice Sidebar -->
    <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendPaymentSidebarVisible" />
  </section>
</template>

<style lang="scss">
@media print {
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
  .v-col-md-5 {
    flex: 0 0 41.6666666667% !important;
    max-width: 41.6666666667% !important;
  }
  .v-col-md-7 {
    flex: 0 0 58.3333333333% !important;
    max-width: 58.3333333333% !important;
  }
  .v-col-md-4 {
    flex: 0 0 33.3333333333%;
    max-width: 33.3333333333%;
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
    margin: 17px !important;
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
    padding: 10px 0 10px 15px;

    background-color: white;
    border-radius: 19px;
    margin-right: 12px;
    border: 1px solid #e4e4e4;
    margin-bottom: 6px;
  }
}
.text-color-black{
  color: black !important;
}
.invoiceGeneral{
  background-color: white !important;
  border-radius: 10px !important;
  padding: 0px !important;
  margin: 17px !important;
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
  padding: 10px 0 10px 15px;

  background-color: white;
  border-radius: 19px;
  margin-right: 12px;
  border: 1px solid #e4e4e4;
  margin-bottom: 6px;
}

</style>
