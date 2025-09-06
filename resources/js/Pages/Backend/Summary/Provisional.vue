<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Provisional Employees Summary</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>

            <div class="q-gutter-sm">
                <q-btn label="Export" color="primary"  @click="exportData()" icon="download" />

            </div>
        </div>

        <br/>

        <q-card flat>
            <q-card-section>
                <q-table
                    :rows="props.offices"
                    :columns="columns"
                    row-key="id"
                    flat
                    :filter="filter"
                    :pagination="{ rowsPerPage: 0 }"
                >
                    <template v-slot:top-right>
                        <q-input borderless dense debounce="800"
                                 v-model="filter"
                                 outlined
                                 clearable
                                  placeholder="Search Office">
                            <template v-slot:append>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                    </template>
                </q-table>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";
import { ref } from "vue";

defineOptions({ layout: BackendLayout });

const props = defineProps({
    offices: Array,
    designations: Array
});

const q = useQuasar();

const filter = ref(""); // Search box model
// Base columns
const columns = [
    { name: 'name', label: 'Office Name', field: 'name', align: 'left', sortable: true }
];

// Add dynamic designation columns
props.designations.forEach(designation => {
    columns.push({
        name: designation,
        label: designation,
        field: designation,
        align: 'center',
        sortable: true
    });
});

// Add total column
columns.push({
    name: 'total',
    label: 'Total',
    field: 'total',
    align: 'center',
    sortable: true
});

const exportData = () => {
    q.loading.show(); // Show loading indicator (assuming you're using Quasar's loading plugin)

    // Make a GET request to the URL with responseType as 'blob'
    axios.get(route('export.summary-pe'), { responseType: 'blob' })
        .then((res) => {
            // Create an object URL from the response data and trigger a download
            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = fileUrl;
            link.setAttribute('download', Date.now() + '.xlsx'); // Set a dynamic file name
            link.click();
        })
        .catch((err) => {
            // Show an error notification if something goes wrong
            q.notify({
                type: 'negative',
                message: err.response?.data?.message || 'Failed to download file',
            });
        })
        .finally(() => {
            q.loading.hide(); // Hide loading indicator
        });
};

const handleBack=e=>{
    window.history.back();
}

</script>


