<script setup>
import { hexToRgb } from '@layouts/utils'
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'

const props = defineProps({
  id: Number,
})

const error = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})

const vuetifyTheme = useTheme()
const dataCus = ref([])
const labels = ref([])
const data  = ref([])
const isActive = ref(false)

const date = reactive({
  from: null,
  to: null,
})


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/orders_date', {
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

const confirm = () =>{
  console.log(date)

  const accessToken = localStorage.getItem('accessToken')

  axiosIns.post('api/orders_date_interval', date, {
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
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    xaxis: {
    //   type: 'datetime',
      tickPlacement: 'on',
      labels: { show: true },
      crosshairs: { opacity: 0 },
      axisTicks: { show: false },
      axisBorder: { show: false },
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

        // formatter: value => `$${ value > 999 ? `${ (value / 1).toFixed(0) }` : value }`,
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
  <VCard
    title="Đơn hàng theo ngày"
    prepend-icon="mdi-chart-line"
  >
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
                title="Chọn mốc thời gian"
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
                        label="Đến ngày"
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
                  Close
                </VBtn>
                <VBtn
                  variant="text"
                  @click="confirm"
                >
                  CONFIRM
                </VBtn>
              </VCardActions>
            </VCard>
          </VDialog>
        </VCol>
      </div>
    </template> 
     
    <VCardText>
      <VueApexCharts
        type="area"
        :options="options"
        :series="series"
        :height="220"
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