<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

import {
  emailValidator,
  requiredValidator,
} from '@validators'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,

  },
  assets: {
    type: Array,
  },
  component: {
    type: Object,
  },

})
const asset_id = ref()
// 👉 Watch for changes in the asset prop and update form fields
watch(() => props.component, (newcomponent) => {
  if (newcomponent) {
    id.value = newcomponent.id || 0
    name.value = newcomponent.name || '';
    asset_id.value = newcomponent.asset_id || '';
  }
});

const emit = defineEmits([
  'update:isDrawerOpen',
  'objectData',
])

const isFormValid = ref(false)
const refForm = ref()
const id = ref()
const name = ref('')
const permissions = ref([])

const component = ref()
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
        id: id.value,
        name: name.value,
        asset_id: asset_id.value,
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

  >
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
              <!-- 👉 Full name -->
              <VCol cols="12">
                <VTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Name"
                />
              </VCol>

              <VCol cols="12">


                <VAutocomplete
                  clearable
                  v-model="asset_id"
                  :items="props.assets"
                  item-value="id"
                  item-title="name"
                  label="Asset"
                  placeholder="Asset"
                  :rules="[requiredValidator]"
                />

              </VCol>

              <!-- 👉 Submit and Cancel -->
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
