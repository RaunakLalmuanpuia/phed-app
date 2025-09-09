<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Engagement Card :<q-chip square v-for="office in offices" :key="office.value" :label="office.label"/></div>
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
        </div>

        <br />

        <!-- Filters -->
        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <q-card flat class="q-mt-md bg-white shadow-1">
                        <q-card-section>
                            <div class="text-subtitle1 text-weight-medium text-grey-8 q-mb-md">
                                Fiscal Year Filter
                            </div>

                            <div class="row q-col-gutter-md">

                                <q-input
                                    class="col-12 col-sm-4"
                                    outlined
                                    dense
                                    clearable
                                    label="Start Year"
                                    v-model="filters.startYear"
                                    mask="####"
                                    placeholder="YYYY"
                                    @clear="clearTable"
                                />

                                <q-input
                                    class="col-12 col-sm-4"
                                    outlined
                                    dense
                                    clearable
                                    label="End Year"
                                    v-model="filters.endYear"
                                    mask="####"
                                    placeholder="YYYY"
                                    @clear="clearTable"
                                />
                            </div>
                        </q-card-section>

                        <q-separator />

                        <!-- Toolbar -->
                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-btn
                                    v-if="canDownloadEngagementCard"
                                    label="Get Employee"
                                    color="primary"
                                    @click="fetchEmployees"
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </q-card-section>
        </q-card>

        <br />

        <!-- Data Table -->
        <q-table
            v-if="showTable"
            ref="empTable"
            title="Employees Engagement Cards"
            :rows="rows"
            :columns="columns"
            row-key="id"
            :loading="loading"
            v-model:pagination="pagination"
            :rows-per-page-options="[5, 10, 20, 50]"
            @request="onRequest"
            selection="multiple"
            v-model:selected="selectedEmployees"
        >
            <template v-slot:top-right>

                <q-btn
                    class="q-mr-sm"
                    icon="download"
                    v-if="selectedEmployees.length > 0"
                    label="Download ZIP"
                    color="primary"
                    @click="downloadBulkPdf"
                />


                <q-input
                    dense
                    outlined
                    debounce="800"
                    v-model="search"
                    placeholder="Search"
                    class="col-12 col-sm-auto"
                    clearable
                    @update:model-value="handleSearch"
                >
                    <template #append>
                        <q-icon name="search" />
                    </template>
                </q-input>
            </template>

            <template v-slot:body-cell-office="props">
                <q-td :props="props">
                    {{ props.row.office?.name }}
                </q-td>
            </template>

            <template v-slot:body-cell-card_no="props">
                <q-td :props="props">
                    {{ props.row.engagement_card[0]?.card_no ?? 'Not Generated' }}
                </q-td>
            </template>

            <template v-slot:body-cell-start_date="props">
                <q-td :props="props">
                    {{ formatDate(props.row.engagement_card[0]?.start_date) ?? '-' }}
                </q-td>
            </template>

            <template v-slot:body-cell-end_date="props">
                <q-td :props="props">
                    {{ formatDate(props.row.engagement_card[0]?.end_date) ?? '-' }}
                </q-td>
            </template>

            <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                    <q-btn
                        v-if="props.row.engagement_card[0]"
                        dense
                        flat
                        round
                        color="primary"
                        icon="download"
                        @click="downloadPdf(props.row.engagement_card[0])"
                        aria-label="Generate Card"
                    />
                </q-td>
            </template>
        </q-table>


    </q-page>
</template>

<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { useQuasar } from "quasar";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

import useUtils from "@/Compositions/useUtils";

const {formatDate} = useUtils();

defineOptions({ layout: BackendLayout });

const props = defineProps(["offices","canDownloadEngagementCard"]);

const filters = ref({
    startYear: "",
    endYear: "",
});



const search = ref("");
const selectedEmployees = ref([]);
const q = useQuasar();



const rows = ref([]);
const loading = ref(false);

const showTable = ref(false);
const empTable = ref(null);

function fetchEmployees() {

    if (!filters.value.startYear) {
        q.notify({ type: "negative", message: "Please select the Start Year" });
        return;
    }

    if (!filters.value.endYear) {
        q.notify({ type: "negative", message: "Please select the End Year" });
        return;
    }

    showTable.value = true;
    rows.value = [];

    setTimeout(() => {
        if (empTable.value) empTable.value.requestServerInteraction();
    });
}

function handleSearch() {
    pagination.value.page = 1;
    if (empTable.value) empTable.value.requestServerInteraction();
}
function clearTable() {
    rows.value = [];
    showTable.value = false;
}


const pagination = ref({
    sortBy: "name",
    descending: false,
    page: 1,
    rowsPerPage: 5,
    rowsNumber: 0,
});

const columns = [
    { name: "name", label: "Name", field: "name", align: "left", sortable: true },
    { name: "designation", label: "Designation", field: "designation", align: "left" },
    { name: "office", label: "Office", field: "office.name", align: "left" },
    { name: "card_no", label: "Card No.", field: "engagement_card.card_no", align: "center" },
    { name: "start_date", label: "Start Date", field: "engagement_card.start_date", align: "center" },
    { name: "end_date", label: "End Date", field: "engagement_card.end_date", align: "center" },
    { name: "actions", label: "Actions", align: "center" },
];

function onRequest(props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;
    loading.value = true;
    axios
        .get(route("mis.manager-json-engagement-card"), {
            params: {
                filter: { ...filters.value },
                page,
                rowsPerPage,
                search: search.value,
                sortBy,
                descending,
            },
        })
        .then((res) => {
            const { list } = res.data;
            const { data, per_page, current_page, total } = list;
            rows.value = data;
            pagination.value.page = current_page;
            pagination.value.rowsNumber = total;
            pagination.value.rowsPerPage = per_page;
        })
        .catch((err) => {
            q.notify({ type: "negative", message: err?.response?.data?.message });
        })
        .finally(() => (loading.value = false));
}

const downloadPdf = async (row) => {
    try {
        // Request PDF from backend using the employee ID or card_no
        const response = await axios.get(route('engagement-card.download', row), {
            responseType: 'blob'  // important for binary response
        });

        // Create a blob link to download
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `EngagementCard_${row.card_no}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();

    } catch (error) {
        q.notify({ type: 'negative', message: error });
    }
};

const downloadBulkPdf = async () => {
    if (selectedEmployees.value.length === 0) {
        return q.notify({ type: 'negative', message: 'Select employees first!' });
    }

    try {
        const employee_ids = selectedEmployees.value.map(e => e.id);

        // Make API request to generate ZIP
        const response = await axios.post(
            route('engagement-card.bulk-download'),
            {
                employee_ids,
                start_date: `${filters.value.startYear}-03-01`,
                end_date: `${filters.value.endYear}-02-28`
            },
            { responseType: 'blob' }
        );


        // Download the ZIP
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/zip' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `EngagementCards_${Date.now()}.zip`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        empTable.value?.requestServerInteraction();
        selectedEmployees.value = [];

        q.notify({ type: 'positive', message: 'ZIP downloaded successfully.' });

    } catch (error) {
        q.notify({ type: "negative", message: 'No Engagement card to Download' });
        console.error(error);
    }
};


const goBack = () => {
    window.history.back()
}

</script>
