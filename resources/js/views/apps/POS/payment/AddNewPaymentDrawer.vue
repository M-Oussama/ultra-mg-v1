<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
const props = defineProps({
  isDrawerOpen: {
    type: null,
    required: true,
  },
  data:{
    type: null,
    required:true
  },
  clients:{
    type: null,
    required:true
  },
  loading:{
    type:null,
    required: true
  }
})
const isFormValid = ref(false)
const refForm = ref()
const saleStore = useSaleStore()
const payment = ref({
  balance:0,
  amount:"0",
  date:'',
  note:'',
  client:{
    id:-1
  }
})
const data = ref();
const client = ref({
  id:-1,
})
const emit = defineEmits([

  'paymentData',
])
// 👉 drawer close
const closeNavigationDrawer = () => {
  props.isDrawerOpen.open = false;
}
const onSubmit = () => {
  if(payment.value.client.id === -1 ||
    payment.value.amount === "0" ||
    payment.value.amount === "" ||
    payment.value.date ==="" ) {

    if(payment.value.client.id === -1){
      errorsMiddleware(
        "Client not selected",
        "Oops! Looks like you forgot to select a client. Kindly pick at least one client to proceed"
      )
    }
    if(payment.value.amount === "0"){
      errorsMiddleware(
        "Amount cannot be 0",
        "The amount value must be higher than 0"
      )
    }
    if(payment.value.amount === ""){
      errorsMiddleware(
        "Amount is Empty",
        "You must enter a value in the amount field"
      )
    }
    if(payment.value.date ===""){
      errorsMiddleware(
        "Payment Date not selected",
        "Please select the payment date"
      )
    }
  } else {
    console.log("payment")
    console.log(payment)
    handleDrawerModelValueUpdate()
      props.loading.isActive = true;
      saleStore.storePayment(payment.value).then(response => {
       props.loading.isActive = false;
       payment.value.amount = 0;
       payment.value.date = '';
       payment.value.note = '';
       payment.value.client.id = -1;
       successMiddleware('Payment created Successfully')
        emit('paymentData', {
          data
        })
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
  return `${item.name}  ${item.surname}`
}

const handleDrawerModelValueUpdate = val => {

  console.log(val);
  props.isDrawerOpen.open = false;
}

</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen.open"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Add Payment
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
              <VCol cols="12">
                <model-list-select
                  :list="props.clients"
                  v-model="payment.client"
                  option-value="id"
                  :custom-text="fullName"
                  :hideSelectedOptions="true"
                  placeholder="Select Client">
                </model-list-select>
              </VCol>
              <!-- 👉  name -->
              <VCol cols="12">
                <VTextField
                  type="number"
                  v-model="payment.amount"
                  min="0"
                  label="Payment Amount"

                />
              </VCol>
              <!-- 👉 surname -->
              <VCol cols="12">
                <AppDateTimePicker
                  v-model="payment.date"
                  label="Payment Date"
                />
              </VCol>

              <VCol cols="12">
                <VTextarea
                  v-model="payment.note"
                  label="Internal Payment Note"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
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

<style>

</style>
