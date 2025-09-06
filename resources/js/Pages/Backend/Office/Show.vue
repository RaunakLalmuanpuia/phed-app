<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">{{data?.name}}</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Offices" @click="$inertia.get(route('office.index'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>

        <div class="row q-col-gutter-md">
            <div class="col-xs-12 col-sm-5 q-pa-md">
                <div class="bg-white q-pa-md">
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Name</div>
                        <div class="text-weight-medium">{{ data?.name }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Type</div>
                        <div class="text-weight-medium">{{ data?.type }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Location</div>
                        <div class="text-weight-medium">{{ data?.location }}</div>
                    </div>
                </div>

            </div>

        </div>

    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";


defineOptions({layout:BackendLayout})
const props=defineProps(['data']);
const q = useQuasar();

const handleBack=e=>{
    window.history.back();
}

</script>
