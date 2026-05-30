<script setup>
import PERMISSIONS from '@/router/permissions'
import { useEmployeeImportsStore } from '@/views/apps/employee/useEmployeeImportsStore'

const store = useEmployeeImportsStore()
const selectedFile = ref(null)
const uploading = ref(false)
const result = ref(null)
const apiError = ref('')

const onFileChange = files => {
  selectedFile.value = Array.isArray(files) ? (files[0] || null) : (files || null)
  result.value = null
  apiError.value = ''
}

const handleImport = async () => {
  if (!selectedFile.value) {
    apiError.value = 'Please select a CSV file first.'
    return
  }
  uploading.value = true
  apiError.value = ''
  result.value = null
  try {
    const response = await store.importEmployeeCareersCsv(selectedFile.value)
    result.value = response.data
  } catch (error) {
    apiError.value = error?.response?.data?.message || 'Import failed.'
  } finally {
    uploading.value = false
  }
}
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12" md="9" lg="8">
        <VCard title="Import Employee Careers CSV">
          <VCardText class="pt-4">
            <p class="mb-4 text-body-2">
              Required: id, employee_id, start_date, end_date, real_start_date, real_end_date, position, position_ar.
            </p>
            <VFileInput accept=".csv,text/csv" label="CSV File" prepend-icon="tabler-file-upload" @update:model-value="onFileChange" />
            <div class="d-flex mt-4">
              <VBtn :loading="uploading" :disabled="uploading || !$can(PERMISSIONS.EMPLOYEE.ADD, PERMISSIONS.EMPLOYEE.SUBJECT)" @click="handleImport">Upload and Import</VBtn>
            </div>
            <VAlert v-if="apiError" type="error" variant="tonal" class="mt-4">{{ apiError }}</VAlert>
            <VAlert v-if="result" type="success" variant="tonal" class="mt-4">Inserted: {{ result.inserted }} | Failed: {{ result.failed }}</VAlert>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </section>
</template>

<route lang="yaml">
meta:
  action: add
  subject: employees
</route>

