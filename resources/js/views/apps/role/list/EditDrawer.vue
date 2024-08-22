<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import ListDualBox from '@/views/apps/role/update-list-dual-box.vue'

import {
  emailValidator,
  requiredValidator,
} from '@validators'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,

  },
  role: {
    type: Object,
  },
  permissionLeft: {
    type: Object,
  },
  permissionRight: {
    type: Object,
  },
})

// 👉 Watch for changes in the role prop and update form fields
watch(() => props.role, (newrole) => {
  if (newrole) {
    id.value = newrole.id || 0
    role.value = newrole.role || '';
    permissions.value = newrole.permissions || '';

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

const role = ref()
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
        role: role.value,
        permissions: props.permissionRight,

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
        Edit role
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
                  v-model="role"
                  :rules="[requiredValidator]"
                  label="Role"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">

                <list-dual-box
                  :left="props.permissionLeft"
                  :right="props.permissionRight"
                ></list-dual-box>
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
