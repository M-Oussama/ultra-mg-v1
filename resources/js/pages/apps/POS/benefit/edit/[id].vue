<script setup>
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import InvoiceEditable from '@/views/apps/POS/sales/InvoiceEditable.vue'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
const route = useRoute()
const router = useRouter()
const articles = ref([])
const _benefit = ref({
  raw_material_price : 0,
  total_profit: 0
})
const total_profits = ref(0)

const saleStore = useSaleStore()
const loading = ref({
  isActive :false
})
const invoice_id = ref(null)

const fetchList = () => {
  saleStore.getArticlesBenefit(Number(route.params.id)).then(response => {
    loading.isActive = false;
    articles.value = response.data.articles
    _benefit.value = response.data.benefit
    console.log(_benefit.value.benefit)
    _benefit.value.total_profit = _benefit.value.benefit

  }).catch(error => {
    loading.isActive = false;
    console.log(error)
  })
}
// 👉 fetchInvoice
watchEffect(fetchList)

const isSendSidebarActive = ref(false)
const isAddPaymentSidebarActive = ref(false)
const paymentTerms = ref(true)
const clientNotes = ref(false)
const paymentStub = ref(false)
const saved = ref(false)
const raw_material_price = ref(0)

const statusName = item => {
  return `${item.name}`
}

const saveInvoice = () => {
  //
  //
  // if(
  //   sale.value.client.id === -1 ||
  //   sale.value.sale_date === null ||
  //   sale.value.sale_items.length === 0
  //
  // ) {
  //   if(sale.value.client.id === -1) {
  //     errorsMiddleware(
  //       "Client Not Selected",
  //       "Oops! Looks like you forgot to select a client for this invoice. Kindly pick at least one client to proceed"
  //     )
  //   }
  //   if( sale.value.sale_date  === null) {
  //     errorsMiddleware(
  //       "The date field is empty",
  //       "Date field remains empty, and no client has been chosen for the invoice."
  //     )
  //   }
  //
  //   if( sale.value.sale_items.length === 0) {
  //     errorsMiddleware(
  //       "Select a least one product.",
  //       "Kindly ensure you've selected at least one product before proceeding."
  //     )
  //   }
  //
  // } else {
  //
  //   if(!loading.isActive) {
  //     loading.isActive = true;
  //     saleStore.updateSale(sale.value).then( response => {
  //       loading.isActive = false;
  //       saved.value = true;
  //       invoice_id.value = response.data.id
  //       successMiddleware('Sale created Successfully')
  //       router.push('/apps/pos/sale/preview/'+invoice_id.value)
  //
  //     }).catch(err => {
  //       loading.isActive = false;
  //       console.log(err)
  //       errorsMiddleware('Error','')
  //       console.log(err)
  //     })
  //   }
  // }
}

const ondataChanged = (article)=> {
 var jerryCan =  parseFloat(article.weight) * (parseFloat(_benefit.value.raw_material_price) /1000);

  var benefit = jerryCan > 0 ? jerryCan + 5+1: 0;

   article.benefit =   parseFloat(article.product_price) - benefit;

   article.total_profit = parseFloat(article.total_amount) - (parseFloat(article.benefit) * parseFloat(article.quantity))

  calculateTotalProfits()
}
const updateRawMaterial = ()=> {
  for (let i = 0; i <articles.value.length ; i++) {
    var jerryCan =  parseFloat(articles.value[i].weight) * (parseFloat(_benefit.value.raw_material_price) /1000);

    var benefit = jerryCan > 0 ? jerryCan + 5+1: 0;

    articles.value[i].benefit =   parseFloat(articles.value[i].product_price) - benefit;
    articles.value[i].total_profit = parseFloat(articles.value[i].total_amount) - (parseFloat(articles.value[i].benefit) * parseFloat(articles.value[i].quantity))
  }
  calculateTotalProfits()

}
const calculateTotalProfits = () =>{
  total_profits.value = 0;
  for (let i = 0; i <articles.value.length ; i++) {
    total_profits.value += parseFloat(articles.value[i].total_profit)
  }
  _benefit.value.total_profit = parseFloat(total_profits.value).toFixed(2)


}
const submitForm = () => {

  loading.value.isActive = true;
  saleStore.updateProfit(articles.value, _benefit.value, Number(route.params.id)).then(response => {
    loading.value.isActive = false;
    successMiddleware(response.data.message)
    router.push('/apps/pos/benefit/list')

  }).catch(error => {
    loading.value.isActive = false;
    console.log(error)
  })
}
const refreshData = () => {

  loading.value.isActive = true;
  saleStore.refreshData(Number(route.params.id)).then(response => {
    console.log(response)
    loading.value.isActive = false;
    fetchList()
    successMiddleware(response.data.message)


  }).catch(error => {
    loading.value.isActive = false;
    console.log(error)
  })
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
      md="12"
    >

      <VCard>
        <VCardText class="d-flex flex-wrap justify-space-between flex-column">
          <VForm
            ref="refForm"
            @submit.prevent
          >
            <VRow>
              <VCol
                cols="6"
                md="4"
                class="mb-5 text-center"
              >
                <VLabel title="Raw material price per KG " class="mb-5"></VLabel>
                <VTextField
                  v-model="_benefit.raw_material_price"
                  type="number"

                  label="RM price KG"
                  placeholder="price of the raw material per KG"
                  @update:modelValue="updateRawMaterial()"

                />
              </VCol>
              <VCol
                cols="6"
                md="4"
                class="mb-5 text-center"
              >
                <VLabel title="Raw material price per KG " class="mb-5"></VLabel>
                <VTextField
                  v-model="_benefit.total_profit"
                  type="number"

                  readonly
                  label="TOTAL PROFIT"
                  placeholder="TOTAL PROFIT"


                />
              </VCol>



                    <VCol  cols="4"    md="1" class="mt-7">
                      <VBtn

                        @click="submitForm()"
                      >
                        Submit
                      </VBtn>
                    </VCol>
                    <VCol  cols="4"    md="1" class="mt-7">
                      <VBtn
                        color="success"
                        @click="refreshData()"
                      >
                        Refresh
                      </VBtn>
                    </VCol>









            </VRow>

            <VRow v-for="article in articles">
              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  v-model="article.product.name"
                  label="Name of the product"
                  placeholder="Name of the product"
                  readonly

                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  type="number"
                  v-model="article.product_price"
                  label="Product's Price"
                  placeholder="price of the product"
                  @update:modelValue="ondataChanged(article)"
                />
              </VCol>
              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  type="number"
                  v-model="article.benefit"
                  label="Product's Benefit"
                  placeholder="Benefit of the product"
                  readonly

                />
              </VCol>
              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  type="number"
                  v-model="article.quantity"
                  label="Product's Quantity"
                  placeholder="quantity of the product this month"
                  readonly

                />
              </VCol>
              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  type="number"
                  v-model="article.total_amount"
                  label="Product's total amount"
                  placeholder="total amount of the product"
                  readonly

                />
              </VCol>
              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  type="number"
                  v-model="article.total_profit"
                  label="Product's total profit"
                  placeholder="total profit of the product"
                  readonly

                />
              </VCol>

            </VRow>

          </VForm>
        </VCardText>
      </VCard>

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
                :to="{ name: 'apps-POS-sale-preview-id', params: { id: invoice_id } }">

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

<style>
.color-black{
  color:black !important;
}
</style>

