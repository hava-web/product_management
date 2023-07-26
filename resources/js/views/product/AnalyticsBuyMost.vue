<script setup>
import { hexToRgb } from '@layouts/utils'
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import axiosIns from '@/plugins/axios'

const vuetifyTheme = useTheme()
const dataCus = ref([])
const labelCus = ref([])
const isActive = ref(false)

const date = reactive({
  from: null,
  to: null,
})

const error = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('api/buy_most', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    dataCus.value.push(...res.data)
    console.log(dataCus.value)
  }).catch(err=>{
    console.log(err.data)
  })
})

const confirm = () => {
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.post('api/buy_most_interval', date, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    dataCus.value = []
    dataCus.value.push(...res.data)
    isActive.value = false
    console.log(dataCus.value)
  }).catch(err=>{
    console.log(err.data)
    error.status = true
    error.title = 'You have some errors'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
}


const options = controlledComputed(() => vuetifyTheme.name.value, () => {
  const currentTheme = ref(vuetifyTheme.current.value.colors)
  const variableTheme = ref(vuetifyTheme.current.value.variables)
  const disabledColor = `rgba(${ hexToRgb(currentTheme.value['on-surface']) },${ variableTheme.value['disabled-opacity'] })`
  const borderColor = `rgba(${ hexToRgb(String(variableTheme.value['border-color'])) },${ variableTheme.value['border-opacity'] })`
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    plotOptions: {
      bar: {
        borderRadius: 9,
        distributed: true,
        columnWidth: '30%',
        endingShape: 'rounded',
        startingShape: 'rounded',
      },
    },
    stroke: {
      width: 2,
      colors: [currentTheme.value.surface],
    },
    legend: { show: false },
    grid: {
      borderColor,
      strokeDashArray: 7,
      padding: {
        top: -1,
        right: 0,
        left: -12,
        bottom: 5,
      },
    },
    dataLabels: { enabled: false },
    colors: [
      currentTheme.value.primary,
    ],
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    xaxis: {
      tickPlacement: 'on',
      labels: { show: true },
      crosshairs: { opacity: 0 },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    
    tooltip: {
      enabled: true,
      enabledOnSeries: undefined,
      shared: true,
      followCursor: false,
      intersect: false,
      inverseOrder: false,
      custom: undefined,
      fillSeriesColor: false,
      theme: false,
      style: {
        fontSize: '12px',
        fontFamily: undefined,
      },
      onDatasetHover: {
        highlightDataSeries: false,
      },
      x: {
        show: true,
        formatter: undefined,
      },
      y: {
        formatter: undefined,
        title: {
          formatter: () => 'Number of purchases',
        },
      },
      z: {
        formatter: undefined,
        title: 'Size: ',
      },
      marker: {
        show: true,
      },
      items: {
       
      },
      fixed: {
        enabled: false,
        position: 'topRight',
        offsetX: 0,
        offsetY: 0,
      },
    },
    yaxis: {
      show: true,
      tickAmount: 4,
      labels: {
        offsetX: -17,
        style: {
          colors: disabledColor,
          fontSize: '12px',
        },
        formatter: value => `${ value > 999 ? `${ (value / 1000).toFixed(0) }` : value }`,
      },
    },
  }
})

const series = computed(() => {
  return [{
    data: dataCus.value,
  }]
})
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Sản phẩm được mua nhiều nhất</VCardTitle>
      <template #append>
        <div class="me-n3">
          <VCol cols="auto">
            <VDialog
              v-model="isActive"
              transition="dialog-bottom-transition"
            >
              <template #activator="{ props }">
                <VBtn
                  color="none"
                  v-bind="props"
                  icon="mdi-clock-time-eight-outline"
                >
                  <VIcon icon="mdi-clock-time-eight-outline" />
                </VBtn>
              </template>
              <Transition name="slide-fade">
                <VAlert 
                  v-if="error.status"
                  :color="error.color"
                  icon="mdi-alert"
                  :title="error.title"
                  closable
                  class="alert"
                  max-width="400px"
                  :text="error.text"
                  @click:close="error.status = false"
                />
              </Transition>
              <VCard>
                <VToolbar
                  color="primary"
                  title="Chọn mốc"
                />
                <VCardText>
                  <VForm class="mt-6">
                    <VRow>
                      <!-- 👉 From -->
                      <VCol
                        cols="12"
                        md="6"
                      >
                        <VTextField
                          v-model="date.from"
                          type="date" 
                          prepend-icon="mdi-rename"
                          label="Từ ngày"
                        />
                      </VCol>

                      <!-- 👉 To -->
                      <VCol
                        cols="12"
                        md="6"
                      >
                        <VTextField
                          v-model="date.to"
                          type="date"
                          prepend-icon="mdi-code-brackets"
                          label="Dến ngày"
                        />
                      </VCol>
                    </VRow>
                  </VForm>
                </VCardText>
                <VCardActions class="justify-end">
                  <VBtn
                    variant="text"
                    @click="isActive = false"
                  >
                    Đóng
                  </VBtn>
                  <VBtn
                    variant="text"
                    @click="confirm"
                  >
                    Xác nhận
                  </VBtn>
                </VCardActions>
              </VCard>
            </VDialog>
          </VCol>
        </div>
      </template> 
    </VCardItem>
    <VCardText>
      <VueApexCharts
        type="bar"
        :options="options"
        :series="series"
        :height="220"
        :width="500"
      />
    </VCardText>
  </VCard>
</template>

<style scoped>
.btn{
  cursor: pointer;
}
.btn:hover{
  background-color: #ECEFF1;
}
.alert{
  position: absolute;
  top: 20px;
  right: 10px;
  z-index: 100;
}
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>
