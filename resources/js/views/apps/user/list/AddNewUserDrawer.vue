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
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'userData',
])

const isFormValid = ref(false)
const refForm = ref()
const name = ref('')
const email = ref('')
const company = ref('')
const country = ref('')
const contact = ref('')
const role = ref()
let password = ref()
let visible = ref(false)
let confirm_visible = ref(false)
let confirm_password = ref()
const plan = ref()
const status = ref()

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
      emit('userData', {
        name: name.value,
        email: email.value,
        password: password.value,
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
        Add User
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

              <!-- 👉 Email -->
              <VCol cols="12">
                <VTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <VTextField
                  hide-details="auto"
                  :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                  :type="visible ? 'text' : 'password'"
                  density="compact"
                  prepend-inner-icon="mdi-lock-outline"
                  variant="outlined"
                  @click:append-inner="visible = !visible"
                  v-model="password"
                  :rules="[requiredValidator]"
                  label="password"
                />
              </VCol> 
              
              <!-- 👉 Confirm Password -->
              <VCol cols="12">
                <VTextField
                  hide-details="auto"
                  :append-inner-icon="confirm_visible ? 'mdi-eye-off' : 'mdi-eye'"
                  :type="confirm_visible ? 'text' : 'password'"
                  density="compact"
                  prepend-inner-icon="mdi-lock-outline"
                  variant="outlined"
                  @click:append-inner="confirm_visible = !confirm_visible"
                  v-model="confirm_password"
                  :rules="[requiredValidator, v => !!v || 'Confirm password is required', v => v === password || 'Passwords do not match']"
                  label="Confirm Password"

                />
              </VCol>

<!--              &lt;!&ndash; 👉 company &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="company"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="Company"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 Country &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="country"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="Address"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 Contact &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="contact"-->
<!--                  type="number"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="Phone number"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 Plan &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VSelect-->
<!--                  v-model="plan"-->
<!--                  label="Select Plan"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  :items="['Basic', 'Company']"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 N°RC &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="country"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="N°RC"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 N°IS &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="country"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="N°IS"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 N°IF &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="country"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="N°IF"-->
<!--                />-->
<!--              </VCol>-->

<!--              &lt;!&ndash; 👉 N°ART &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  v-model="country"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="N°ART"-->
<!--                />-->
<!--              </VCol>-->


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
