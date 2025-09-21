<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Engagement Card</div>
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
                                Office Filter
                            </div>

                            <div class="row q-col-gutter-md">
                                <!-- Office Selector -->
                                <q-select
                                    label="Select Office(s)"
                                    class="col-12 col-sm-4"
                                    v-model="filters.offices"
                                    :options="office"
                                    option-label="name"
                                    option-value="id"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @clear="clearTable"
                                    @update:model-value="checkOffices"
                                />

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
                                    v-if="canGenerateEngagementCard"
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
                    icon="picture_as_pdf"
                    v-if="selectedEmployees.length > 0 && canCreateEngagementCard"
                    label="Generate Cards"
                    color="primary"
                    @click="openBulkGenerateDialog(selectedEmployees)"
                />


                <q-btn
                    class="q-mr-sm"
                    icon="download"
                    v-if="selectedEmployees.length > 0 && canDownloadEngagementCard"
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
                        v-if="canCreateEngagementCard"
                        dense
                        flat
                        round
                        color="primary"
                        icon="picture_as_pdf"
                        @click="openIndividualDialog(props.row.id)"
                        aria-label="Generate Card"
                    />

                    <q-btn
                        v-if="props.row.engagement_card[0] && canDownloadEngagementCard"
                        dense
                        flat
                        round
                        color="primary"
                        icon="download"
                        @click="downloadPdf(props.row.engagement_card[0])"
                        aria-label="Generate Card"
                    />

                    <q-btn
                        v-if="props.row.engagement_card[0] && canDeleteEngagementCard"
                        dense
                        flat
                        round
                        color="red"
                        icon="delete"
                        @click="deleteEngagementCard(props.row.engagement_card[0].id)"
                        aria-label="Delete Card"
                    />
                </q-td>
            </template>
        </q-table>

        <!-- Individual Dialog -->
        <q-dialog v-model="showIndividualDialog">
            <q-card>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 q-mb-md">Generate Engagement Card</div>
                    <q-form @submit.prevent="submitIndividualForm">
                        <div class="row q-col-gutter-md">
                            <q-input
                                filled
                                v-model="individualForm.start_date"
                                label="Start Date"
                                type="date"
                                class="col-6"
                                :error="individualForm.errors.start_date"
                            />
                            <q-input
                                filled
                                v-model="individualForm.end_date"
                                label="End Date"
                                type="date"
                                class="col-6"
                                :error="individualForm.errors.end_date"
                            />
                            <q-input
                                filled
                                v-model="individualForm.phed_file_no"
                                label="PHED File Number"
                                class="col-8 "
                                :error="individualForm.errors.phed_file_no"
                            />
                            <q-input
                                filled
                                v-model="individualForm.letter_date"
                                label="Letter Date"
                                type="date"
                                class="col-4 "
                                :error="individualForm.errors.letter_date"
                            />
                            <q-input
                                filled
                                v-model="individualForm.approval_dpar"
                                label="Approval DP&AR"
                                class="col-12"
                                :error="individualForm.errors.approval_dpar"
                            />
                            <q-input
                                filled
                                v-model="individualForm.approval_fin"
                                label="Approval Finance"
                                class="col-12"
                                :error="individualForm.errors.approval_fin"
                            />
                        </div>
                        <q-card-actions align="right">
                            <q-btn flat label="Cancel" color="negative" v-close-popup />
                            <q-btn flat label="Generate" color="primary" type="submit" />
                        </q-card-actions>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-dialog>

        <!-- Bulk Dialog -->
        <q-dialog v-model="showBulkDialog">
            <q-card>
                <q-card-section class="q-pa-md">
                    <div class="text-h6 q-mb-md">Generate Bulk Cards</div>
                    <q-form @submit.prevent="submitBulkForm">
                        <div class="row q-col-gutter-md">
                            <q-input
                                filled
                                v-model="bulkForm.start_date"
                                label="Start Date"
                                type="date"
                                class="col-6"
                                :error="bulkForm.errors.start_date"
                            />
                            <q-input
                                filled
                                v-model="bulkForm.end_date"
                                label="End Date"
                                type="date"
                                class="col-6"
                                :error="bulkForm.errors.end_date"
                            />
                            <q-input
                                filled
                                v-model="bulkForm.phed_file_no"
                                label="PHED File Number"
                                class="col-8"
                                :error="bulkForm.errors.phed_file_no"
                            />
                            <q-input
                                filled
                                v-model="bulkForm.letter_date"
                                label="Letter Date"
                                type="date"
                                class="col-4"
                                :error="bulkForm.errors.letter_date"
                            />
                            <q-input
                                filled
                                v-model="bulkForm.approval_dpar"
                                label="Approval DP&AR"
                                class="col-12"
                                :error="bulkForm.errors.approval_dpar"
                            />
                            <q-input
                                filled
                                v-model="bulkForm.approval_fin"
                                label="Approval Finance"
                                class="col-12"
                                :error="bulkForm.errors.approval_fin"
                            />
                        </div>

                        <q-card-actions align="right">
                            <q-btn flat label="Cancel" color="negative" v-close-popup />
                            <q-btn flat label="Generate" color="primary" type="submit" />
                        </q-card-actions>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-dialog>
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

