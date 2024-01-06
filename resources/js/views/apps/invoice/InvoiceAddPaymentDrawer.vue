<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { onMounted } from 'vue';
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";
const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  data:{
    type: null,
    required:true
  },
  loading:{
    type:null,
    required: true
  }
})
watch(props.isDrawerOpen, ()=>{

})
const emit = defineEmits([
  'update:isDrawerOpen',
  'submit',
])
const saleStore = useSaleStore()
const payment = ref({
  balance:props.data.balance,
  amount:0,
  date:'',
  note:'',
})

const onSubmit = () => {
  handleDrawerModelValueUpdate()
  payment.value.sale = props.data;
  props.loading.isActive = true;
  saleStore.addPayment(payment.value, payment.value.sale.id).then(response => {

    props.loading.isActive = false;
    props.data.balance = payment.value.balance;

    props.data.payment_total = parseFloat(props.data.payment_total);

    payment.value.amount = 0;
    payment.value.date = '';
    payment.value.note = '';
    successMiddleware('Payment added Successfully')
  }).catch(error => {
    console.log(error)
    props.loading.isActive = false;
    errorsMiddleware(
      "Error Occured",
      "Oops! Looks like the payment has not been submited "
    )
  })
}
const balance = props.data.balance;

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const initialize = () => {
  // ...

  console.log(payment.balance)
};
onMounted(() => initialize());
const recalculate = () => {

  var amount = parseFloat(payment.value.amount === "" ? 0: payment.value.amount);
  payment.value.balance = parseFloat(balance) - amount;

  console.log(payment.value.amount)
}

</script>
<template>
  <VNavigationDrawer
    temporary
    location="end"
    :width="400"
    :model-value="props.isDrawerOpen"
    class="scrollable-content"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Add Payment
      </h6>

      <VSpacer />

      <VBtn
        icon
        size="32"
        color="default"
        variant="tonal"
        class="rounded"
        @click="handleDrawerModelValueUpdate(false)"
      >
        <VIcon
          size="18"
          icon="tabler-x"
        />
      </VBtn>
    </div>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <VForm @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <VTextField
                  disabled
                  v-model="payment.balance"
                  label="Invoice Balance"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="payment.amount"
                  label="Payment Amount"
                  @keyup="recalculate"
                />
              </VCol>

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

              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Send
                </VBtn>

                <VBtn
                  type="reset"
                  color="secondary"
                  variant="tonal"
                  @click="$emit('update:isDrawerOpen', false)"
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
