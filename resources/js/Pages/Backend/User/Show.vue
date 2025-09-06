<template>
<q-page class="container" padding>
    <div class="flex items-center justify-between q-pa-md bg-white">
        <div>
            <div class="stitle">{{data?.name}}</div>
            <q-breadcrumbs class="text-dark">
                <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                <q-breadcrumbs-el class="cursor-pointer" label="User Accounts" @click="$inertia.get(route('user.index'))"/>
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
                        <div class="text-grey-7">Designation</div>
                        <div class="text-weight-medium">{{ data?.designation }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Mobile</div>
                        <div class="text-weight-medium">{{ data?.mobile }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Email</div>
                        <div class="text-weight-medium">{{ data?.mobile }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Role</div>
                        <div v-for="item in data?.roles" class="text-weight-medium">{{ item?.name }}</div>
                    </div>
                    <div class="flex justify-between items-center  ">
                        <div class="text-grey-7">Office</div>
                        <div v-for="item in data?.offices" class="text-weight-medium">{{ item?.name }}</div>
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
