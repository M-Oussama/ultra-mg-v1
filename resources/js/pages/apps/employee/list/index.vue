<script setup>
import AddNewEmployeeDrawer from '@/views/apps/employee/list/AddNewEmployeeDrawer.vue'
import EditEmployeeDrawer from '@/views/apps/employee/list/EditEmployeeDrawer.vue'
import ConfirmationDialog from '@/views/apps/employee/list/ConfirmationDialog.vue'
import { useEmployeeStore } from '@/views/apps/employee/useEmployeeStore'
import { successMiddleware } from '@/middlewares/successMiddleware'
import { errorsMiddleware } from '@/middlewares/errorsMiddleware'
import autoTable from 'jspdf-autotable'
import { jsPDF } from 'jspdf'

const route = useRouter()
const employeeStore = useEmployeeStore()
const searchQuery = ref('')
const loading = ref(false)
const cities = ref([])

const loading2 = ref({
  isActive: true,
})

const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()

const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalEmployees = ref(0)

const employees = ref([])
const selectedEmployeeIds = ref([])
const isExportDialogVisible = ref(false)

const exportFieldOptions = [
  { key: 'name', label: 'Name' },
  { key: 'surname', label: 'Surname' },
  { key: 'name_ar', label: 'Name Arabic' },
  { key: 'surname_ar', label: 'Surname Arabic' },
  { key: 'father_name_ar', label: 'Father Name Arabic' },
  { key: 'mother_full_name_ar', label: 'Mother Full Name Arabic' },
  { key: 'birthdate', label: 'Birthdate' },
  { key: 'birthplace', label: 'Birthplace' },
  { key: 'birthCity', label: 'Birth City' },
  { key: 'email', label: 'Email' },
  { key: 'address', label: 'Address' },
  { key: 'phone', label: 'Phone' },
  { key: 'NIN', label: 'NIN' },
  { key: 'NCN', label: 'NCN' },
  { key: 'CNAS', label: 'CNAS' },
  { key: 'card_issue_date', label: 'Card Issue Date' },
  { key: 'card_issue_place', label: 'Card Issue Place' },
  { key: 'cardIssuedCity', label: 'Card Issued City' },
  { key: 'active', label: 'Status' },
]

const selectedExportFields = ref([
  'name',
  'surname',
  'father_name_ar',
  'NIN',
  'phone',
])

const fetchEmployees = () => {
  loading2.value.isActive = true

  employeeStore.fetchEmployees({
    searchValue: searchQuery.value,
    perPage: rowPerPage.value,
    currentPage: currentPage.value,
  }).then(response => {
    employees.value = response.data.employees.data
    selectedEmployeeIds.value = selectedEmployeeIds.value.filter(id =>
      employees.value.some(employee => employee.id === id),
    )
    cities.value = response.data.cities
    totalPage.value = response.data.totalPage
    totalEmployees.value = response.data.totalEmployees
    loading.value = false
    loading2.value.isActive = false
  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false
  })
}

watchEffect(fetchEmployees)

watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

watch(searchQuery, () => {
  fetchEmployees()
})

const isAddNewEmployeeDrawerVisible = ref(false)
const isEditEmployeeDrawerVisible = ref(false)
const isDialogVisible = ref(false)
const selectedEmployee = ref()

watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

