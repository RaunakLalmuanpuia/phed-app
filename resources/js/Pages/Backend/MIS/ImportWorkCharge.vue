<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Import Work Charge Data</div>
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
                                Import Filter
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

                                <q-file
                                    label="Upload Document"
                                    class="col-12 col-sm-4"
                                    v-model="filters.document"
                                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    outlined
                                    dense
                                    clearable
                                    use-chips
                                    :counter="false"
                                />
                            </div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-btn
                                    v-if="canImport"
                                    label="Import Data"
                                    icon="add"
                                    color="primary"
                                    @click="submitImport"
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

const props=defineProps(['office','canImport'])

const filters = ref({
    office: null,
    document:null
})




const submitImport = () => {
    $q.dialog({
        title: 'Confirm Import',
        message: 'Are you sure you want to import the data?',
        cancel: true,
        persistent: true
    }).onOk(() => {
        const formData = new FormData();
        formData.append("office", filters.value.office);
        formData.append("document", filters.value.document);


        axios.post(route('mis.import-work-charge-employee'), formData, {
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
