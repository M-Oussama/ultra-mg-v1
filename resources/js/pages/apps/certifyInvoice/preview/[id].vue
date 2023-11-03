<script setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

// Components
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'

// Store
import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";

const invoiceListStore = useCertifyInvoiceListStore()
const route = useRoute()
const invoiceData = ref({
  amount:0,
  fac_id: 3,
  date: "2023-11-03",
  client_id: 1,
  payment_type: "Espece",
  amount_letter: "",
  client:{
    id: 1,
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
  certify_invoice_products:[{
    price: 1,
    quantity: 20,
    total: 20,
    id: 1,
    product : {
      name: "Sample Product",
      brand: "Sample Brand",
      description: "Product Description",
      product_code: "PROD123",
      category_id: 1,
      SKU: "SKU123",
      min_stock_level: 10,
      price: "1.00",
      stockable: 0,
      tax_rate: "0.08",
      product_stock: {
        id: 1,
        product_id: 1,
        quantity: 20,
      }
    }
  }]
})
const paymentDetails = ref()
const isAddPaymentSidebarVisible = ref(false)
const isSendPaymentSidebarVisible = ref(false)

// 👉 fetchInvoice
invoiceListStore.fetchInvoice(Number(route.params.id)).then(response => {
  invoiceData.value = response.data.invoice
  //paymentDetails.value = response.data.paymentDetails
}).catch(error => {
  console.log(error)
})

// ℹ️ Your real data will contain this information
const purchasedProducts = [
  {
    name: 'Premium Branding Package',
    description: 'Branding & Promotion',
    qty: 1,
    hours: 15,
    price: 32,
  },
  {
    name: 'SMM',
    description: 'Social media templates',
    qty: 1,
    hours: 14,
    price: 28,
  },
  {
    name: 'Web Design',
    description: 'Web designing package',
    qty: 1,
    hours: 12,
    price: 24,
  },
  {
    name: 'SEO',
    description: 'Search engine optimization',
    qty: 1,
    hours: 5,
    price: 22,
  },
]

// 👉 Print Invoice
const printInvoice = () => {
  window.print()
}
</script>

<template>
  <section v-if="invoiceData">
    <VRow>
      <VCol
        cols="12"
        md="9"
      >
        <VCard>
          <!-- SECTION Header -->
          <VCardText class="d-flex flex-wrap justify-space-between flex-column flex-sm-row print-row">
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
                Facture #{{ invoiceData.id }}
              </h6>

              <!-- 👉 Issue Date -->
              <p class="mb-2">
                <span>Date Issued: </span>
                <span class="font-weight-semibold">{{ invoiceData.date }}</span>
              </p>
            </div>
          </VCardText>
          <!-- !SECTION -->

          <VDivider />

          <!-- 👉 Payment Details -->
          <VCardText class="d-flex justify-space-between flex-wrap flex-column flex-sm-row print-row">
            <div class="ma-sm-4">
              <h6 class="text-sm-h6 font-weight-semibold mb-3">
                Invoice:
              </h6>

              <p class="mb-1">
                Invoice: #FAJ/2023/{{ invoiceData.id }}
              </p>
              <p class="mb-1">
                Payment Method : {{ invoiceData.payment_type }}
              </p>
              <p class="mb-1">
                amount : {{ invoiceData.amount }}
              </p>
              <p class="mb-1">
                date : {{ invoiceData.date }}
              </p>

            </div>

            <div class="ma-sm-4">
              <h6 class="text-sm-button font-weight-semibold">
                Information:
              </h6>

              <p class="mb-1 ">
                Client: {{ invoiceData.client.name }}
              </p>
              <p class="mb-1">
                Address : {{ invoiceData.client.address }}
              </p>
              <p class="mb-1">
                phone : {{ invoiceData.client.phone }}
              </p>
              <p class="mb-1">
                email : {{ invoiceData.client.email }}
              </p>

            </div>
            <div class="ma-sm-4">
              <h6 class="text-sm-button font-weight-semibold ">
               Identifiers:
              </h6>
              <p class="mb-1">
                N°RC:  {{ invoiceData.client.NRC }}
              </p>
              <p class="mb-1">
                N°IF: {{invoiceData.client.NIF }}
              </p>
              <p class="mb-1">
                N°IS:  {{ invoiceData.client.NIS }}
              </p>
              <p class="mb-1">
                N°ART:{{ invoiceData.client.NART }}
              </p>

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
                v-for="(item,index) in invoiceData.certify_invoice_products"
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
                  {{ item.price.toFixed(2) }} DZD
                </td>
                <td class="text-center">
                  {{ item.quantity }}
                </td>
                <td class="text-center">
                  {{ item.total.toFixed(2) }} DZD
                </td>
              </tr>
            </tbody>
          </VTable>

          <VDivider class="my-2" />

          <!-- Total -->
          <VCardText class="d-flex justify-space-between flex-column flex-sm-row print-row">
            <div class="my-2 mx-sm-4">

              <h5>Invoice's Final Amount : {{ invoiceData.amount_letter }}</h5>
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
                      {{ (invoiceData.amount).toFixed(2) }} DZD
                    </p>
                    <p class="mb-2">
                      {{ (invoiceData.amount*0.19).toFixed(2) }} DZD
                    </p>
                    <p class="mb-2">
                      {{ (invoiceData.amount*1.19).toFixed(2) }} DZD
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
              :to="{ name: 'apps-certifyInvoice-edit-id', params: { id: route.params.id } }"
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

  .v-card {
    box-shadow: none !important;

    .print-row {
      flex-direction: row !important;
    }
  }

  .layout-content-wrapper {
    padding-inline-start: 0 !important;
  }
}
</style>
