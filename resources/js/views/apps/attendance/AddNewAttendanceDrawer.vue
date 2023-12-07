<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import {
  emailValidator,
  requiredValidator,
} from '@validators'

import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";

const props = defineProps({
  isDrawerOpen: {
    type: null,
    required: true,
  },
  attendances:{
    type: null,
    required:true
  },
  months:{
    type:null,
    required:true
  },
  years:{
    type:null,
    required:true
  }
})
const isFormValid = ref(false)
const refForm = ref()
const saleStore = useSaleStore()
const attendance = ref({
  month:'',
  year:'',
})
const data = ref();
const client = ref({
  id:-1,
})
const emit = defineEmits([
  'update:isDrawerOpen',
  'attendanceData',
])
// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}
const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {

    if (valid) {
      emit('attendanceData', {
        attendance
      })
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}
const handleDrawerModelValueUpdate = val => {


  emit('update:isDrawerOpen', val)
}

</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
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

                  <!-- 👉 Autocomplete -->
                  <VAutocomplete
                    v-model="attendance.month"
                    :items="props.months"
                    :rules="[requiredValidator]"
                    item-value="id"
                    item-title="name"
                    label="Month"
                  />


              </VCol>
              <!-- 👉  name -->
              <VCol cols="12">
                <VAutocomplete
                  v-model="attendance.year"
                  :items="props.years"
                  :rules="[requiredValidator]"
                  label="Year"
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

<style>

</style>
