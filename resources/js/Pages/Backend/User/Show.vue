<template>
<q-page class="container" padding>
    <div class="flex items-center justify-between q-pa-md bg-white">
        <div>
            <div class="stitle">{{data?.name}}</div>
            <q-breadcrumbs class="text-dark">
                <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                <q-breadcrumbs-el class="cursor-pointer" label="User Accounts" @click="$inertia.get(route('user.index'))"/>
                <q-breadcrumbs-el :label="data?.name"/>
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
                </div>

            </div>
            <div class="col-xs-12 col-sm-7">
                <q-list separator class="bg-white">
                    <q-item>
                        <q-item-section>
                            <q-item-label class="text-lg text-bold">Activity Logs</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-item  v-for="audit in audits" :key="audit.id">
                        <q-item-section>
                            <q-item-label>{{audit?.user?.name}} <strong> {{audit?.event}} </strong> at {{formatDateTime(audit.created_at)}}</q-item-label>
                        </q-item-section>
                        <q-item-section side>
                            <q-btn @click="openAudit(audit)" round icon="chevron_right" />
                        </q-item-section>
                    </q-item>
                </q-list>
            </div>
        </div>

    <q-dialog v-model="state.openAudit">
        <q-card style="width: 430px" class="q-pa-md">
            <div class="flex justify-between items-center">
                <div class="text-lg text-bold">Logs</div>
                <q-btn class="q-pa-sm" round v-close-popup icon="close"/>
            </div>
            <div class="text-md text-bold text-grey-7 q-mt-sm">Old Values</div>
            <div v-for="[key,value] in Object.entries(state.selected.old_values)" class="flex justify-between items-center">
                <div v-if="key!=='password' && key!=='id'" class="text-grey-7">{{key}}</div>
                <div  v-if="key!=='password' && key!=='id'"  class="text-weight-medium">{{value}}</div>
            </div>
            <q-separator class="q-mt-sm"/>
            <div class="text-md text-bold text-grey-7 q-mt-sm">New Values</div>
            <div v-for="[key,value] in Object.entries(state.selected.new_values)" class="flex justify-between items-center">
                <div  v-if="key!=='password' && key!=='id'"  class="text-grey-7">{{key}}</div>
                <div  v-if="key!=='password' && key!=='id'"  class="text-weight-medium">{{value}}</div>
            </div>
        </q-card>
    </q-dialog>


</q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import useUtils from "@/Compositions/useUtils";
import {reactive} from "vue";

defineOptions({layout:BackendLayout})
const props=defineProps(['data','audits']);
const q = useQuasar();
const {formatDateTime} = useUtils();
const state=reactive({
    openAudit:false,
    selected:null
})

const openAudit=val=>{
    state.selected = val;
    state.openAudit = true;
}

</script>
