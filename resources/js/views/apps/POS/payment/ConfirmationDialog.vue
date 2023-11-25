<script setup>
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
const saleStore = useSaleStore()
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  payment: {
    type: Object,
    required: true,
  },
  loading: {
    type: Object,
    required: true,
  },
  payments: {
    type: Object,
    required: true,
  },
})

const id = ref()

const emit = defineEmits([
  'paymentData',
])
// 👉 drawer close
const closeConfirmationDialog = () => {
  props.isDialogVisible.open = false;

}
// 👉 Watch for changes in the user prop and update form fields

const onSubmit = () => {
  props.loading.isActive = true;
  const id = props.payment.id;
  const index = props.payments.findIndex(p => p.id === id);

  saleStore.deletePayment(props.payment).then(response => {
    props.isDialogVisible.open = false;
    props.loading.isActive = false;
    successMiddleware('Payment Deleted Successfully')
    props.payments.splice(index,1);
    console.log("id: "+id)
    console.log("index: "+index)
  }).catch(error => {
    console.log(error)
    props.loading.isActive = false;
    errorsMiddleware(
      "Error Occured",
      "Oops! Looks like the payment has not been deleted "
    )
  })
  props.isDialogVisible.open = false;
}


</script>

<template>
  <VDialog
    v-model="props.isDialogVisible"
    persistent
    class="v-dialog-sm"

  >

    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeConfirmationDialog()" />

    <!-- Dialog Content -->
    <VCard title="Confirmation">
      <VCardText>
        Are you sure you want to delete this payment ?
      </VCardText>

      <VCardText class="d-flex justify-end gap-3 flex-wrap">
        <VBtn
          color="secondary"
          variant="tonal"
          @click="props.isDialogVisible.open = false"
        >
          Disagree
        </VBtn>
        <VBtn @click="onSubmit">
          Agree
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style scoped>

</style>
