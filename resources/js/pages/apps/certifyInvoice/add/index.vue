<script setup>
import InvoiceEditable from '@/views/apps/invoice/InvoiceEditable.vue'
import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";

const invoiceData2 = ref({
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
  certify_invoice_products:[],
  clients: [],
  products: [],
  unSelectedProducts: [],
})
const router = useRouter()
const paymentTerms = ref(true)
const clientNotes = ref(false)
const paymentStub = ref(false)
const loading = ref({
  isActive: false
})
const invoice_id = ref(null)
const saved = ref(false)

const paymentMethods = [
  'Espece',
  'Cheque',
  'Versement Bancaire',
]

const certifyInvoiceListStore = useCertifyInvoiceListStore()

// 👉 fetchData
certifyInvoiceListStore.fetchData().then(response => {

  invoiceData2.value.clients = response.data.clients
  invoiceData2.value.products = response.data.products
  invoiceData2.value.id = response.data.id
  //companyProfile.value = response.data.companyProfile
}).catch(err => {
  console.log(err)
})


const saveInvoice = () => {


  if(
    invoiceData2.value.client.id === -1 ||
    invoiceData2.value.date === null ||
    invoiceData2.value.certify_invoice_products.length === 0 ||
    invoiceData2.value.payment_type === ""
  ) {
    if(invoiceData2.value.client.id === -1) {
      errorsMiddleware(
        "Client Not Selected",
        "Oops! Looks like you forgot to select a client for this invoice. Kindly pick at least one client to proceed"
      )
    }
    if( invoiceData2.value.date  === null) {
      errorsMiddleware(
        "The date field is empty",
        "Date field remains empty, and no client has been chosen for the invoice."
      )
    }

    if( invoiceData2.value.certify_invoice_products.length === 0) {
      errorsMiddleware(
        "Select a least one product.",
        "Kindly ensure you've selected at least one product before proceeding."
      )
    }

    if(invoiceData2.value.payment_type === "") {
      errorsMiddleware(
        "Choose a payment method.",
        "Please select a payment method to complete your invoice."
      )
    }
  } else {

    if(!loading.isActive) {
       loading.isActive = true;
      certifyInvoiceListStore.addCertifyInvoice(invoiceData2.value).then( response => {
        loading.isActive = false;
        saved.value = true;
        invoice_id.value = response.data.id
        successMiddleware('Invoice created Successfully')
        router.push('/apps/certifyinvoice/preview/'+invoice_id.value)

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
  <Div

    id="invoice-add"
  >
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
          <!-- 👉 InvoiceEditable -->
          <VCol
            cols="12"
            md="9"
          >
            <InvoiceEditable :data="invoiceData2" :loading="loading" />
          </VCol>

          <!-- 👉 Right Column: Invoice Action -->
          <VCol
            cols="12"
            md="3"
          >
            <VCard class="mb-8">
              <VCardText>
                <!-- 👉 Send Invoice -->
                <VBtn
                  block
                  prepend-icon="tabler-send"
                  class="mb-2"
                >
                  Send Invoice
                </VBtn>
                <VBtn
                  v-if="saved"
                  block
                  color="default"
                  variant="tonal"
                  class="mb-2"
                  :to="{ name: 'apps-certifyInvoice-preview-id', params: { id: invoice_id } }">
                  Preview
                </VBtn>

                <!-- 👉 Save -->
                <VBtn
                  :loading="loading.isActive"
                  :disabled="loading.isActive"
                  block
                  color="success"
                  @click="saveInvoice"
                  v-if="!saved"
                >
                  Save
                </VBtn>
              </VCardText>
            </VCard>

            <!-- 👉 Select payment method -->
            <VSelect
              v-model="invoiceData2.payment_type"
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

            <!-- 👉  Client Notes -->
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

            <!-- 👉  Payment Stub -->
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
        </VRow>

  </Div>
</template>

<style lang="scss">

</style>
<route lang="yaml">
meta:
  action: list
  subject: certify_invoices
</route>
