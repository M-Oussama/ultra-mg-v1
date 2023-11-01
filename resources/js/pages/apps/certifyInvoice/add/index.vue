<script setup>
import InvoiceEditable from '@/views/apps/invoice/InvoiceEditable.vue'
import {useCertifyInvoiceListStore} from "@/views/apps/certifyInvoice/useCertifyInvoiceListStore";

const invoiceData = ref({
  invoice: {
    id: 5037,
    issuedDate: '',
    service: '',
    total: 0,
    avatar: '',
    invoiceStatus: '',
    balance: '',
    dueDate: '',
    client: {
      address: '',
      company: '',
      companyEmail: '',
      contact: '',
      country: '',
      name: '',
    },
  },
  paymentDetails: {
    totalDue: '$12,110.55',
    bankName: 'American Bank',
    country: 'United States',
    iban: 'ETD95476213874685',
    swiftCode: 'BR91905',
  },
  purchasedProducts: [{
    id: '',
    name: '',
    brand: '',
    description: '',
    product_code: '',
    SKU: '',
    price: 15,
    stockable: false,
    tax_rate: 0.5,
  }],
  clients:[],
  products:[],
  note: '',
  paymentMethod: '',
  selectedPaymentMethod: '',
  salesperson: '',
  thanksNote: '',
})

const paymentTerms = ref(true)
const clientNotes = ref(false)
const paymentStub = ref(false)
const loading = ref(false)
const paymentMethods = [
  'Espece',
  'Cheque',
  'Versement Bancaire',
]

const certifyInvoiceListStore = useCertifyInvoiceListStore()

// 👉 fetchClients
certifyInvoiceListStore.fetchData().then(response => {
  invoiceData.value.clients = response.data.clients
  invoiceData.value.products = response.data.products
  //companyProfile.value = response.data.companyProfile
}).catch(err => {
  console.log(err)
})

const saveInvoice = () => {
  if(!loading){
    loading.value = true;
  }

  console.log(invoiceData);

 /*certifyInvoiceListStore.addCertifyInvoice().then(response => {


  }).catch(err => {
    console.log(err)
  })*/
}


</script>

<template>
  <VRow>
    <!-- 👉 InvoiceEditable -->
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
          <!-- 👉 Send Invoice -->
          <VBtn
            block
            prepend-icon="tabler-send"
            class="mb-2"
          >
            Send Invoice
          </VBtn>

          <!-- 👉 Preview -->
          <VBtn
            block
            color="default"
            variant="tonal"
            class="mb-2"
            :to="{ name: 'apps-invoice-preview-id', params: { id: '5036' } }"
          >
            Preview
          </VBtn>

          <!-- 👉 Save -->
          <VBtn
            :loading="loading"
            :disabled="loading"
            block
            color="success"

            @click="saveInvoice"
          >
            Save
          </VBtn>
        </VCardText>
      </VCard>

      <!-- 👉 Select payment method -->
      <VSelect
        v-model="invoiceData.selectedPaymentMethod"
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
</template>

