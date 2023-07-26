<script setup>
import { hexToRgb } from '@layouts/utils'
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import axiosIns from '@/plugins/axios'

const props = defineProps({
  id: Number,
})

const vuetifyTheme = useTheme()
const dataCus = ref([])


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/orders_employee_date/' + props.id, {
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

const orderWeek =  id =>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('api/orders_employee_week/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    dataCus.value = []
    dataCus.value.push(...res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
  })
}

const orderMonth =  id =>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('api/orders_employee_month/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    dataCus.value = []
    dataCus.value.push(...res.data)
    console.log(dataCus.value)
  }).catch(err=>{
    console.log(err.data)
  })
}

const orderYear =  id =>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('api/orders_employee_year/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    dataCus.value = []
    dataCus.value.push(...res.data)
    console.log(dataCus.value)
  }).catch(err=>{
    console.log(err.data)
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
    title="Tổng đơn hàng tạo được"
    prepend-icon="mdi-chart-bar"
  >
    <template #append>
      <div class="me-n3">
        <VBtn
          class="me-2"
          icon="mdi-bell-outline"
          color="none"
        >
          <VIcon icon="mdi-dots-vertical" />
          <VMenu
            activator="parent"
            location="right"
          >
            <VList>
              <VListItem
                class="btn"
                @click="orderWeek(props.id)"
              >
                Đơn hàng theo tuần
              </VListItem>
              <VListItem
                class="btn"
                @click="orderMonth(props.id)"
              >
                Đơn hàng theo tháng
              </VListItem>
              <VListItem
                class="btn"
                @click="orderYear(props.id)"
              >
                Đơn hàng theo năm
              </VListItem>
            </VList>
          </VMenu>
        </VBtn>
      </div>
    </template> 
     
    <VCardText>
      <VueApexCharts
        type="bar"
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
</style>