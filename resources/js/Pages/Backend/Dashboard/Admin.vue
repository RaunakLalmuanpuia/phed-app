<template>
    <q-page class="container"  padding>
        <div class="row q-col-gutter-md">

            <div v-if="!applicant" class="col-xs-12 col-sm-3">
                <div class="dealing-card q-pa-md count-warning">
                    <div class="title-warning">Total Employees</div>
                    <div class="text-caption caption-sm">Current Count</div>
                    <div class="flex items-center">
                        <div class="count-dealing">{{ totalEmployees }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="!applicant" class="col-xs-12 col-sm-3">
                <div class="verification-card q-pa-md count-blue">
                    <div class="title-blue">Provisional</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-blue">{{ peCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="!applicant" class="col-xs-12 col-sm-3">
                <div class="approval-card q-pa-md count-green">
                    <div class="title-green">Muster Roll</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-green">{{ mrCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="!applicant" class="col-xs-12 col-sm-3">
                <div class="deletion-card q-pa-md count-red">
                    <div class="title-red">Deleted</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-red">{{ deletedCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>

<!--            <div v-if="!applicant" class="col-xs-12 col-sm-4">-->
<!--                <div class="q-pa-md bg-white">-->
<!--                    <div class="title">Skill Category Distribution</div>-->
<!--                    <br />-->
<!--                    <div class="row q-col-gutter-md">-->
<!--                        <div class="col-xs-12">-->
<!--                            <PieChart style="height: 320px" :chartData="skillCategoryData" :options="options" />-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->


            <div class="col-xs-12 col-sm-12">
                <OfficeStat />
            </div>
        </div>
    </q-page>

</template>

<script setup>
import BackendLayout from "../../../Layouts/BackendLayout.vue";
import OfficeStat from "../../../Components/Common/OfficeStat.vue";
import { ref, reactive } from 'vue'
import { BarChart, PieChart } from 'vue-chart-3'

defineOptions({layout:BackendLayout})
const props=defineProps(['totalEmployees','peCount','mrCount','deletedCount'])

// Dummy data for employee skill categories
const skillCategoryData = {
    labels: ['Skilled', 'Semi-skilled', 'Unskilled'],
    datasets: [
        {
            label: 'Skill Category',
            data: [50, 30, 20],
            borderWidth:1,
            borderColor: '#fff',
            width:10,
            backgroundColor: ['#4f5396','#a77f3d','#299894','#64378c','#333'],
        },
    ],
}

const options = ref({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'PHED Employee Management',
        },
    },
});

const state = reactive({
    totalEmployees: 100,
    contractualEmployees: 60,
    permanentEmployees: 40,
    offices: ['Office A', 'Office B', 'Office C', 'Office D'],
})

// Assume we're always showing admin view for now
const applicant = false
</script>
<style scoped>
.title{
    font-size: 24px;
    margin: 0;
    font-weight: bold;
    color: #191c51;
}
.dealing-card{
    padding: 18px;
    background-color: #fff8f0;
    color: #f27001;
}
.verification-card{
     padding: 18px;
    background-color: #eff7ff;
     color: #191c51;
 }
.approval-card{
    padding: 18px;

    background-color: #f0fdf6;
}

.deletion-card{
    padding: 18px;
    background-color: #fae1e1;
}
.count-green{
    font-size: 36px;
    font-weight: 600;
    line-height: normal;
    color: #29ad3d;
}
.count-warning{
    font-size: 36px;
    font-weight: 600;
    line-height: normal;
    color: #f27000;
}
.count-blue{
    font-size: 36px;
    font-weight: 600;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #1266ed;
}
.count-red{
    font-size: 36px;
    font-weight: 600;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #ed1224;
}
.caption{
    font-size: 16px;
    font-weight: 600;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.31;
    letter-spacing: normal;
    color: #080808;
}
.title-green{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #29ad3d;
}
.title-warning{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #f27000;
}
.title-blue{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #1266ed;
}

.title-red{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #ed1224;
}
.caption-sm{
    font-size: 12px;
    font-weight: normal;
    line-height: 1.25;

    color: #9b9b9b;
}
</style>
