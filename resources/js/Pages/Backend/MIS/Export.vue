<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Export Data</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el  class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>


        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <!-- Filter + Toolbar -->
                    <q-card flat class="q-mt-md bg-white shadow-1">
                        <q-card-section>
                            <div class="text-subtitle1 text-weight-medium text-grey-8 q-mb-md">
                                Export Filter
                            </div>

                            <div class="row q-col-gutter-md">

                                <q-select
                                    label="Select Office"
                                    class="col-12 col-sm-4"
                                    v-model="filters.office"
                                    :options="office"
                                    option-label="name"
                                    option-value="id"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                />
                                <q-select
                                    label="Select Employment Type"
                                    class="col-12 col-sm-4"
                                    v-model="filters.type"
                                    :options="type"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                />

                                <q-select
                                    label="Select Employment Type"
                                    class="col-12 col-sm-4"
                                    v-model="filters.type"
                                    :options="type"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                />


                            </div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-btn
                                    v-if="canExport"
                                    label="Export Data"
                                    icon="add"
                                    color="primary"
                                    @click="submitExport"
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </q-card-section>

        </q-card>
    </q-page>
</template>


<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";

import {useQuasar} from "quasar";
import {ref} from "vue";
const $q = useQuasar();
defineOptions({layout:BackendLayout})

const props=defineProps(['office','canExport'])

const filters = ref({
    type: null,
    office: null,

})

const type = [
    { label: 'MR', value: 'MR' },
    { label: 'PE', value: 'PE' },
    { label: 'DELETED', value: 'Deleted' },
]


const submitExport = () => {
    $q.dialog({
        title: 'Confirm Export',
        message: 'Are you sure you want to export the data?',
        cancel: true,
        persistent: true
    }).onOk(() => {
        const formData = new FormData();
        formData.append("type", filters.value.type);
        formData.append("office", filters.value.office);


        axios.post(route('mis.import-employee'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
            .then((res) => {
                $q.notify({
                    type: 'positive',
                    message: "Data Successfully Imported"
                })

            })
            .catch(error => {
                if (error.response) {
                    const errors = error.response.data.errors
                    const messages = []

                    if (errors) {
                        for (const field in errors) {
                            if (errors[field]) {
                                messages.push(...errors[field])
                            }
                        }

                        $q.notify({
                            type: 'negative',
                            message: messages,
                        })

                    } else if (error.response.data.message) {

                        $q.notify({
                            type: 'negative',
                            message: error.response.data.message,
                        })
                    }
                } else {
                    $q.notify({
                        type: 'negative',
                        message: 'System Error. Please try again later.'
                    })
                }
            })
    })
}
const goBack = () => {
    window.history.back()
}
</script>
<style scoped>

</style>
