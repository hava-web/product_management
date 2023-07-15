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

  await axiosIns.get('api/customers_month', {
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
    xaxis: {
      categories: labelCus.value,
      type: 'datetime',
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
  data: dataCus.value,
}]
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Customers By Time</VCardTitle>
      <!--
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
        <VListItem class="btn">
        Customers By Mouth
        </VListItem>
        </VList>
        </VMenu>
        </VBtn>
        </div>
        </template> 
      -->
    </VCardItem>

    <VCardText>
      <VueApexCharts
        type="line"
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
</style>
