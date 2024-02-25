<script setup>
import InvoiceEditable from '@/views/apps/POS/sales/add/InvoiceEditable.vue'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'

/** DATA **/

const sale = ref({
  id:1,
  balance: 0,
  total_amount: 0,
  sale_date: new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+ new Date().getDate(),
  client_id: null,
  city:null,
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
  cities: [],

  sale_items:[],
  clients: [],
  products: [],
  sale_statues:[],
  payment: false,
  paymentAmount: 0,
})
const router = useRouter()
const paymentTerms = ref(true)
const clientNotes = ref(false)
const paymentStub = ref(false)
const cities = ref([])
const loading = ref({
  isActive: false
})
const invoice_id = ref(null)
const saved = ref(false)

const saleStore = useSaleStore()
loading.isActive = true;
// 👉 fetchData
saleStore.fetchData().then(response => {
  console.log(loading.isActive)
  sale.value.id = response.data.last_id
  sale.value.clients = response.data.clients
  sale.value.cities = response.data.cities
  sale.value.products = response.data.products
  sale.value.sale_statues = response.data.sale_statues
  //companyProfile.value = response.data.companyProfile
  loading.isActive = false;
  console.log(loading.isActive)
}).catch(err => {
  console.log(err)
  loading.isActive = false;
})

/** METHODS **/

const statusName = item => {
  return `${item.name}`
}

const saveInvoice = () => {


  if(
    sale.value.client.id === -1 ||
    sale.value.sale_date === null ||
    sale.value.sale_items.length === 0
  ) {
    if(sale.value.client.id === -1) {
      errorsMiddleware(
        "Client Not Selected",
        "Oops! Looks like you forgot to select a client for this invoice. Kindly pick at least one client to proceed"
      )
    }
    if( sale.value.sale_date  === null) {
      errorsMiddleware(
        "The date field is empty",
        "Date field remains empty, and no client has been chosen for the invoice."
      )
    }

    if( sale.value.sale_items.length === 0) {
      errorsMiddleware(
        "Select a least one product.",
        "Kindly ensure you've selected at least one product before proceeding."
      )
    }


  } else {

    if(!loading.isActive) {
      loading.isActive = true;
        saleStore.storeSale(sale.value).then( response => {

        saved.value = true;
        invoice_id.value = response.data.id
        successMiddleware('Sale created Successfully')
          loading.isActive = false;
        router.push('/apps/pos/sale/preview/'+invoice_id.value)

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
  <div

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
            <InvoiceEditable :data="sale" :loading="loading" />
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
                  :to="{ name: 'apps-POS-sale-preview-id', params: { id: invoice_id } }">
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

<!--            &lt;!&ndash; 👉 Select payment method &ndash;&gt;-->
<!--            <model-list-select-->
<!--              :list="sale.sale_statues"-->
<!--              v-model="sale.sale_status"-->
<!--              option-value="id"-->
<!--              :custom-text="statusName"-->
<!--              placeholder="Select Payment Type">-->
<!--            </model-list-select>-->

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

  </div>
</template>

<style lang="scss">

</style>