const props = defineProps(["office", "canGenerateEngagementCard",'canCreateEngagementCard','canDownloadEngagementCard','canDeleteEngagementCard']);

const filters = ref({
    offices: [],
    startYear: "",
    endYear: "",
});



const search = ref("");
const selectedEmployees = ref([]);
const q = useQuasar();

const form = useForm({})

const rows = ref([]);
const loading = ref(false);

const showTable = ref(false);
const empTable = ref(null);

function fetchEmployees() {
    if (!filters.value.offices || filters.value.offices.length === 0) {
        q.notify({ type: "negative", message: "Please select an office" });
        return;
    }

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
function checkOffices(val) {
    if (!val || val.length === 0) clearTable();
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
        .get(route("mis.json-engagement-card"), {
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

// Individual Form & Dialog
const showIndividualDialog = ref(false);
const individualDialogId = ref(null);
const individualForm = useForm({
    employee_id:'',
    start_date: '',
    end_date: '',
    card_no: '',
    phed_file_no: '',
    letter_date:'',
    approval_dpar: '',
    approval_fin: '',
});

// Bulk Form & Dialog
const bulkForm = useForm({
    employee_ids: [],    // <-- add this
    start_date: '',
    end_date: '',
    phed_file_no: '',
    letter_date:'',
    approval_dpar: '',
    approval_fin: '',
});
function openIndividualDialog(employeeId) {
    individualDialogId.value = employeeId;
    individualForm.reset();
    showIndividualDialog.value = true;
}

function submitIndividualForm() {
    // Merge employee_id into the form itself
    individualForm.employee_id = individualDialogId.value;

    individualForm.post(route('engagement-card.generate'), {
        onSuccess: () => {
            q.notify({ type: 'positive', message: 'Engagement card generated.' });
            showIndividualDialog.value = false;
            empTable.value?.requestServerInteraction();
        },
        onError: (errors) => {
            Object.values(errors).forEach((error) => {
                q.notify({
                    type: 'negative',
                    message: error,
                    position: 'bottom',
                });
            });
        },
    });
}

// Bulk Form & Dialog
const showBulkDialog = ref(false);

function openBulkGenerateDialog(selected) {
    if (!selected.length) {
        return q.notify({ type: 'negative', message: 'Select employees first!' });
    }
    bulkForm.reset();
    bulkForm.employee_ids = selected.map(e => e.id); // now it will be tracked
    showBulkDialog.value = true;
}

function submitBulkForm() {
    bulkForm.post(route('engagement-card.bulk-generate'), {
        onSuccess: () => {
            q.notify({ type: 'positive', message: 'Bulk engagement cards generated.' });
            showBulkDialog.value = false;
            empTable.value?.requestServerInteraction();
            selectedEmployees.value = [];
        },
        onError: (errors) => {
            Object.values(errors).forEach((error) => {
                q.notify({ type: 'negative', message: error, position: 'bottom' });
            });
        }
    });
}

function deleteEngagementCard(props) {
    q.dialog({
        title: 'Confirm Deletion',
        message: 'Are you sure you want to delete this Engagement Card?',
        ok: {
            label: 'Yes',
            color: 'primary',
        },
        cancel: {
            label: 'No',
            color: 'red',
        },
    }).onOk(() => {
        form.delete(route('engagement-card.destroy', props), {
            onSuccess: () => {
                q.notify({ type: 'positive', message: 'Engagement card deleted.' });
                empTable.value?.requestServerInteraction();
            },
            onError: (errors) => {
                Object.values(errors).forEach((error) => {
                    q.notify({
                        type: 'negative',
                        message: error,
                        position: 'bottom',
                    });
                });
            },
        });

    }).onCancel(() => {
        q.notify({
            type: 'info',
            message: 'Deletion cancelled.',
            color: 'negative',
        });
    });
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
