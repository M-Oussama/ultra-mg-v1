<script setup>
import { VIcon } from 'vuetify/components'
import sliderBar1 from '@images/illustrations/sidebar-pic-1.png'
import sliderBar2 from '@images/illustrations/sidebar-pic-2.png'
import sliderBar3 from '@images/illustrations/sidebar-pic-3.png'
import {useDashboardStore} from "@/views/dashboards/maintenance/useDashboardStore";

const websiteAnalytics = [
  {
    name: 'Traffic',
    slideImg: sliderBar1,
    data: [
      {
        number: '1.5k',
        text: 'Sessions',
      },
      {
        number: '3.1k',
        text: 'Page Views',
      },
      {
        number: '1.2k',
        text: 'Leads',
      },
      {
        number: '12%',
        text: 'Conversions',
      },
    ],
  },
  {
    name: 'Spending',
    slideImg: sliderBar2,
    data: [
      {
        number: '12h',
        text: 'Spend',
      },
      {
        number: '182',
        text: 'Order Size',
      },
      {
        number: '127',
        text: 'Order',
      },
      {
        number: '23k',
        text: 'Items',
      },
    ],
  },
  {
    name: 'Revenue Sources',
    slideImg: sliderBar3,
    data: [
      {
        number: '268',
        text: 'Direct',
      },
      {
        number: '890',
        text: 'Organic',
      },
      {
        number: '622',
        text: 'Referral',
      },
      {
        number: '1.2k',
        text: 'Campaign',
      },
    ],
  },
]

const props = defineProps({
  loading:{
    type: null,
    required: true,
  }
})
const daysLeft = (endDate) => {
  // Get the current date
  const currentDate = new Date();

  // Get the target date

  const target =  new Date(endDate);

  // Calculate the difference in time
  const differenceInTime = target.getTime() - currentDate.getTime();

  // Calculate the difference in days
  return Math.ceil(differenceInTime / (1000 * 3600 * 24));
}
const dashboard = useDashboardStore()
const maintenancesList = ref([])
const loading = ref({
  isActive :false
})
const getIncomingMaintenance = () => {
  loading.value.isActive = true;
  dashboard.getIncomingMaintenances({

  }).then(response => {
    maintenancesList.value = response.data.maintenances
    loading.value.isActive = false;
    console.log(response.data);
  }).catch(error => {
    loading.value.isActive = false;
  })
}
// 👉 Fetch Projects
watchEffect(() => {
  getIncomingMaintenance()
})


</script>

<template>
  <VCard color="primary">
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
    <VCarousel
      cycle
      :continuous="false"
      :show-arrows="false"
      hide-delimiter-background
      :delimiter-icon="() => h(VIcon, { icon: 'fa-circle', size: '10' })"
      height="auto"
      class="carousel-delimiter-top-end web-analytics-carousel"
    >
      <VCarouselItem
        v-for="item in maintenancesList"
        :key="item.name"
      >
        <VCardText>
          <VRow>
            <VCol cols="12">
              <h6 class="text-h6 text-white mb-1">
                {{item.name}}
              </h6>
              <p class="text-sm mb-0">
                {{ item.notes }}
              </p>
            </VCol>

            <VCol
              cols="12"
              sm="6"
              order="2"
              order-sm="1"
            >
              <VRow>


                <VCol

                  cols="6"
                  class="text-no-wrap"
                >
                  <VChip
                    label
                    class="me-2"
                  >
                    #
                  </VChip>
                  <span>{{ item.component.asset.name }}</span>
                </VCol>
                <VCol

                  cols="6"
                  class="text-no-wrap"
                >
                  <VChip
                    label
                    class="me-2"
                  >
                  #
                  </VChip>
                  <span>{{ item.component.name }}</span>
                </VCol>

                <VCol

                  cols="6"
                  class="text-no-wrap"
                >
                  <VChip
                    label
                    class="me-2"
                  >
                     D
                  </VChip>
                  <span>{{ item.next_maintenance_date }}</span>
                </VCol>

                <VCol

                  cols="6"
                  class="text-no-wrap"
                >
                  <VChip
                    label
                    class="me-2"
                  >
                    D
                  </VChip>
                  <span>{{ daysLeft(item.next_maintenance_date) }} Days</span>
                </VCol>
              </VRow>
            </VCol>

            <VCol
              cols="12"
              sm="6"
              order="1"
              order-sm="2"
              class="position-relative text-center"
            >
              <img
                :src="item.slideImg"
                class="card-website-analytics-img"
              >
            </VCol>
          </VRow>
        </VCardText>
      </VCarouselItem>
    </VCarousel>
  </VCard>
</template>

<style lang="scss">
.card-website-analytics-img {
  block-size: 160px;
}

@media screen and (min-width: 600px) {
  .card-website-analytics-img {
    position: absolute;
    margin: auto;
    inset-block-end: 40px;
    inset-block-start: -1rem;
    inset-inline-end: 1rem;
  }
}

.web-analytics-carousel {
  .v-carousel__controls {
    .v-btn:not(.v-btn--active) {
      opacity: 0.4;
    }
  }
}
</style>

