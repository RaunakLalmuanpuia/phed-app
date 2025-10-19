<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Muster Roll Employee List</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>

            <div class="q-gutter-sm">
                <q-btn label="WC Summary" color="primary" @click="$inertia.get(route('summary.wc'))"/>
                <q-btn label="PE Summary" color="primary" @click="$inertia.get(route('summary.pe'))" />
                <q-btn label="MR Summary" color="primary" @click="$inertia.get(route('summary.mr'))" />
            </div>
        </div>
        <br>

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
                    <!-- Dashboard Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 q-mb-md">
                            <q-card
                                flat
                                bordered
                                class="bg-white q-px-md q-py-lg shadow-1 cursor-pointer"
                                v-for="office in offices"
                                @click="$inertia.get(route('employees.index-mr', office))"
                                :key="office.title"
                            >
                                <div>
                                    <div class="stitle">{{ office.name }}</div>
                                    <div class="text-caption text-grey-6 q-mt-xs">{{ office.type }}</div>
                                    <div class="text-h6 text-weight-bold">{{ office.mr_count }}</div>
                                    <div class="text-caption text-grey-7">Muster Roll Employees</div>
                                </div>
                            </q-card>
                        </div>
                </div>
            </q-card-section>


        </q-card>
    </q-page>
</template>



<script setup>
import {onMounted, reactive, ref, watch} from 'vue';

import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";
import {router} from "@inertiajs/vue3";

defineOptions({layout:BackendLayout})

const props=defineProps(['offices','search'])



const state=reactive({
    search:props?.search,

})

const handleSearch=e=>{
    router.get(route('employees.mr'), {
        search: state.search
    });
}
const goBack = () => {
    window.history.back()
}


</script>


<style scoped>

</style>


