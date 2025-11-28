<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Work Charge Employees Summary : {{officeName}}</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>

        </div>

        <br/>

        <q-card flat>
            <q-card-section>
                <div class="text-h6 q-mb-sm">Work Charge Employees Summary</div>
                <template v-if="props.rows.length > 0">
                    <q-table
                        :rows="rows"
                        :columns="columns"
                        row-key="name_of_post"
                        flat
                        :filter="filter"
                        :pagination="{ rowsPerPage: 0 }"
                        hide-bottom
                    >
                        <template v-slot:no-data>
                            <div class="text-center q-pa-md text-grey">
                                No data found
                            </div>
                        </template>
                        <!-- FOOTER TOTAL ROW -->
                        <template v-slot:bottom-row>
                            <q-tr class="bg-grey-3 text-bold q-border-t">
                                <q-td class="text-left"><strong>Total</strong></q-td>
                                <q-td></q-td> <!-- group blank -->
                                <q-td class="text-center"><strong>{{ getColumnTotal('total') }}</strong></q-td>
                            </q-tr>
                        </template>
                    </q-table>
                </template>
                <template v-else>
                    <div class="text-center q-pa-md text-grey">
                        No data found
                    </div>
                </template>
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
    rows: Array,       // each row: name_of_post, group, total
    officeName: String,
});

const q = useQuasar();
const filter = ref("");

// Table Columns (ONLY 3)
const columns = [
    { name: 'name_of_post', label: 'Name of Posts', field: 'name_of_post', align: 'left' },
    { name: 'group', label: 'Group', field: 'group', align: 'center' },
    { name: 'total', label: 'Total', field: 'total', align: 'center' },
];

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
            q.notify({ type: 'negative', message: 'Failed to download file' });
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
