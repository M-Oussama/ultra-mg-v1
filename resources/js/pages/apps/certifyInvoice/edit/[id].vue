<script setup>
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import InvoiceEditable from '@/views/apps/certifyInvoice/InvoiceEditable.vue'
import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";
const route = useRoute()
const router = useRouter()
const invoiceData = ref({
  amount:0,
  fac_id: 3,
  date: "2023-11-03",
  client_id: 1,
  payment_type: "Espece",
  amount_letter: "",
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

})
const invoiceListStore = useCertifyInvoiceListStore()
const loading = ref({
  isActive :false
})




// 👉 fetchInvoice
invoiceListStore.fetchInvoice(Number(route.params.id)).then(response => {
  loading.isActive = false;
  console.log("data");
  invoiceData.value = response.data.invoice
  invoiceData.value.clients = response.data.clients
  invoiceData.value.products = response.data.products

}).catch(error => {
  loading.isActive = false;
  console.log(error)
})

const isSendSidebarActive = ref(false)
const isAddPaymentSidebarActive = ref(false)
const paymentTerms = ref(true)
const clientNotes = ref(false)
const paymentStub = ref(false)
const saved = ref(false)

const paymentMethods = [
  'Espece',
  'Cheque',
  'Versement Bancaire',
]

const saveInvoice = () => {

  if(
    invoiceData.value.client.id === -1 ||
    invoiceData.value.date === null ||
    invoiceData.value.certify_invoice_products.length === 0 ||
    invoiceData.value.payment_type === ""
  ) {
    if(invoiceData.client.id === -1) {
      errorsMiddleware(
        "Client Not Selected",
        "Oops! Looks like you forgot to select a client for this invoice. Kindly pick at least one client to proceed"
      )
    }
    if(invoiceData.date === null) {
      errorsMiddleware(
        "The date field is empty",
        "Date field remains empty, and no client has been chosen for the invoice."
      )
    }

    if(invoiceData.certify_invoice_products.length === 0) {
      errorsMiddleware(
        "Select a least one product.",
        "Kindly ensure you've selected at least one product before proceeding."
      )
    }

    if(invoiceData.payment_type === "") {
      errorsMiddleware(
        "Choose a payment method.",
        "Please select a payment method to complete your invoice."
      )
    }
  } else {

    if(!loading.isActive) {
      loading.isActive = true;
      invoiceListStore.updateCertifyInvoice(invoiceData.value).then( response => {
        loading.isActive = false;
        saved.value = true;
        successMiddleware('Invoice updated Successfully')
        router.push({ name: 'apps-certifyInvoice-preview-id', params: { id: invoiceData.value.id} })

      }).catch(err => {
        loading.isActive = false;
        console.log(err)
        errorsMiddleware('Error','')
        console.log(err)
      })
    }
  }
}
</script>

<template>
  <VRow>
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



              <!-- 👉  Preview button -->
              <VBtn
                v-if="saved"
                block
                color="secondary"
                variant="tonal"
                class="mb-2"
                :to="{ name: 'apps-invoice-preview-id', params: { id: route.params.id } }"
              >
                Preview
              </VBtn>



              <!-- 👉 Save button -->
              <VBtn
                :loading="loading.isActive"
                :disabled="loading.isActive"
                block
                color="success"

                class="mb-2"
                v-if="!saved"
                @click="saveInvoice"
              >
                Save
              </VBtn>



<!--          &lt;!&ndash; 👉 Add Payment trigger button &ndash;&gt;-->
<!--          <VBtn-->
<!--            block-->
<!--            prepend-icon="tabler-currency-dollar"-->
<!--            @click="isAddPaymentSidebarActive = true"-->
<!--          >-->
<!--            Add Payment-->
<!--          </VBtn>-->
        </VCardText>
      </VCard>

      <!-- 👉 Accept payment via  -->
      <VSelect
        v-model="invoiceData.payment_type"
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
<!--    <InvoiceAddPaymentDrawer v-model:isDrawerOpen="isAddPaymentSidebarActive" />-->
  </VRow>
</template>

<route lang="yaml">
meta:
  action: list
  subject: certify_invoices
</route>
