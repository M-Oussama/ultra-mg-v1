<script setup>
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import InvoiceEditable from '@/views/apps/POS/sales/InvoiceEditable.vue'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";
import {useReturnStore} from "@/views/apps/POS/return/usereturnStore";
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
const route = useRoute()
const router = useRouter()
const _product_return = ref({
  id:1,
  balance: 0,
  total_amount: 0,
  date: null,
  client_id: 1,
  city:null,
  cities: [],
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
  clients: [],
  products: [],
  sale_statues:[],
  payment: true,
  paymentAmount: 0,
  payment_total: 0,
  totalPayment:0,
  removePaymentRecords:false
})

const returnStore = useReturnStore()
const loading = ref({
  isActive :false
})
const invoice_id = ref(null)

// 👉 fetchInvoice
returnStore.getReturnData(Number(route.params.id)).then(response => {
  loading.isActive = false;
  _product_return.value = response.data.product_return
  _product_return.value.sale_date = _product_return.value.date
  _product_return.value.clients = response.data.clients
  _product_return.value.cities = response.data.cities
  _product_return.value.products = response.data.products
  _product_return.value.sale_statues = response.data.sale_statues

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

const statusName = item => {
  return `${item.name}`
}

const saveInvoice = () => {


  if(
    _product_return.value.client.id === -1 ||
    _product_return.value.date === null ||
    _product_return.value.sale_items.length === 0

  ) {
    if(_product_return.value.client.id === -1) {
      errorsMiddleware(
        "Client Not Selected",
        "Oops! Looks like you forgot to select a client for this invoice. Kindly pick at least one client to proceed"
      )
    }
    if( _product_return.value.date  === null) {
      errorsMiddleware(
        "The date field is empty",
        "Date field remains empty, and no client has been chosen for the invoice."
      )
    }

    if( _product_return.value._product_return_items.length === 0) {
      errorsMiddleware(
        "Select a least one product.",
        "Kindly ensure you've selected at least one product before proceeding."
      )
    }

  } else {

    if(!loading.isActive) {
      loading.isActive = true;
      returnStore.updateReturn(_product_return.value).then( response => {
        loading.isActive = false;
        saved.value = true;
        invoice_id.value = response.data.id
        successMiddleware('Product Return updated Successfully')
        router.push('/apps/pos/return/preview/'+invoice_id.value)

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
      <InvoiceEditable :data="_product_return" :loading="loading" />
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
                :to="{ name: 'apps-POS-return-preview-id', params: { id: invoice_id } }">

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
      <h6 class="text-sm font-weight-medium mb-3">
        Payment Method:
      </h6>


      <!-- 👉 Payment Terms -->
      <div class="mt-5 d-flex align-center justify-space-between">
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

