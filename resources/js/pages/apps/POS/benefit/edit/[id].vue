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

const saleStore = useSaleStore()
const loading = ref({
  isActive :false
})
const invoice_id = ref(null)

// 👉 fetchInvoice
saleStore.getArticlesBenefit(Number(route.params.id)).then(response => {
  loading.isActive = false;
  articles.value = response.data.articles

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
 var jerryCan =  parseFloat(article.weight) * (parseFloat(article.raw_material_price) /1000);
  var benefit = jerryCan > 0 ? jerryCan + 5+1: 0;

   article.benefit =   parseFloat(article.price) - benefit;

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
            <VRow v-for="article in articles">
              <VCol
                cols="12"
                md="4"
              >
                <VTextField
                  v-model="article.product.name"
                  label="Name of the product"
                  placeholder="Name of the product"

                />
              </VCol>

              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  type="number"
                  v-model="article.weight"

                  label="Product's Weight"
                  placeholder="Weight of the product"

                  @update:modelValue="ondataChanged(article)"
                />
              </VCol>
              <VCol
                cols="12"
                md="2"
              >
                <VTextField
                  v-model="article.raw_material_price"
                  type="number"

                  label="RM price KG"
                  placeholder="price of the raw material per KG"

                  @update:modelValue="ondataChanged(article)"
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

                />
              </VCol>


            </VRow>
            <VCol cols="12">
              <VBtn
                type="submit"
                @click="refForm?.validate()"
              >
                Submit
              </VBtn>
            </VCol>
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

