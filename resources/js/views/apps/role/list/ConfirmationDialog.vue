<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  role: {
    type: Object,
    required: true,
  },
})

const id = ref()

const emit = defineEmits([
  'update:isDrawerOpen',
  'objectData',
])
// 👉 drawer close
const closeConfirmationDialog = () => {
  emit('update:isDialogVisible', false)

}
// 👉 Watch for changes in the role prop and update form fields
watch(() => props.role, (newrole) => {
  if (newrole) {
    id.value = newrole.id || 0
  }
});
const onSubmit = () => {
    emit('objectData', {
      id: props.role.id,
    })
    emit('update:isDialogVisible', false)
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
        Are you sure you want to delete this role ?
      </VCardText>

      <VCardText class="d-flex justify-end gap-3 flex-wrap">
        <VBtn
          color="secondary"
          variant="tonal"
          @click=" emit('update:isDialogVisible', false)"
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
