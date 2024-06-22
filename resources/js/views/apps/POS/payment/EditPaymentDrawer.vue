<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import  ListDualBox from '@/plugins/vue-list-dual-box/update-list-dual-box.vue'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  payment: {
    type: Object,
    required: true,
  },
  clients: {
    type: Object,
    required: true,
  },
  loading: {
    type: Object,
    required: true,
  },
})
const saleStore = useSaleStore()

// 👉 Watch for changes in the Client prop and update form fields
watch(() => props.payment, (newPayment) => {
  console.log("newPayment")
  console.log(newPayment)
  if (newPayment) {
   /* payment.id = newPayment.id || -1
    payment.sale_id = newPayment.sale_id || ''
    payment.client_id = newPayment.client_id || ""
    payment.amount_paid = newPayment.amount_paid || ""
    payment.payment_date = newPayment.payment_date || ""
    payment.note = newPayment.note || ""*/

  }
});
watch(props.isDrawerOpen, ()=>{

  if(props.isDrawerOpen.open){

    payment.value.id = props.payment.id || -1
    payment.value.sale_id =  props.payment.sale_id || ''
    payment.value.client_id =  props.payment.client_id || ""
    payment.value.amount_paid =  props.payment.amount_paid || ""
    payment.value.payment_date =  props.payment.payment_date || ""
    payment.value.note =  props.payment.note || ""

  }
})

const emit = defineEmits([
  'paymentData',
])
const payment = ref({
  id:0,
  sale_id:"",
  client_id:-1,
  amount_paid:'',
  payment_date:'',
  note:'',

})

const isFormValid = ref(false)
const refForm = ref()
const notPaidInvoices = ref([])
const notPaidInvoicesTemp = ref([])
const paidInvoices = ref([])


// 👉 drawer close
const closeNavigationDrawer = () => {
  props.isDrawerOpen.open = false;
}

const onSubmit = () => {

  if(payment.client_id === -1 ||
    payment.amount_paid === "0" ||
    payment.payment_date === "" ||
    payment.note ==="" ) {

    if(payment.client_id === -1){
      errorsMiddleware(
        "Client not selected",
        "Oops! Looks like you forgot to select a client. Kindly pick at least one client to proceed"
      )
    }
    if(payment.amount_paid === "0"){
      errorsMiddleware(
        "Amount cannot be 0",
        "The amount value must be higher than 0"
      )
    }
    if(payment.amount_paid === ""){
      errorsMiddleware(
        "Amount is Empty",
        "You must enter a value in the amount field"
      )
    }
    if(payment.payment_date ===""){
      errorsMiddleware(
        "Payment Date not selected",
        "Please select the payment date"
      )
    }
  } else {
    props.loading.isActive = true;
    saleStore.updatePaymentWithInvoices(payment.value, paidInvoices.value).then(response => {
      props.isDrawerOpen.open = false;
      props.loading.isActive = false;
      emit('paymentData',1)

      successMiddleware('Payment updated Successfully')

    }).catch(error => {
      console.log(error)
      props.loading.isActive = false;
      errorsMiddleware(
        "Error Occured",
        "Oops! Looks like the payment has not been submited "
      )
    })
  }

}

const fullName = item => {
  return `${item.name}  ${item.surname? item.surname : ''}`
}

const selectedClient = ref()
const toggleSwitch = ref(false)

const clientChanged = (client)=> {
  if(selectedClient.value === undefined) {
    selectedClient.value = {...client};
    getInvoices(selectedClient.value.id)
  } else {
    if(client.id !== selectedClient.value.id) {
      selectedClient.value = {...client};
      getInvoices(selectedClient.value.id)
    }
  }

}
const getInvoices = (id) => {
  props.loading.isActive = true
  saleStore.getPaidInvoices(id, payment.value.id).then(response => {
    notPaidInvoices.value = response.data.notPaidInvoices
    notPaidInvoicesTemp.value = response.data.notPaidInvoices
    paidInvoices.value = response.data.paidInvoices
    props.loading.isActive = false;
  }).catch(error => {
    console.log(error)
    props.loading.isActive = false;
    errorsMiddleware(
      "Error Occured",
      "Oops! Looks like the payment has not been submited "
    )
  })
}
const capitalizedLabel = label => {
  const convertLabelText = label.toString()

  return convertLabelText.charAt(0).toUpperCase() + convertLabelText.slice(1)
}
const activateDualBox = value => {
   if(payment.value.client_id > 0) {
      getInvoices(payment.value.client_id)
   }

}

</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen.open"

  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Edit Payment
      </h6>

      <VSpacer />

      <!-- 👉 Close btn -->
      <VBtn
        variant="tonal"
        color="default"
        icon
        size="32"
        class="rounded"
        @click="closeNavigationDrawer"
      >
        <VIcon
          size="18"
          icon="tabler-x"
        />
      </VBTn>
    </div>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
<!--              <VCol cols="12">-->
<!--                <model-list-select-->
<!--                  :list="props.clients"-->
<!--                  v-model="payment.client_id"-->
<!--                  option-value="id"-->
<!--                  :custom-text="fullName"-->
<!--                  :hideSelectedOptions="true"-->
<!--                  @update:modelValue="clientChanged"-->
<!--                  placeholder="Select Client">-->
<!--                </model-list-select>-->
<!--              </VCol>-->
              <!-- 👉  name -->
              <VCol cols="12">
                <VTextField
                  type="number"
                  v-model="payment.amount_paid"
                  min="0"
                  label="Payment Amount"

                />
              </VCol>


              <VCol cols="12" class="align-content-normal">
                <Label>Activate DualBox

                </Label>
                <VSwitch
                  v-model="toggleSwitch"
                  color="success"
                  @update:modelValue="activateDualBox"
                />
              </VCol>
                <VCol cols="12" v-if="toggleSwitch">
                <list-dual-box
                  :amount="payment.amount_paid"
                  :left="notPaidInvoices"
                  :leftTemp="notPaidInvoicesTemp"
                  :right="paidInvoices"
                ></list-dual-box>
              </VCol>
              <!-- 👉 surname -->
              <VCol cols="12">
                <AppDateTimePicker
                  v-model="payment.payment_date"
                  label="Payment Date"
                />
              </VCol>

              <VCol cols="12">
                <VTextarea
                  v-model="payment.note"
                  label="Internal Payment Note"
                />
              </VCol>
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Submit
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>

<style lang="scss">
.align-content-normal{
  display: flex;
  justify-content: normal;

  & > Label {
    margin-top: 2%;
    margin-right: 4%;
    }
}

</style>
