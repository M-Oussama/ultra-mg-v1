<script setup>
import { useProjectStore } from '@/views/dashboards/analytics/useProjectStore'
import { avatarText } from '@core/utils/formatters'
import {useDashboardStore} from "@/views/dashboards/analytics/useDashboardStore";

const projectStore = useProjectStore()
const searchQuery = ref('')

const totalPage = ref(1)
const totalProjects = ref(0)
const projects = ref([])
const selectedRows = ref([])
const selectAllProject = ref(false)

const dashboard = useDashboardStore()

const rowPerPage = ref(5)
const currentPage = ref(1)
const vacationsList = ref([])
const getEmployeeInVacation = () => {
  //loading2.value.isActive = true;
  dashboard.getIncomingVacations({
    perPage: rowPerPage.value,
    currentPage: currentPage.value,
  }).then(response => {
    vacationsList.value = response.data.vacations.data
    // loading2.value.isActive = false;
    console.log(response.data);
  }).catch(error => {
    // loading2.value.isActive = false;
  })
}
// 👉 Fetch Projects
const props = defineProps({
  vacation: {
    type: Array,
    required: true,
  },
})
watch(() => props.vacation, (newVacation) => {
  if (newVacation) {
    vacationsList.value = newVacation

  }
});



// 👉 Fetch Projects
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = vacationsList.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = vacationsList.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalProjects.value } entries`
})

// 👉 Add/Remove all checkbox ids in/from array
const selectUnselectAll = () => {
  selectAllProject.value = !selectAllProject.value
  if (selectAllProject.value) {
    projects.value.forEach(project => {
      if (!selectedRows.value.includes(`check${ project.status }`))
        selectedRows.value.push(`check${ project.status }`)
    })
  } else {
    selectedRows.value = []
  }
}

// 👉 watch if checkbox array is empty all checkbox should be uncheck
watch(selectedRows, () => {
  if (!selectedRows.value.length)
    selectAllProject.value = false
}, { deep: true })

const addRemoveIndividualCheckbox = checkID => {
  if (selectedRows.value.includes(checkID)) {
    const index = selectedRows.value.indexOf(checkID)

    selectedRows.value.splice(index, 1)
  } else {
    selectedRows.value.push(checkID)
    selectAllProject.value = true
  }
}

const daysLeft = (endDate) => {
  // Get the current date
  const currentDate = new Date();

  // Get the target date

  const target =  new Date(addDay(endDate,365));

  // Calculate the difference in time
  const differenceInTime = target.getTime() - currentDate.getTime();

  // Calculate the difference in days
  return Math.ceil(differenceInTime / (1000 * 3600 * 24));
}

const addDay = (targetDate, number) => {
  // Create a new Date object from the given date
  const newDate = new Date(targetDate);

  // Add one day
  newDate.setDate(newDate.getDate() + number);

 // console.log(newDate);
  return formatDate(newDate);
}
function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
  const day = String(date.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}
</script>

<template>
  <VCard v-if="projects">
    <VCardItem class="project-header d-flex flex-wrap justify-space-between py-4 gap-4">
      <VCardTitle>Incoming Vacations</VCardTitle>
      <div
        class="me-3"
        style="width: 80px;"
      >
        <VSelect
          v-model="rowPerPage"
          density="compact"
          variant="outlined"
          :items="[5,10, 20, 30, 50]"
        />
      </div>
      <template #append>
        <div
          class="d-flex align-center gap-2"
          style="width: 272px;"
        >
          <VLabel>Search:</VLabel>
          <VTextField
            v-model="searchQuery"
            placeholder="Search"
          />
        </div>
      </template>
    </VCardItem>

    <VDivider />

    <!-- SECTION Table -->
    <VTable class="text-no-wrap">
      <!-- 👉 Table head -->
      <thead>
        <tr>
          <!-- 👉 Check/Uncheck all checkbox -->
          <th
            scope="col"
            class="text-center"
          >
            <div style="width: 1rem;">
<!--              <VCheckbox-->
<!--                :model-value="selectAllProject"-->
<!--                :indeterminate="(projects.length !== selectedRows.length) && !!selectedRows.length"-->
<!--                @click="selectUnselectAll"-->
<!--              />-->
              ID
            </div>
          </th>

          <th
            scope="col"
            class="font-weight-semibold"
          >
            NAME
          </th>
          <th
            scope="col"
            class="font-weight-semibold"
          >
            START DATE
          </th>

          <th
            scope="col"
            class="font-weight-semibold"
          >
            REST
          </th>
          <th
            scope="col"
            class="font-weight-semibold"
          >
            <span class="ms-2">ACTIONS</span>
          </th>
        </tr>
      </thead>

      <!-- 👉 Table Body -->
      <tbody>
        <tr
          v-for="vacation in vacationsList"
          :key="vacation.id"
          style="height: 3.5rem;"
        >
          <!-- 👉 Individual checkbox -->
          <td>
            <div style="width: 1rem;">
<!--              <VCheckbox-->
<!--                :id="`check${vacation.id}`"-->
<!--                :model-value="selectedRows.includes(`check${project.id}`)"-->
<!--                @click="addRemoveIndividualCheckbox(`check${project.id}`)"-->
<!--              />-->
               {{vacation.id}}
            </div>
          </td>

          <!-- 👉 Name -->
          <td>
            <div class="d-flex align-center gap-3">
              <VAvatar
                variant="tonal"
                color="primary"
                size="38"
              >
<!--                <VImg-->
<!--                  v-if="vacation.logo.length"-->
<!--                  :src="vacation.logo"-->
<!--                />-->
<!--                <span-->
<!--                  v-else-->
<!--                  class="font-weight-semibold"-->
<!--                >{{ avatarText(vacation.count) }}</span>-->
              </VAvatar>

              <div>
                <h6 class="text-sm-subtitle-2 text-medium-emphasis font-weight-semibold">
                  {{ vacation.employee.name }} {{vacation.employee.surname}}
                </h6>
                <span class="text-disabled">{{ vacation.end_date }}</span>
              </div>
            </div>
          </td>

          <!-- 👉 Leader -->
          <td class="text-medium-emphasis">
            {{ addDay(vacation.end_date, 365) }}
          </td>



          <!-- 👉 Progress -->
          <td class="text-center">
            {{ daysLeft(vacation.end_date) }} DAYS
          </td>

          <!-- 👉 Actions -->
          <td
            class="text-center"
            style="width: 7.5rem;"
          >
            <VBtn
              icon
              variant="plain"
              color="default"
              size="x-small"
            >
              <VIcon
                :size="22"
                icon="tabler-user-check"
              />

              <VMenu activator="parent">
                <VList density="compact">
                  <VListItem
                    href="#"
                    title="Details"
                  />
                  <VListItem
                    href="#"
                    title="Archive"
                  />
                </VList>
              </VMenu>
            </VBtn>
          </td>
        </tr>
      </tbody>

      <!-- 👉 table footer  -->
      <tfoot v-show="!vacationsList.length">
        <tr>
          <td
            colspan="8"
            class="text-center text-body-1"
          >
            No data available
          </td>
        </tr>
      </tfoot>
    </VTable>
    <!-- !SECTION -->

    <VDivider />

    <!-- SECTION Pagination -->
    <VCardText class="d-flex align-center flex-wrap justify-space-between gap-4 py-3">
      <!-- 👉 Pagination meta -->
      <span class="text-sm text-disabled">{{ paginationData }}</span>

      <!-- 👉 Pagination -->
      <VPagination
        v-model="currentPage"
        size="small"
        :total-visible="2"
        :length="totalPage"
      />
    </VCardText>
  <!-- !SECTION -->
  </VCard>
</template>

<style lang="scss">
.project-header .v-card-item__append {
  padding-inline-start: 0;
}
</style>
