<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import axiosIns from '@/plugins/axios'

const props = defineProps({
  id: Number,
})

const vuetifyTheme = useTheme()
const dataCus = ref([])
const label = ref([])
const data = ref([])


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/products_warehouse/' + props.id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    dataCus.value.push(...res.data)
    dataCus.value.forEach(obj => {
      label.value.push(obj.x)
      data.value.push(obj.y)
    })
    console.log(label.value)
    console.log(data.value)

  }).catch(err=>{
    console.log(err.data)
  })
})

const options = controlledComputed(() => vuetifyTheme.name.value, () => {
  return {
    chart: {
      type: "pie",
    },
   
    labels: label.value,
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
          },
          legend: {
            position: "bottom",
          },
        },
      },
    ],
  }
})

const series = data.value
</script>

<template>
  <VCard
    title="Tất cả sản phẩm trong kho"
    prepend-icon="mdi-chart-pie"
  >
    <VueApexCharts
      type="pie"
      :options="options"
      :series="series"
      :width="380"
      :height="380"
    />
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

