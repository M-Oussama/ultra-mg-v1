<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const attendanceStore = useAttendanceStore()

import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {useAttendanceStore} from "@/views/apps/attendance/useAttendanceStore";

const props = defineProps({
  isDrawerOpen: {
    type: null,
    required: true,
  },
  loading: {
    type: null,
    required: true,
  },
  employee:{
    type: Object,
    required:true
  },
  disableEndDate:{
    type: Boolean,
    required:true
  },
  disableStartDate:{
    type: Boolean,
    required:true
  },

})
const attendance = ref({
  start_date: '',
  end_date: ''
})
watch(props.isDrawerOpen, ()=>{
  if(props.isDrawerOpen.open){
console.log(props.employee)
    attendance.value.id = props.employee.id || ""
    attendance.value.start_date = props.employee.start_date || ""
    attendance.value.end_date =  props.employee.end_date || ''
  }
})
const isFormValid = ref(false)
const refForm = ref()


const data = ref();
const client = ref({
  id:-1,
})

// 👉 drawer close
const closeNavigationDrawer = () => {
  props.isDrawerOpen.open = false
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  // update the end Date then close the drawer if any error keep the drawer open

  props.loading.isActive = true;
  console.log(props.loading)
  attendanceStore.updateEndDate(attendance.value.end_date, attendance.value.start_date, attendance.value.id).then(response => {
    props.isDrawerOpen.open = false;
    props.loading.isActive = false;

    props.employee.end_date = response.data.endDate;

    successMiddleware(response.data.msg)

  }).catch(error => {

    props.loading.isActive = false;
    errorsMiddleware(
      "Error Occured",
      error.response.data.message
    )
  })
}
const handleDrawerModelValueUpdate = val => {


  props.isDrawerOpen.value.open = val
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
        Update End Date
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

                <AppDateTimePicker
                  v-model="attendance.start_date"
                  label="Start Date"
                  :disabled="disableStartDate"
                />

              </VCol>
              <!-- 👉  name -->
              <VCol cols="12">
                <AppDateTimePicker
                  v-model="attendance.end_date"
                  label="End Date"
                  :config="{  minDate: `

                  ${attendance.start_date}` }"
                  :disabled="disableEndDate"
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
