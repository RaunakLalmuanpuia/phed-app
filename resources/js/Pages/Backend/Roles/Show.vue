<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">{{data?.name}} &nbsp; Role's</div>
                <q-breadcrumbs class="text-dark cursor-pointer">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('role.index'))"  label="Roles"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <q-separator class="q-my-md"/>

        <div class="q-pa-md bg-white">
            <div class="row q-col-gutter-md">
                <div class="col-xs-12 col-sm-4 col-md-3" v-for="item in state.permissions" :key="item">
                    <q-checkbox v-model="state.selected" :val="item" :label="item" />
                </div>
            </div>

            <br/>
            <q-separator class="q-my-md"/>
            <div class="flex q-gutter-md">
                <q-btn :disable="state.selected.length<=0" @click="submit" class="sized-btn" color="primary" label="Apply"/>
                <q-btn @click="$inertia.get(route('role.index'))" class="sized-btn" color="negative" label="Cancel"/>
            </div>
        </div>
        <br/>


    </q-page>

</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {onMounted, reactive, computed} from "vue";
import {useQuasar} from "quasar";
import useUtils from "@/Compositions/useUtils";
import {router} from "@inertiajs/vue3";


defineOptions({layout: BackendLayout})

const props = defineProps(['data','permissions']);
const q = useQuasar();
const {formatDateTime, formatMoney} = useUtils();
const state = reactive({
    selected:props.data.permissions.map(item=>item.name),
    permissions:props.permissions.map(item=>item.name)
})

const submit=e=>{
    router.put(route('role.update',props.data.id),{
        permissions: state.selected
    },{
        preserveState: false,
        onStart: params => q.loading.show({
            boxClass: 'bg-grey-2 text-grey-9',
            spinnerColor: 'primary', message: ' Updating...'
        }),
        onFinish: params => q.loading.hide()
    })
}
const handleBack=e=>{
    window.history.back();
}


</script>
<style scoped>
.list-row {
    padding: 12px;
    background-color: white;
}
</style>
