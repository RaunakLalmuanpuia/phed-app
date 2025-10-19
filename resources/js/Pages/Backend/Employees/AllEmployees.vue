<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">All Employee List</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                </q-breadcrumbs>
            </div>
            <div class="q-gutter-sm">
                <q-btn label="WC Summary" color="primary" @click="$inertia.get(route('summary.wc'))"/>
                <q-btn label="PE Summary" color="primary" @click="$inertia.get(route('summary.pe'))" />
                <q-btn label="MR Summary" color="primary" @click="$inertia.get(route('summary.mr'))" />
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

        <br/>
        <q-card flat>
            <q-card-section>

                <div class="flex items-center justify-between q-pa-md bg-white">
                    <div>
                        <div class="stitle">Offices</div>
                    </div>

                    <div class="flex q-gutter-sm">
                        <q-input
                            borderless
                            dense
                            outlined
                            clearable
                            debounce="800"
                            v-model="state.search"
                            placeholder="Search Office"
                            @update:model-value="handleSearch"
                        >
                            <template v-slot:append>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                    </div>
                </div>

                <div class="q-pa-md">
                    <!-- Office Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 q-mb-md">
                        <q-card
                            flat
                            bordered
                            class="bg-white q-px-md q-py-lg shadow-1 cursor-pointer"
                            v-for="office in offices"
                            @click="$inertia.get(route('employees.index-all', office))"
                            :key="office.title"
                        >
                            <div>
                                <div class="stitle">{{ office.name }}</div>
                                <div class="text-caption text-grey-6 q-mt-xs">{{ office.type }}</div>
                                <div class="grid grid-cols-3 gap-4 q-mt-sm">


                                    <div>

                                        <div class="text-h6 text-weight-bold">{{ office.wc_count }}</div>
                                        <div class="text-caption text-grey-7">WC</div>
                                    </div>

                                    <div>

                                        <div class="text-h6 text-weight-bold">{{ office.pe_count }}</div>
                                        <div class="text-caption text-grey-7">PE</div>
                                    </div>
                                    <div>

                                        <div class="text-h6 text-weight-bold">{{ office.mr_count }}</div>
                                        <div class="text-caption text-grey-7">MR</div>
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
import {reactive} from 'vue';

import BackendLayout from "@/Layouts/BackendLayout.vue";

import {router} from '@inertiajs/vue3'
defineOptions({layout:BackendLayout})

const props=defineProps(['offices','search','totalEmployees','wcCount','peCount','mrCount','deletedCount'])

const cards = [
    {
        title: 'Total',
        value: props.totalEmployees,
        icon: 'person_search',
        iconColor: 'light-blue-5',
        bgColor: '#d6f3ff',
    },
    {
        title: 'Work Charge',
        value: props.wcCount,
        icon: 'person',

        iconColor: 'red-6',
        bgColor: '#ffe1e1',
    },
    {
        title: 'Provisional',
        value: props.peCount,
        icon: 'person_add',
        iconColor: 'orange-5',
        bgColor: '#ffe9d6',
    },
    {
        title: 'Muster Roll',
        value: props.mrCount,
        icon: 'how_to_reg',
        iconColor: 'indigo-6',
        bgColor: '#d7d9ff',
    },



]

const cards2 = [
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



const state=reactive({
    search:props?.search,

})

const handleSearch=e=>{
    router.get(route('employees.all'), {
        search: state.search
    });
}
</script>

<style scoped>

</style>
