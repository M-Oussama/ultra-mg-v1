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
  end_date: '',
  real_start_date:'',
  real_end_date:'',
  position:'',
  position_ar:'',
})
watch(props.isDrawerOpen, ()=>{
  if(props.isDrawerOpen.open){
console.log(props.employee)
    attendance.value.id = props.employee.id || ""
    attendance.value.start_date = props.employee.start_date || ""
    attendance.value.end_date =  props.employee.end_date || ''
    attendance.value.real_start_date =  props.employee.real_start_date || ''
    attendance.value.real_end_date =  props.employee.real_end_date || ''
    attendance.value.position =  props.employee.position || ''
    attendance.value.position_ar =  props.employee.position_ar || ''
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
  attendanceStore.updateEndDate(convertDate(attendance.value.end_date), convertDate(attendance.value.start_date), attendance.value.id, attendance.value.position, convertDate(attendance.value.real_start_date), convertDate(attendance.value.real_end_date), attendance.value.position_ar).then(response => {
    props.isDrawerOpen.open = false;
    props.loading.isActive = false;

    props.employee.end_date = response.data.endDate;

    successMiddleware(response.data.msg)
    location.reload()
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
const convertDate = dateString =>{
  if(dateString != null && dateString !== "" ){
    console.log(dateString)
    // Parse the given date string
    let date = new Date(dateString);



    // Extract year, month, and day components
    let year = date.getFullYear();
    let month = String(date.getMonth() + 1).padStart(2, '0'); // Adding 1 because months are zero-based
    let day = String(date.getDate()).padStart(2, '0');

    // Format the date as "YYYY-MM-DD"
    return `${year}-${month}-${day}`;
  }
  return dateString;
}

const format = (date) => {
  const day = date.getDate();
  const month = date.getMonth() + 1;
  const year = date.getFullYear();

  return `${year}-${leadingZero(month,2)}-${leadingZero(day)}`;
}
function leadingZero (str) {
  return parseInt(str) < 10 ? "0"+str : str;
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
                <VLabel>
                  REAL START DATE
                </VLabel>
                <VueDatePicker v-model="attendance.real_start_date"  auto-apply  :format="format" @update:model-value="onStartDateChanged" />
              </VCol>
              <!-- 👉  name -->
              <VCol cols="12">
                <VLabel>
                  REAL END DATE
                </VLabel>
                <VueDatePicker v-model="attendance.real_end_date"  auto-apply  :format="format"  :min-date="attendance.end_date_restrict"/>

              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="attendance.position"
                  :rules="[requiredValidator]"
                  label="Position"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="attendance.position_ar"
                  :rules="[requiredValidator]"
                  label="Position Arabic"
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
