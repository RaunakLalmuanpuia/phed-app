<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Remuneration Summary :<q-chip square v-for="office in offices" :key="office.value" :label="office.label"/></div>
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
                <q-btn label="Export" color="primary" v-if="canExportRemunerationSummary"  @click="exportData()" icon="download" />
            </div>

        </div>

        <br />

        <!-- Data Table -->

        <q-table
            :rows="rows"
            :columns="columns"
            :filter="filter"
        row-key="sl_no"
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


<!--        &lt;!&ndash; Totals &ndash;&gt;-->
<!--        <div class="q-mt-md text-right">-->
<!--            <strong>Totals: </strong>-->
<!--            Remuneration: {{ formatAmount(totals.remuneration) }} |-->
<!--            Medical: {{ formatAmount(totals.medical_allowance) }} |-->
<!--            Total: {{ formatAmount(totals.total) }} |-->
<!--            Monthly Rem: {{ formatAmount(totals.monthly_rem) }}-->
<!--        </div>-->



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

const props = defineProps(["offices", "canGenerateRemuneration",'canExportRemunerationSummary']);

const q =useQuasar();

const rows = ref([])
const totals = ref({})

const filter = ref("") // Search box model


const columns = [
    { name: "sl_no", label: "Sl.No", field: "sl_no", align: "center" },
    { name: "name", label: "Name", field: "name", align: "left" },
    { name: "designation", label: "Designation", field: "designation", align: "left" },
    {
        name: "remuneration",
        label: "Remuneration",
        field: "remuneration",
        align: "right",
        format: (val) =>
            new Intl.NumberFormat("en-IN", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val ?? 0),
    },
    {
        name: "medical_allowance",
        label: "Medical (4%)",
        field: "medical_allowance",
        align: "right",
        format: (val) =>
            new Intl.NumberFormat("en-IN", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val ?? 0),
    },
    { name: "total", label: "Total", field: "total", align: "right" },
    { name: "monthly_rem", label: "Monthly Rem.", field: "monthly_rem", align: "right" },
    { name: "next_increment", label: "Next Increment", field: "next_increment", align: "center" },
    { name: "pay_matrix", label: "Pay Matrix", field: "pay_matrix", align: "center" },
];



onMounted(async () => {
    const response = await axios.get(route('mis.manager-json-remuneration'))
    rows.value = response.data.data
    totals.value = response.data.totals
})

const exportData = () => {
    q.loading.show(); // Show loading indicator

    axios.get(route('export.office-remuneration'), { responseType: 'blob' })
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
