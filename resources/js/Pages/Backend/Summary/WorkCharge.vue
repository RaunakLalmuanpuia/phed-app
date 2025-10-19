<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Work Charge Employees Summary</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>

            <div class="q-gutter-sm">
                <q-btn label="Export" color="primary" @click="exportData()" icon="download" />
            </div>
        </div>

        <br/>

        <q-card flat>
            <q-card-section>
                <q-table
                    :rows="rows"
                    :columns="columns"
                    row-key="name_of_post"
                    flat
                    :filter="filter"
                    :pagination="{ rowsPerPage: 0 }"
                >
                    <template v-slot:body-cell-name_of_post="props">
                        <q-td :props="props" class="sticky-col bg-white z-10">
                            {{ props.row.name_of_post }}
                        </q-td>
                    </template>

                    <template v-slot:header-cell-name_of_post="props">
                        <q-th :props="props" class="sticky-col bg-white z-20">
                            {{ props.col.label }}
                        </q-th>
                    </template>

                    <template v-slot:top-right>
                        <q-input borderless dense debounce="800" v-model="filter" outlined clearable placeholder="Search Post">
                            <template v-slot:append>
                                <q-icon name="search" />
                            </template>
                        </q-input>
                    </template>

                    <template v-slot:bottom-row>
                        <q-tr class="bg-grey-3 text-bold q-border-t">
                            <q-td class="text-left font-bold"><strong>Total</strong></q-td>
                            <q-td></q-td> <!-- Group column empty -->
                            <q-td v-for="office in offices" :key="office" class="text-center font-bold">
                                <strong>{{ getColumnTotal(office) }}</strong>
                            </q-td>
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
import { ref } from "vue";
import { useQuasar } from "quasar";

defineOptions({ layout: BackendLayout });

const props = defineProps({
    rows: Array,
    offices: Array,
});

const q = useQuasar();
const filter = ref("");

// Columns: remove Sl., keep Name of Posts, Group, offices, Total
const columns = [
    { name: 'name_of_post', label: 'Name of Posts', field: 'name_of_post', align: 'left', sortable: true },
    { name: 'group', label: 'Group', field: 'group', align: 'center', sortable: true },
];

// Add office columns dynamically
props.offices.forEach(office => {
    columns.push({ name: office, label: office, field: office, align: 'center', sortable: true });
});

// Total column
columns.push({ name: 'total', label: 'Total', field: 'total', align: 'center', sortable: true });

const getColumnTotal = (field) => {
    return props.rows.reduce((sum, row) => sum + (Number(row[field]) || 0), 0);
};

const exportData = () => {
    q.loading.show();
    axios.get(route('export.summary-wc'), { responseType: 'blob' })
        .then(res => {
            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = fileUrl;
            link.setAttribute('download', `WC_Summary_${Date.now()}.xlsx`);
            link.click();
        })
        .catch(err => {
            q.notify({ type: 'negative', message: err.response?.data?.message || 'Failed to download file' });
        })
        .finally(() => q.loading.hide());
};

const handleBack = () => window.history.back();
</script>

<style scoped>
.sticky-col {
    position: sticky;
    left: 0;
    background: white;
    z-index: 1;
}
</style>
