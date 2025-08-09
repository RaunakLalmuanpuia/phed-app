<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Provisional Employee List</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el label="All Employees" :to="route('mis.import')"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>
        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <div class="q-pa-md">
                        <!-- Dashboard Cards -->
                        <div class="q-gutter-md row q-col-gutter-md q-mb-md">
                            <q-card
                                flat
                                bordered
                                class="bg-white q-px-md q-py-lg shadow-1 col-12 col-sm-6 col-md-3 q-mr-sm"
                                v-for="office in offices"
                                @click="$inertia.get(route('employees.index-pe', office))"
                                :key="office.title"
                            >
                                <div>
                                    <div class="stitle">{{ office.name }}</div>
                                    <div class="text-caption text-grey-6 q-mt-xs">{{ office.type }}</div>
                                    <div class="text-h6 text-weight-bold">{{ office.pe_count }}</div>
                                    <div class="text-caption text-grey-7">Employees</div>
                                </div>
                            </q-card>
                        </div>


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

const props=defineProps(['offices'])




</script>


<style scoped>

</style>


