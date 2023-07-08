<script setup>
import { hexToRgb } from '@layouts/utils'
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import axiosIns from '@/plugins/axios'

const vuetifyTheme = useTheme()
const dataCus = ref([])
const labelCus = ref([])


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('api/chart_customer/', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    labelCus.value = res.data.map(customer => `${customer.lastname} ${customer.firstname}`)
    dataCus.value = res.data.map(customer => customer.numbers_of_purchases)
    console.log(labelCus.value)
    console.log(dataCus.value)
  }).catch(err=>{
    console.log(err.data)
  })
})

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
      categories: labelCus.value,
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
        formatter: value => `${ value > 999 ? `${ (value / 1000).toFixed(0) }` : value }`,
      },
    },
  }
})

const series = [{
  data: [{
    x: 'category A',
    y: 10,
  }, {
    x: 'category B',
    y: 18,
  }, {
    x: 'category C',
    y: 13,
  }],
}]
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Customers Overview</VCardTitle>
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