const paginationData = computed(() => {
  const firstIndex = employees.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = employees.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Showing ${firstIndex} to ${lastIndex} of ${totalEmployees.value} entries`
})

const selectedEmployees = computed(() =>
  employees.value.filter(employee => selectedEmployeeIds.value.includes(employee.id)),
)

const isAllCurrentPageSelected = computed({
  get: () => employees.value.length > 0 && employees.value.every(employee => selectedEmployeeIds.value.includes(employee.id)),
  set: value => {
    selectedEmployeeIds.value = value
      ? employees.value.map(employee => employee.id)
      : []
  },
})

const exportSummary = computed(() => {
  const employeeCount = selectedEmployees.value.length
  const fieldCount = selectedExportFields.value.length

  return `${employeeCount} employee${employeeCount === 1 ? '' : 's'} selected, ${fieldCount} field${fieldCount === 1 ? '' : 's'} selected`
})

const formatEmployeeFieldValue = (employee, fieldKey) => {
  const specialValueResolvers = {
    birthCity: currentEmployee => currentEmployee.birth_city?.name || currentEmployee.birthCity?.name || '',
    cardIssuedCity: currentEmployee => currentEmployee.card_issued_city?.name || currentEmployee.cardIssuedCity?.name || '',
    active: currentEmployee => currentEmployee.active ? 'Active' : 'Inactive',
  }

  const value = specialValueResolvers[fieldKey]
    ? specialValueResolvers[fieldKey](employee)
    : employee[fieldKey]

  return value ?? ''
}

const openExportDialog = () => {
  if (!selectedEmployees.value.length) {
    errorsMiddleware('Select at least one employee to export')

    return
  }

  isExportDialogVisible.value = true
}

const exportSelectedEmployeesToPdf = () => {
  if (!selectedEmployees.value.length) {
    errorsMiddleware('Select at least one employee to export')

    return
  }

  if (!selectedExportFields.value.length) {
    errorsMiddleware('Select at least one field to include in the PDF')

    return
  }

  const doc = new jsPDF({
    orientation: selectedExportFields.value.length > 6 ? 'landscape' : 'portrait',
    unit: 'mm',
    format: 'a4',
  })

  const fieldLabels = selectedExportFields.value.map(fieldKey => {
    const field = exportFieldOptions.find(option => option.key === fieldKey)

    return field?.label || fieldKey
  })

  const tableBody = selectedEmployees.value.map((employee, index) => [
    index + 1,
    ...selectedExportFields.value.map(fieldKey => formatEmployeeFieldValue(employee, fieldKey)),
  ])

  const pageWidth = doc.internal.pageSize.getWidth()
  const pageHeight = doc.internal.pageSize.getHeight()
  const generatedAt = new Date().toLocaleString()
  const titleY = 18
  const metaStartY = 28

  doc.setDrawColor(210, 214, 220)
  doc.setLineWidth(0.5)
  doc.line(14, titleY + 3, pageWidth - 14, titleY + 3)

  doc.setTextColor(32, 33, 36)
  doc.setFontSize(17)
  doc.text('Employee Export', 14, titleY)
  doc.setFontSize(9)
  doc.setTextColor(110, 114, 122)
  doc.text(`Generated ${generatedAt}`, 14, metaStartY)
  doc.text(`Employees ${selectedEmployees.value.length}`, 14, metaStartY + 6)
  doc.text(`Fields ${fieldLabels.join(', ')}`, 14, metaStartY + 12, { maxWidth: pageWidth - 28 })

  autoTable(doc, {
    startY: 48,
    head: [['#', ...fieldLabels]],
    body: tableBody,
    styles: {
      fontSize: 8.5,
      cellPadding: {
        top: 3.5,
        right: 3,
        bottom: 3.5,
        left: 3,
      },
      overflow: 'linebreak',
      valign: 'middle',
      lineColor: [226, 229, 234],
      lineWidth: 0.2,
      textColor: [45, 48, 53],
    },
    headStyles: {
      fillColor: [245, 246, 248],
      textColor: [55, 58, 64],
      fontStyle: 'bold',
      lineColor: [220, 223, 228],
      lineWidth: 0.25,
    },
    alternateRowStyles: {
      fillColor: [250, 250, 251],
    },
    bodyStyles: {
      fillColor: [255, 255, 255],
    },
    margin: {
      top: 48,
      left: 10,
      right: 10,
      bottom: 18,
    },
    didDrawPage: data => {
      doc.setFontSize(9)
      doc.setTextColor(130, 134, 140)
      doc.line(14, pageHeight - 13, pageWidth - 14, pageHeight - 13)
      doc.text(`Page ${data.pageNumber}`, pageWidth - 14, pageHeight - 8, { align: 'right' })
    },
  })

  const date = new Date().toISOString().slice(0, 10)

  doc.save(`employees-export-${date}.pdf`)
  isExportDialogVisible.value = false
  successMiddleware('Employee PDF exported successfully')
}

const addNewEmployee = employeeData => {
  loading2.value.isActive = true

  employeeStore.addEmployee(employeeData).then(response => {
    successMiddleware(response.data.message)
    loading2.value.isActive = false
    fetchEmployees()
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false
  })
}

const updateEmployee = employeeData => {
  loading2.value.isActive = true

  employeeStore.updateEmployee(employeeData).then(response => {
    successMiddleware(response.data.message)
    loading2.value.isActive = false
    fetchEmployees()
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false
  })
}

const deleteEmployee = employeeData => {
  loading2.value.isActive = true

  employeeStore.deleteEmployee(employeeData).then(response => {
    loading2.value.isActive = false
    successMiddleware(response.data.message)
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false
  })

  fetchEmployees()
}

const openUpdateDrawer = employee => {
  isEditEmployeeDrawerVisible.value = true
  selectedEmployee.value = employee
}

const openConfirmationDialog = employee => {
  isDialogVisible.value = true
  selectedEmployee.value = employee
}

const navigateToEmployeeContract = employee => {
  route.push({
    name: 'apps-employee-contract-id',
    params: { id: employee.id },
  })
}
</script>

<template>
  <section>
    <VRow>
      <VOverlay
        :model-value="loading2.isActive"
        class="align-center justify-center"
      >
        <VProgressCircular
          color="primary"
          indeterminate
          size="64"
        />
      </VOverlay>

      <VCol cols="12">
        <VCard title="Employees">
          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <div
              class="me-3"
              style="width: 80px;"
            >
              <VSelect
                v-model="rowPerPage"
                density="compact"
                variant="outlined"
                :items="[10, 20, 30, 50]"
              />
            </div>

            <VSpacer />

            <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
              <div style="width: 9rem;">
                <VProgressCircular
                  v-if="loading"
                  indeterminate
                  color="primary"
                />
                <VTextField
                  v-else
                  ref="searchField"
                  v-model="searchQuery"
                  placeholder="Search"
                  density="compact"
                  @input="isTyping = true"
                />
              </div>

              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
                :disabled="!selectedEmployeeIds.length"
                @click="openExportDialog"
              >
                Export PDF
              </VBtn>

              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewEmployeeDrawerVisible = true"
              >
                New Employee
              </VBtn>
            </div>
          </VCardText>

          <VDivider />

          <VTable class="text-no-wrap">
            <thead>
              <tr>
                <th
                  scope="col"
                  style="width: 48px;"
                >
                  <VCheckbox
                    v-model="isAllCurrentPageSelected"
                    density="compact"
                    hide-details
                  />
                </th>
                <th scope="col">
                  ID
                </th>
                <th scope="col">
                  Employee
                </th>
                <th scope="col">
                  Email
                </th>
                <th
                  scope="col"
                  class="text-center"
                >
                  Actions
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="employee in employees"
                :key="employee.id"
                style="height: 3.75rem;"
              >
                <td>
                  <VCheckbox
                    v-model="selectedEmployeeIds"
                    :value="employee.id"
                    density="compact"
                    hide-details
                  />
                </td>

                <td>
                  {{ employee.id }}
                </td>

                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="employee.avatar"
                        :src="employee.avatar"
                      />
                      <span v-else>{{ employee.name.toUpperCase().charAt(0) }}</span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: employee.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ employee.name }} {{ employee.surname }}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">@{{ employee.email }}</span>
                    </div>
                  </div>
                </td>

                <td>
                  <span class="text-base font-weight-semibold">{{ employee.email }}</span>
                </td>

                <td
                  class="text-center"
                  style="width: 10rem;"
                >
                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openUpdateDrawer(employee)"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-edit"
                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    :to="{ name: 'apps-attendance-employees-id', params: { id: employee.id } }"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-viewfinder"
                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="navigateToEmployeeContract(employee)"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-file"
                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openConfirmationDialog(employee)"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-trash"
                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-dots-vertical"
                    />
                  </VBtn>
                </td>
              </tr>
            </tbody>

            <tfoot v-show="!employees.length">
              <tr>
                <td
                  colspan="5"
                  class="text-center"
                >
                  No data available
                </td>
              </tr>
            </tfoot>
          </VTable>

          <VDivider />

          <VCardText class="d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5">
            <span class="text-sm text-disabled">
              {{ paginationData }}
            </span>

            <VPagination
              v-model="currentPage"
              size="small"
              :total-visible="5"
              :length="totalPage"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <AddNewEmployeeDrawer
      v-model:isDrawerOpen="isAddNewEmployeeDrawerVisible"
      :cities="cities"
      @employee-data="addNewEmployee"
    />

    <EditEmployeeDrawer
      v-model:isDrawerOpen="isEditEmployeeDrawerVisible"
      v-model:employee="selectedEmployee"
      v-model:cities="cities"
      @employee-data="updateEmployee"
    />

    <ConfirmationDialog
      v-if="isDialogVisible"
      v-model:isDialogVisible="isDialogVisible"
      v-model:employee="selectedEmployee"
      @employee-data="deleteEmployee"
    />

    <VDialog
      v-model="isExportDialogVisible"
      max-width="720"
    >
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between flex-wrap gap-2">
          <span>Export Employees PDF</span>
          <VChip
            color="primary"
            variant="tonal"
          >
            {{ exportSummary }}
          </VChip>
        </VCardTitle>

        <VDivider />

        <VCardText class="pt-4">
          <p class="text-body-2 mb-4">
            Choose the employee fields you want to include in the PDF.
          </p>

          <VRow>
            <VCol
              v-for="field in exportFieldOptions"
              :key="field.key"
              cols="12"
              sm="6"
              md="4"
            >
              <VCheckbox
                v-model="selectedExportFields"
                :label="field.label"
                :value="field.key"
                density="compact"
                hide-details
              />
            </VCol>
          </VRow>

          <VDivider class="my-4" />

          <div class="text-body-2 selected-employees-preview">
            Selected employees:
            {{ selectedEmployees.map(employee => `${employee.name} ${employee.surname}`.trim()).join(', ') }}
          </div>
        </VCardText>

        <VCardActions class="px-6 pb-6">
          <VSpacer />
          <VBtn
            variant="tonal"
            color="secondary"
            @click="isExportDialogVisible = false"
          >
            Cancel
          </VBtn>
          <VBtn @click="exportSelectedEmployeesToPdf">
            Export PDF
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}

.selected-employees-preview {
  line-height: 1.6;
}
</style>

<route lang="yaml">
meta:
  action: list
  subject: employees
</route>
