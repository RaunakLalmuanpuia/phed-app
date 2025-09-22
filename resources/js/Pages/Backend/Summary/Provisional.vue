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
                    <template v-slot:body-cell-name="props">
                        <q-td
                            :props="props"
                            class="sticky-col bg-white z-10"
                        >
                            {{ props.row.name }}
                        </q-td>
                    </template>

                    <template v-slot:header-cell-name="props">
                        <q-th
                            :props="props"
                            class="sticky-col bg-white z-20"
                        >
                            {{ props.col.label }}
                        </q-th>
                    </template>

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
                    <template v-slot:bottom-row>
                        <q-tr class="bg-grey-3 text-bold q-border-t">
                            <!-- First cell label -->
                            <q-td class="text-left font-bold">
                                <strong>Total</strong>
                            </q-td>

                            <!-- Dynamic designation totals -->
                            <q-td
                                v-for="designation in props.designations"
                                :key="designation"
                                class="text-center font-bold"
                            >
                                <strong>{{ getColumnTotal(designation) }}</strong>
                            </q-td>

                            <!-- Total column -->
                            <q-td class="text-center font-bold">
                                <strong>{{ getColumnTotal('total') }}</strong>
                            </q-td>
                        </q-tr>
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

const getColumnTotal = (field) => {
    return props.offices.reduce((sum, row) => {
        const value = Number(row[field]) || 0;
        return sum + value;
    }, 0);
};
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

<style scoped>
.sticky-col {
    position: sticky;
    left: 0;
    background: white; /* Ensure it doesn’t overlap weirdly */
    z-index: 1; /* Keep above normal cells */
}
</style>

