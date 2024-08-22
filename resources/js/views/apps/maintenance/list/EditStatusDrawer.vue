<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

import {
  emailValidator,
  requiredValidator,
} from '@validators'
import {useMaintenanceStore} from "@/views/apps/maintenance/useMaintenanceStore";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,

  },
  assets: {
    type: Array,
  },
  maintenance: {
    type: Object,
  },

})
const loading = ref({
  isActive: false
})
const maintenanceStore = useMaintenanceStore()

const asset_id = ref()
// 👉 Watch for changes in the asset prop and update form fields
watch(() => props.maintenance, (newmaintenance) => {
  if (newmaintenance) {
    console.log(newmaintenance)
    maintenance.value.id = newmaintenance.id || 0
    maintenance.value.name = newmaintenance.name || '';
    maintenance.value.asset_id = newmaintenance.component.asset_id || '';
    maintenance.value.component_id = newmaintenance.component_id || '';
    maintenance.value.maintenance_date = newmaintenance.maintenance_date || '';
    maintenance.value.next_maintenance_date = newmaintenance.next_maintenance_date || '';
    maintenance.value.notes = newmaintenance.notes || '';
    maintenance.value.status = newmaintenance.status || '';

  }
});

const emit = defineEmits([
  'update:isDrawerOpen',
  'objectData',
])
const components = ref([])

const isFormValid = ref(false)
const refForm = ref()
const id = ref()
const name = ref('')
const permissions = ref([])

const maintenance = ref({
  id :'',
  name:'',
  component_id:'',
  maintenance_date:'',
  next_maintenance_date:'',
  notes:'',
  status:'',
  })
let password = ref()
let visible = ref(false)


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
        emit('objectData', {
          maintenance: maintenance.value
        })
        emit('update:isDrawerOpen', false)
        nextTick(() => {
          refForm.value?.reset()
          refForm.value?.resetValidation()
        })
      }
    })


}

function isDateGreaterOrEqual(date1, date2) {
  // Extract the date portion as a 'YYYY-MM-DD' string
  if(date1 ==='' || date2 === '')
    return true
  date1 = new Date(date1)
  date2 = new Date(date2)
  const date1Str = date1.toISOString().split('T')[0];
  const date2Str = date2.toISOString().split('T')[0];


  return date1Str >= date2Str;
}

const onStartDateChanged = () =>{

  if(maintenance.value.maintenance_date === '' || (isDateGreaterOrEqual(maintenance.value.maintenance_date, maintenance.value.next_maintenance_date)  && maintenance.value.next_maintenance_date !== '')) {
    maintenance.value.next_maintenance_date = ''
  }


  maintenance.value.next_maintenance_date_restrict = new Date(new Date(maintenance.value.maintenance_date).getTime() + 24 * 60 * 60 *1000).toISOString().split('T')[0]
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
const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
const status = ref([
  'Started',
  'Active',
  'Done'
])
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"

  >
    <v-overlay
      :model-value="loading.isActive"
      class="align-center justify-center"
    >
      <v-progress-circular
        color="primary"
        indeterminate
        size="64"
      ></v-progress-circular>
    </v-overlay>
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Edit Component
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


                <VAutocomplete
                  clearable
                  v-model="maintenance.status"
                  :items="status"
                  item-value="id"
                  item-title="name"
                  label="Status"
                  placeholder="Status"
                  :rules="[requiredValidator]"
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
