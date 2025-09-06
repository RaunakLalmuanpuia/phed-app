<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Document Types</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>

                </q-breadcrumbs>
            </div>

            <div class="flex q-gutter-sm">
                <q-btn @click="$inertia.get(route('document-type.create'))" color="btn-primary" label="New Document Type"/>
            </div>
        </div>
        <br/>
        <q-list>
            <q-item v-for="item in list" class="flex justify-between q-mt-sm list-row  rounded-borders">
                <div class="column q-py-sm">
                    <span>{{ item?.name }}</span>
                    <span>{{item?.description}}</span>
                </div>

                <div class="flex q-gutter-sm q-py-sm">
                    <q-btn @click="$inertia.get(route('document-type.edit',item.id))" size="small" icon="arrow_right"/>
                    <q-btn @click="$inertia.delete(route('document-type.destroy',item.id))" size="small" icon="delete"/>
                </div>
            </q-item>

        </q-list>

    </q-page>

</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {onMounted, reactive, computed} from "vue";
import {useQuasar} from "quasar";
import useUtils from "@/Compositions/useUtils";


defineOptions({layout: BackendLayout})

const props = defineProps(['list']);
const q = useQuasar();
const {formatDateTime, formatMoney} = useUtils();
const state = reactive({
})

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
