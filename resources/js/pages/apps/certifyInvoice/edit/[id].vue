<script setup>
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import InvoiceEditable from '@/views/apps/certifyInvoice/InvoiceEditable.vue'

// Store
import { useInvoiceStore } from '@/views/apps/invoice/useInvoiceStore'

import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";
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
  }],
  clients: [],
  products: [],
  unSelectedProducts: [],
})
const invoiceListStore = useCertifyInvoiceListStore()

// 👉 fetchInvoice
invoiceListStore.fetchInvoice(Number(route.params.id)).then(response => {
  invoiceData.value = response.data.invoice
  invoiceData.value.clients = response.data.clients
  invoiceData.value.products = response.data.products
  invoiceData.value.unSelectedProducts = response.data.unSelectedProducts

}).catch(error => {
  console.log(error)
})

const isSendSidebarActive = ref(false)
const isAddPaymentSidebarActive = ref(false)
const paymentTerms = ref(true)
const clientNotes = ref(false)
const paymentStub = ref(false)
const selectedPaymentMethod = ref('Bank Account')

const paymentMethods = [
  'Bank Account',
  'PayPal',
  'UPI Transfer',
]
const saveInvoice = () => {
  console.log(invoiceData)
}
</script>

<template>
  <VRow>
    <!-- 👉 InvoiceEditable   -->
    <VCol
      cols="12"
      md="9"
    >
      <InvoiceEditable :data="invoiceData" />
    </VCol>

    <!-- 👉 Right Column: Invoice Action -->
    <VCol
      cols="12"
      md="3"
    >
      <VCard class="mb-8">
        <VCardText>
          <!-- 👉 Send Invoice Trigger button -->
          <VBtn
            block
            prepend-icon="tabler-send"
            class="mb-2"
            @click="isSendSidebarActive = true"
          >
            Send Invoice
          </VBtn>

          <div class="d-flex gap-2">
            <div class="w-50">
              <!-- 👉  Preview button -->
              <VBtn
                block
                color="secondary"
                variant="tonal"
                class="mb-2"
                :to="{ name: 'apps-invoice-preview-id', params: { id: route.params.id } }"
              >
                Preview
              </VBtn>
            </div>

            <div class="w-50">
              <!-- 👉 Save button -->
              <VBtn
                block
                color="secondary"
                variant="tonal"
                class="mb-2"
                @click="saveInvoice"
              >
                Save
              </VBtn>
            </div>
          </div>

          <!-- 👉 Add Payment trigger button -->
          <VBtn
            block
            prepend-icon="tabler-currency-dollar"
            @click="isAddPaymentSidebarActive = true"
          >
            Add Payment
          </VBtn>
        </VCardText>
      </VCard>

      <!-- 👉 Accept payment via  -->
      <VSelect
        v-model="selectedPaymentMethod"
        :items="paymentMethods"
        label="Accept Payment Via"
        class="mb-6"
      />

      <!-- 👉 Payment Terms -->
      <div class="d-flex align-center justify-space-between">
        <VLabel for="payment-terms">
          Payment Terms
        </VLabel>
        <div>
          <VSwitch
            id="payment-terms"
            v-model="paymentTerms"
          />
        </div>
      </div>

      <!-- 👉 Client Notes -->
      <div class="d-flex align-center justify-space-between">
        <VLabel for="client-notes">
          Client Notes
        </VLabel>
        <div>
          <VSwitch
            id="client-notes"
            v-model="clientNotes"
          />
        </div>
      </div>

      <!-- 👉 Payment Stub -->
      <div class="d-flex align-center justify-space-between">
        <VLabel for="payment-stub">
          Payment Stub
        </VLabel>
        <div>
          <VSwitch
            id="payment-stub"
            v-model="paymentStub"
          />
        </div>
      </div>
    </VCol>

    <!-- 👉 Invoice send drawer -->
    <InvoiceSendInvoiceDrawer v-model:isDrawerOpen="isSendSidebarActive" />

    <!-- 👉 Invoice add payment drawer -->
    <InvoiceAddPaymentDrawer v-model:isDrawerOpen="isAddPaymentSidebarActive" />
  </VRow>
</template>

