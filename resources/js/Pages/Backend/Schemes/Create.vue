<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Create Scheme</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Schemes" @click="$inertia.get(route('scheme.index'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>
        <q-form @submit="submit" class="bg-white q-pa-md">
            <div style="max-width: 720px" class="row q-col-gutter-sm">
                <div class="col-xs-12 col-sm-3">
                    <div class="text-label q-mt-md">Name</div>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <q-input v-model="form.name"
                             :error="!!form.errors?.name"
                             :error-message="form.errors?.name?.toString()"
                             outlined
                             dense
                             item-aligned
                             :rules="[
                         val=>val !=='' || 'Name is required'
                     ]"/>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <div class="text-label q-mt-md">Description</div>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <q-input v-model="form.description"
                             :error="!!form.errors?.description"
                             :error-message="form.errors?.description?.toString()"
                             outlined
                             dense
                             item-aligned
                    />
                </div>

                <div class="col-xs-12">
                    <q-separator class="q-my-md"/>
                </div>
                <div class="col-xs-12 flex q-gutter-sm">
                    <q-btn class="sized-btn" type="submit" color="btn-primary" label="Save"/>
                    <q-btn class="sized-btn" color="negative" outline label="Cancel" @click="$inertia.get(route('scheme.index'))"/>
                </div>

            </div>
        </q-form>


    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

defineOptions({layout:BackendLayout})
const props=defineProps(['userRoles']);
const q = useQuasar();
const form=useForm({
    name:'',
    description:'',


})
const submit=e=>{
    form.post(route('scheme.store'),{
        onStart:params => q.loading.show(),
        onFinish:params => q.loading.hide()
    })
}
const handleBack=e=>{
    window.history.back();
}

</script>
