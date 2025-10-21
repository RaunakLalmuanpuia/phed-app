<template>
    <q-page class="container"  padding>
        <div class="row q-col-gutter-md">

            <div v-if="!isAdmin && !isReviewer" class="col-xs-12 col-sm-3">
                <div class="dealing-card q-pa-md count-warning cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.manager.all'))">
                    <div class="title-warning">Total Employees</div>
                    <div class="text-caption caption-sm">Current Count</div>
                    <div class="flex items-center">
                        <div class="count-dealing">{{ totalEmployees }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="!isAdmin && !isReviewer" class="col-xs-12 col-sm-3">
                <div class="purple-card q-pa-md count-purple cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.manager.wc'))">
                    <div class="title-purple">Work Charge</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-purple">{{ wcCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>

            <div v-if="!isAdmin && !isReviewer" class="col-xs-12 col-sm-3">
                <div class="verification-card q-pa-md count-blue cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.manager.pe'))">
                    <div class="title-blue">Provisional</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-blue">{{ peCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="!isAdmin && !isReviewer" class="col-xs-12 col-sm-3">
                <div class="approval-card q-pa-md count-green cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.manager.mr'))">
                    <div class="title-green">Muster Roll</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-green">{{ mrCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="!isAdmin && !isReviewer" class="col-xs-12 col-sm-3">
                <div class="scheme-card q-pa-md count-scheme cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.manager.scheme'))">
                    <div class="title-scheme">Scheme</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-scheme">{{ schemeCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>

            <div v-if="!isAdmin && !isReviewer" class="col-xs-12 col-sm-3">
                <div class="deletion-card q-pa-md count-red cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.manager.deleted'))">
                    <div class="title-red">Deleted</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-red">{{ deletedCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>



            <div v-if="isAdmin || isReviewer" class="col-xs-12 col-sm-3">
                <div class="dealing-card q-pa-md count-warning cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.all'))">
                    <div class="title-warning">Total Employees</div>
                    <div class="text-caption caption-sm">Current Count</div>
                    <div class="flex items-center">
                        <div class="count-dealing">{{ totalEmployees }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>

            <div v-if="isAdmin || isReviewer" class="col-xs-12 col-sm-3">
                <div class="purple-card q-pa-md count-purple cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.wc'))">
                    <div class="title-purple">Work Charge</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-purple">{{ wcCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="isAdmin || isReviewer" class="col-xs-12 col-sm-3">
                <div class="verification-card q-pa-md count-blue cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.pe'))">
                    <div class="title-blue">Provisional</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-blue">{{ peCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>
            <div v-if="isAdmin || isReviewer" class="col-xs-12 col-sm-3">
                <div class="approval-card q-pa-md count-green cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.mr'))">
                    <div class="title-green">Muster Roll</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-green">{{ mrCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>


            <div v-if="isAdmin || isReviewer" class="col-xs-12 col-sm-3">
                <div class="scheme-card q-pa-md count-scheme cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.scheme'))">
                    <div class="title-scheme">Scheme</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-scheme">{{ schemeCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>

            <div v-if="isAdmin || isReviewer" class="col-xs-12 col-sm-3">
                <div class="deletion-card q-pa-md count-red cursor-pointer hover:opacity-80" @click="$inertia.get(route('employees.deleted'))">
                    <div class="title-red">Deleted</div>
                    <div class="text-caption caption-sm">Employment Type</div>
                    <div class="flex items-center">
                        <div class="count-red">{{ deletedCount }}</div>
                        <div class="caption q-ml-md">Employees</div>
                    </div>
                </div>
            </div>



            <div v-if="isAdmin || isReviewer" class="col-xs-12">
                <div class="notification-card cursor-pointer hover:opacity-80" @click="$inertia.get(route('notification.list'))">
                    <div class="title-notification mb-2">Notifications</div>
                    <div class="text-caption caption-sm mb-4">Request Types</div>

                    <div class="q-pa-md flex justify-around items-center flex-wrap">
                        <!-- Edit Requests -->
                        <div  class="flex flex-col items-center mx-2">
                            <strong class="count-notification">{{ notifications.editRequests }}</strong>
                            <span class="caption text-center">Edit <br> Requests</span>
                        </div>

                        <!-- Transfer Requests -->
                        <div class="flex flex-col items-center mx-2">
                            <strong class="count-notification">{{ notifications.transferRequests }}</strong>
                            <span class="caption text-center">Transfer <br> Requests</span>
                        </div>

                        <!-- Document Edit Requests -->
                        <div class="flex flex-col items-center mx-2">
                            <strong class="count-notification">{{ notifications.documentEditRequests }}</strong>
                            <span class="caption text-center">Document Edit <br> Requests</span>
                        </div>

                        <!-- Document Delete Requests -->
                        <div class="flex flex-col items-center mx-2">
                            <strong class="count-notification">{{ notifications.documentDeleteRequests }}</strong>
                            <span class="caption text-center">Document Delete <br> Requests</span>
                        </div>

                        <!-- Deletion Requests -->
                        <div  class="flex flex-col items-center mx-2">
                            <strong class="count-notification">{{ notifications.deletionRequests }}</strong>
                            <span class="caption text-center">Deletion <br> Requests</span>
                        </div>


                    </div>
                </div>

            </div>






            <div class="col-xs-12 col-sm-12">
                <OfficeStat />
            </div>
        </div>
    </q-page>

</template>

<script setup>
import BackendLayout from "../../Layouts/BackendLayout.vue";
import OfficeStat from "../../Components/Common/OfficeStat.vue";
// import OfficeStat from "../../../Components/Common/OfficeStat.vue";
import {ref, reactive, computed} from 'vue'
import { BarChart, PieChart } from 'vue-chart-3'
import {usePage} from "@inertiajs/vue3";

defineOptions({layout:BackendLayout})
const props=defineProps(['totalEmployees','wcCount','peCount','mrCount','schemeCount','deletedCount','notifications'])

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

const isAdmin = computed(() => !!usePage().props.roles?.find(item => item === 'Admin'));

const isReviewer = computed(() => !!usePage().props.roles?.find(item => item === 'Reviewer'));

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


.purple-card{
    padding: 18px;
    background-color: #ecd2fc;
}

.scheme-card{
    padding: 18px;
    background-color: #faf2e1;
}

.notification-card{
    padding: 18px;
    background-color: #9eead6;
}
.count-green{
    font-size: 36px;
    font-weight: 600;
    line-height: normal;
    color: #29ad3d;
}

.count-purple{
    font-size: 36px;
    font-weight: 600;
    line-height: normal;
    color: #a859d9;
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

.count-scheme{
    font-size: 36px;
    font-weight: 600;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #d3b11f;
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

.count-notification{
    font-size: 36px;
    font-weight: 600;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #068a67;
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
.title-purple{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #a859d9;
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


.title-scheme{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #d3b11f;
}


.title-notification{
    font-size: 24px;
    font-weight: bold;
    text-align: left;
    color: #068a67;
}
.caption-sm{
    font-size: 12px;
    font-weight: normal;
    line-height: 1.25;

    color: #9b9b9b;
}
</style>
