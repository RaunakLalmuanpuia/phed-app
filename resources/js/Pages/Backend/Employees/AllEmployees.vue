<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">All Employee List</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>

        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <!-- Dashboard Cards -->
                    <div class="q-gutter-md row q-col-gutter-md">
                        <q-card
                            flat
                            bordered
                            class="col-12 col-sm bg-white q-px-md q-py-lg shadow-1"
                            v-for="card in cards"
                            :key="card.title"
                        >
                            <div class="row justify-between items-center">
                                <div>
                                    <div class="text-grey-6 text-subtitle1">{{ card.title }}</div>
                                    <div class="text-h5 text-weight-bold">
                                        {{ card.value }}
                                    </div>
                                    <div class="text-caption text-grey-6 q-mt-xs">
                                        Employees
                                    </div>
                                </div>
                                <q-avatar
                                    size="48px"
                                    :style="{ backgroundColor: card.bgColor }"
                                    text-color="white"
                                >
                                    <q-icon :name="card.icon" :color="card.iconColor" size="20px" />
                                </q-avatar>
                            </div>
                        </q-card>
                    </div>



                </div>
            </q-card-section>

        </q-card>
        <br/>


        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <!-- Use wrap and gap via flex utilities instead of q-gutter-md -->
                    <div class="row wrap items-start">
                        <q-card
                            flat
                            bordered
                            class="bg-white q-px-md q-py-lg shadow-1 col-12 col-sm-6 col-md-3"
                            v-for="office in offices"
                            :key="office.id"
                        >
                            <div>
                                <div class="text-grey-6 text-subtitle1">{{ office.name }}</div>
                                <div class="text-caption text-grey-6 q-mt-xs">{{ office.type }}</div>
                                <div class="row q-mt-sm">
                                    <div class="col-auto">
                                        <div class="text-caption text-grey-7">Muster Roll</div>
                                        <div class="text-h6 text-weight-bold">{{ office.mr_count }}</div>
                                    </div>
                                    <div class="col-auto q-ml-md">
                                        <div class="text-caption text-grey-7">Provisional</div>
                                        <div class="text-h6 text-weight-bold">{{ office.pe_count }}</div>
                                    </div>
                                </div>
                            </div>
                        </q-card>
                    </div>
                </div>
            </q-card-section>
        </q-card>

        <br/>
        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <!-- Dashboard Cards -->
                    <div class="row q-col-gutter-lg q-mb-md">
                        <q-card
                            flat
                            bordered
                            class="bg-white q-px-md q-py-lg shadow-1 col-12 col-sm-6 col-md-3"
                            v-for="office in offices"
                            :key="office.title"

                        >
                            <div class="row justify-between items-center">
                                <div>
                                    <div class="text-grey-6 text-subtitle1">{{ office.type }}</div>
                                    <div class="text-h5 text-weight-bold">
                                        {{ office.name }}
                                    </div>
                                    <div class="text-caption text-grey-6 q-mt-xs">
                                        Employees
                                    </div>
                                </div>
                            </div>
                        </q-card>
                    </div>
                </div>
            </q-card-section>
        </q-card>

















    </q-page>
</template>



<script setup>
import {onMounted, ref, watch} from 'vue';

import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";

defineOptions({layout:BackendLayout})

const props=defineProps(['offices','totalEmployees','peCount','mrCount','deletedCount'])

const cards = [
    {
        title: 'Total',
        value: props.totalEmployees,
        icon: 'person_search',
        iconColor: 'light-blue-5',
        bgColor: '#d6f3ff',
    },
    {
        title: 'Muster Roll',
        value: props.mrCount,
        icon: 'how_to_reg',
        iconColor: 'indigo-6',
        bgColor: '#d7d9ff',
    },
    {
        title: 'Provisional',
        value: props.peCount,
        icon: 'person_add',
        iconColor: 'orange-5',
        bgColor: '#ffe9d6',
    },
    {
        title: 'Deleted',
        value: props.deletedCount,
        icon: 'person',

        iconColor: 'red-6',
        bgColor: '#ffe1e1',
    },

]

const cards2 = [
    {
        officeName: 'Head Office',
        officeType: 'Government',
        mrCount: props.mrCount,
        peCount: props.peCount,
        icon: 'business',
        iconColor: 'blue-6',
        bgColor: '#d6f3ff',
    },
    {
        officeName: 'Regional Office',
        officeType: 'Public Sector',
        mrCount: 50,
        peCount: 20,
        icon: 'domain',
        iconColor: 'indigo-6',
        bgColor: '#d7d9ff',
    },
    {
        officeName: 'Branch Office',
        officeType: 'Private',
        mrCount: 30,
        peCount: 15,
        icon: 'apartment',
        iconColor: 'orange-5',
        bgColor: '#ffe9d6',
    },
];




</script>


<style scoped>

</style>
