<script setup>
import { ref, reactive, onMounted } from 'vue'
import {BarChart} from "vue-chart-3";
import {useQuasar} from "quasar";

const q = useQuasar();
// Grouped bar chart: PE vs MR by office
const employeeByOfficeChartData = ref({
    labels: [],
    datasets: [],
})


const state = reactive({
    offices: [],
})

const options = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'PHED Employee Management',
        },
    },
    layout: {
        padding: {
            bottom: 30,
        },
    },
    scales: {
        x: {
            ticks: {
                padding: 10,
            },
        },
        y: {
            beginAtZero: true,
        },
    },
})



onMounted(()=>{
    axios.get(route('dashboard.office-statistics'))
        .then(res=>{
            employeeByOfficeChartData.value = {
                labels: res.data.labels,
                datasets: res.data.datasets,
            }

        })
        .catch(err=>q.notify({type:'negative',message:err?.response?.data?.message}))
})

</script>

<template>
    <div class="q-pa-md bg-white full-width">
        <div class="text-lg text-primary text-bold">Office-wise PE & MR Count</div>
        <br />
        <div class="q-gutter-y-md">
            <BarChart
                class="full-width"
                style="height: 360px; width: 100%"
                :chartData="employeeByOfficeChartData"
                :options="options"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
