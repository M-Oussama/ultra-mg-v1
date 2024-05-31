<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const store = useVacationStore()

import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {useVacationStore} from "@/views/apps/employee/vacation/useVacationStore";

const props = defineProps({
  isDrawerOpen: {
    type: null,
    required: true,
  },
  loading: {
    type: null,
    required: true,
  },
  vacation: {
    type: Object,
    required: true,
  },

})

const object = ref({
  start_date: '',
  end_date: '',
  end_date_restrict:new Date().getFullYear()+'-'+(new Date().getMonth() +1)+'-'+(new Date().getDate()+1),
  count:0,
})
watch(() => props.vacation, (newData) => {
  if(newData){
    object.value = {...newData};
  }


})

const route = useRoute()


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
  if(object.value.start_date === '' || object.value.start_date === null || object.value.end_date === '' || object.value.end_date === null  || object.value.count <= 0){
    if(object.value.start_date === '' || object.value.start_date === null)
      errorsMiddleware(
        "Warning",
        'start date must be selected'
      )


    if(object.value.count <= 0)
      errorsMiddleware(
        "Warning",
        'count days starts from 1 '
      )
  }else{
    props.loading.isActive = true;
    store.updateVacation(convertDate(object.value.start_date), convertDate(object.value.end_date), object.value.id, object.value.count).then(response => {
      props.isDrawerOpen.open = false;
      props.loading.isActive = false;

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
  if(object.value.start_date === null){
    object.value.end_date = null
  }

  if(object.value.start_date === '' ||(object.value.start_date >= object.value.end_date  && object.value.end_date !== '')) {
    object.value.end_date = ''
  }

  computeNewEndDate()


 object.value.end_date_restrict = new Date(new Date(object.value.start_date).getTime() + 24 * 60 * 60 * 2000).toISOString().split('T')[0]
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

const onCountDaysChanged = () => {
  computeNewEndDate()
}
const computeNewEndDate = () => {
  if(object.value.start_date) {
    // Create a Date object from the start date
    let start = new Date(object.value.start_date);

    // Add the number of days (converted to milliseconds)
    object.value.end_date = new Date(start.getTime() + object.value.count * 24 * 60 * 60 * 1000);
  }
  console.log(object.value.end_date)



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
                  Vacation Start Date
                </VLabel>
                <VueDatePicker v-model="object.start_date"  auto-apply  :format="format" @update:model-value="onStartDateChanged" />
              </VCol>
              <!-- 👉  name -->


              <VCol cols="12">

                <VTextField
                  v-model="object.count"
                  type="number"
                  label="Vacation Days"
                  @update:model-value="onCountDaysChanged"
                />
              </VCol>
              <!-- 👉  name -->
              <VCol cols="12">
                <VLabel>
                  Vacation End Date
                </VLabel>
                <VueDatePicker v-model="object.end_date"  auto-apply disabled="true" :format="format"  :min-date="object.end_date_restrict"/>

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
