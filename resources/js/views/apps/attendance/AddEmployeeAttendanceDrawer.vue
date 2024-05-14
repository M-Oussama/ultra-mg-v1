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
  employees:{
    type: Object,
    required:true
  },
})
const route = useRoute()
const attendance = ref({
  start_date: new Date().getFullYear()+'-'+(new Date().getMonth() +1)+'-'+new Date().getDate(),
  end_date: '',
  end_date_restrict:new Date().getFullYear()+'-'+(new Date().getMonth() +1)+'-'+(new Date().getDate()+1),
  position:'',
  real_start_date:'',
  real_end_date:'',
  national_card:'',
  birth_certificate:''
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
const  getBase64  =(file) =>{

}
 const onSubmit = () => {
  // update the end Date then close the drawer if any error keep the drawer open
   var reader = new FileReader();
   reader.readAsDataURL(attendance.value.birth_certificate[0]);
   reader.onload = function () {
     let base64BC =  reader.result;
     var reader2 = new FileReader();
     reader2.readAsDataURL(attendance.value.national_card[0]);
     reader2.onload = function () {
       let base64NC =  reader.result;
       callSubmit(base64BC,base64NC)
     }


   };
   reader.onerror = function (error) {
     return '';
   };




}
const callSubmit = (base64BC,base64NC) => {
  if(attendance.value.start_date === ''){
    errorsMiddleware(
      "Warning",
      'start date must be selected'
    )
  }else{
    props.loading.isActive = true;
    console.log(props.loading)
    attendanceStore.addEmployeeAttendanceRecord(attendance.value.end_date, attendance.value.start_date, Number(route.params.id),attendance.value.position,base64BC, base64NC).then(response => {
      props.isDrawerOpen.open = false;
      props.loading.isActive = false;

      successMiddleware(response.data.msg)
      //location.reload()



    }).catch(error => {

      props.loading.isActive = false;
      errorsMiddleware(
        "Error Occured",
        error.response.data.message
      )
    })
  }

}
const handleDrawerModelValueUpdate = val => {


  props.isDrawerOpen.value.open = val
}
function leadingZero (str) {
  return parseInt(str) < 10 ? "0"+str : str;
}

const format = (date) => {
  const day = date.getDate();
  const month = date.getMonth() + 1;
  const year = date.getFullYear();

  return `${year}-${leadingZero(month,2)}-${leadingZero(day)}`;
}

const onStartDateChanged = () =>{

  if(attendance.value.start_date === '' ||(attendance.value.start_date >= attendance.value.end_date  && attendance.value.end_date !== '')) {
    attendance.value.end_date = ''
  }


 attendance.value.end_date_restrict = new Date(new Date(attendance.value.start_date).getTime() + 24 * 60 * 60 * 2000).toISOString().split('T')[0]
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
        Add New Record
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
                <VLabel>
                  CNAS START DATE
                </VLabel>
                <VueDatePicker v-model="attendance.start_date"  auto-apply  :format="format" @update:model-value="onStartDateChanged" />
              </VCol>
              <!-- 👉  name -->
              <VCol cols="12">
                <VLabel>
                  CNAS END DATE
                </VLabel>
                <VueDatePicker v-model="attendance.end_date"  auto-apply  :format="format"  :min-date="attendance.end_date_restrict"/>

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
              <VCol
                cols="12"
              >
                <VFileInput
                  v-model="attendance.national_card"
                  label="National Card"
                  variant="filled"
                />
              </VCol>
              <VCol
                cols="12"
              >
                <VFileInput
                  v-model="attendance.birth_certificate"
                  label="Birth Certificate"
                  variant="filled"
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
.dp--menu-wrapper {
  position: static;
}
</style>
