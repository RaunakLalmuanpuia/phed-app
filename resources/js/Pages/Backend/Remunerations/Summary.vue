<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Remuneration Summary</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el
                        @click="$inertia.get(route('dashboard'))"
                        icon="dashboard"
                        label="Dashboard"
                        class="cursor-pointer"
                    />
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>

            <div class="q-gutter-sm">
                <q-btn label="Export" color="primary"  @click="exportData()" icon="download" />
            </div>

        </div>

        <br />

        <!-- Data Table -->
        <q-table
            :rows="filteredRows"
            :columns="columns"
            row-key="office_name"
            flat
            bordered
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


            <template v-slot:body-cell-one_month="props">
                <q-td :props="props">
                    {{ formatAmount(props.row.one_month) }}
                </q-td>
            </template>

            <template v-slot:body-cell-three_months="props">
                <q-td :props="props">
                    {{ formatAmount(props.row.three_months) }}
                </q-td>
            </template>

            <template v-slot:body-cell-six_months="props">
                <q-td :props="props">
                    {{ formatAmount(props.row.six_months) }}
                </q-td>
            </template>

            <template v-slot:body-cell-one_year="props">
                <q-td :props="props">
                    {{ formatAmount(props.row.one_year) }}
                </q-td>
            </template>

            <template v-slot:bottom-row>
                <q-tr v-if="!filter" class="bg-grey-3 text-bold q-border-t">
                    <q-td v-for="col in columns" :key="col.name" :class="'text-' + (col.align || 'left')" class="q-pa-xs">
                        <strong>
                            <!-- Format only numeric columns -->
                            <template v-if="['one_month','three_months','six_months','one_year'].includes(col.field)">
                                {{ formatAmount(totals[col.field]) }}
                            </template>
                            <template v-else>
                                {{ totals[col.field] }}
                            </template>
                        </strong>
                    </q-td>
                </q-tr>
            </template>
        </q-table>

    </q-page>
</template>

<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { useQuasar } from "quasar";
import { ref,onMounted, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";

const {formatDate} = useUtils();
defineOptions({ layout: BackendLayout });

const props = defineProps(["office", "canGenerateRemuneration"]);

const q =useQuasar();

const rows = ref([])
const totals = ref({})

const filter = ref("") // Search box model


const columns = [
    { name: 'office_name', label: 'Name of Office', field: 'office_name', align: 'left' },
    { name: 'employee_count', label: 'No. of PE', field: 'employee_count', align: 'center' },
    { name: 'one_month', label: 'Wages for 1 Month (₹)', field: 'one_month', align: 'right' },
    { name: 'three_months', label: 'Wages for 3 Months (₹)', field: 'three_months', align: 'right' },
    { name: 'six_months', label: 'Wages for 6 Months (₹)', field: 'six_months', align: 'right' },
    { name: 'one_year', label: 'Wages for 1 Year (₹)', field: 'one_year', align: 'right' },
]


// Computed filtered rows
const filteredRows = computed(() => {
    if (!filter.value) return rows.value
    return rows.value.filter(row =>
        row.office_name.toLowerCase().includes(filter.value.toLowerCase())
    )
})

const formatAmount = (val) => {
    if (val == null) return ''
    // force number, then format with 2 decimals and Indian grouping
    return new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(val)
}



onMounted(async () => {
    const response = await axios.get(route('remuneration.json-summary'))
    rows.value = response.data.data
    totals.value = response.data.totals
})
const exportData = () => {
    q.loading.show(); // Show loading indicator

    axios.get(route('export.remuneration-summary'), { responseType: 'blob' })
        .then((res) => {
            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            const currentYear = new Date().getFullYear(); // Get current year
            link.href = fileUrl;
            link.setAttribute('download', `remuneration-summary-${currentYear}.xlsx`); // Use current year in filename
            link.click();
        })
        .catch((err) => {
            q.notify({
                type: 'negative',
                message: err.response?.data?.message || 'Failed to download file',
            });
        })
        .finally(() => {
            q.loading.hide(); // Hide loading indicator
        });
};

const goBack = () => {
    window.history.back()
}


</script>

<style scoped></style>
