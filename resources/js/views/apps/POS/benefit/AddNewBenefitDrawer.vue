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
  months:{
    type: null,
    required:true
  },
  years:{
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
const benefit = ref({
  month:null,
  year:null,

})
const data = ref();
const client = ref({
  id:-1,
})
const emit = defineEmits([
  'benefitData',
])
// 👉 drawer close
const closeNavigationDrawer = () => {
  props.isDrawerOpen.open = false;
}
const onSubmit = () => {
  if(benefit.value.month === null ||
    benefit.value.year === null ) {

    if(benefit.value.client.month === null){
      errorsMiddleware(
        "Month not selected",
        "Oops! Looks like you forgot to select a Month. Kindly pick at least one month to proceed"
      )
    }
    if(benefit.value.client.year === null){
      errorsMiddleware(
        "Month not selected",
        "Oops! Looks like you forgot to select a Year. Kindly pick at least one year to proceed"
      )
    }
  } else {

    handleDrawerModelValueUpdate()
      props.loading.isActive = true;
      saleStore.storeBenefit(benefit.value).then(response => {
       props.loading.isActive = false;
        benefit.value.month = 0;
        benefit.value.year = 0;
       successMiddleware('Benefit created Successfully')
        emit('benefitData', {
          data
        })
     }).catch(error => {
       console.log(error)
       props.loading.isActive = false;
       errorsMiddleware(
         "Error Occured",
         "Oops! Looks like the Benefit has not been submited "
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
        Add Benefit
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
                    v-model="benefit.month"
                    :items="props.months"

                    item-value="id"
                    item-title="name"
                    label="Month"
                  />
                </VCol>

                <VCol cols="12">
                  <VAutocomplete
                    v-model="benefit.year"
                    :items="props.years"

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
