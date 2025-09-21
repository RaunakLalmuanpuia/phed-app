<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Remuneration Details</div>
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
                                <q-select
                                    class="col-12 col-sm-4"
                                    outlined
                                    dense
                                    clearable
                                    label="Increment Year"
                                    v-model="filters.incrementYear"
                                    :options="yearOptions"
                                    emit-value
                                    map-options
                                    @clear="clearTable"
                                />

                            </div>
                        </q-card-section>

                        <q-separator />

                        <!-- Toolbar -->
                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-btn
                                    v-if="canGenerateRemuneration"
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
            title="Employees Remuneration"
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

                <q-btn class="q-mr-sm"  icon="upload"  v-if="selectedEmployees.length > 0 && canBulkUpdateRemuneration" label="Update" color="primary"  @click="openBulkUpdateDialog" />

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

            <template v-slot:body-cell-remuneration="props">
                <q-td :props="props">
                    ₹{{ props.row.remuneration_detail?.remuneration }}
                </q-td>
            </template>

            <template v-slot:body-cell-round_total="props">
                <q-td :props="props">
                    ₹{{ props.row.remuneration_detail?.round_total }}
                </q-td>
            </template>

            <template v-slot:body-cell-next_increment_date="props">
                <q-td :props="props">
                    {{ formatDate(props.row.remuneration_detail?.next_increment_date) }}
                </q-td>
            </template>

            <template v-slot:body-cell-next_increment_amount="props">
                <q-td :props="props">
                    ₹{{ props.row.remuneration_detail?.total }}
                </q-td>
            </template>

            <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                    <q-btn
                        v-if="canEditRemuneration"
                        dense
                        flat
                        round
                        color="primary"
                        icon="edit"
                        @click="openDialog(props.row)"
                        aria-label="Edit"
                    />
                </q-td>
            </template>
        </q-table>


        <q-dialog v-model="showDialog">
            <q-card style="min-width: 400px">
                <q-card-section>
                    <div class="text-h6">Edit Remuneration</div>
                </q-card-section>

                <q-card-section class="q-gutter-md">

                    <!-- Employee details as side-by-side label + value -->
                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Name</div>
                        <div class="col-8 text-body1">{{ currentRow?.name }}</div>
                    </div>

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Designation</div>
                        <div class="col-8 text-body1">{{ currentRow?.designation }}</div>
                    </div>

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Office</div>
                        <div class="col-8 text-body1">{{ currentRow?.office?.name }}</div>
                    </div>

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Current Remuneration (Total)</div>
                        <div class="col-8 text-body1">Rs {{ currentRow?.remuneration_detail?.total || 0 }}</div>
                    </div>

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Date of Next Increment</div>
                        <div class="col-8 text-body1">{{ formatDate(currentRow?.remuneration_detail?.next_increment_date) || 0 }}</div>
                    </div>

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Pay Matrix</div>
                        <div class="col-8 text-body1">{{ currentRow?.remuneration_detail?.pay_matrix}}</div>
                    </div>

                    <!-- Remuneration Input -->
                    <q-input
                        label="Remuneration"
                        type="number"
                        v-model="form.remuneration"
                        outlined
                        dense
                        :readonly="!editMode"

                    >
                        <template v-slot:append>
                            <q-icon
                                name="edit"
                                class="cursor-pointer"
                                @click="enableEdit"
                            />
                        </template>
                    </q-input>

                    <!-- Next Increment Date Input -->
                    <q-input
                        label="Next Increment Date"
                        type="date"
                        v-model="form.next_increment_date"
                        outlined
                        dense
                        :readonly="!editMode"
                    />
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" v-close-popup @click="cancelEdit" />
                    <q-btn
                        color="primary"
                        label="Update"
                        :disable="!editMode"
                        @click="submitForm"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>


        <q-dialog v-model="showBulkDialog">


            <q-card style="min-width: 300px">
                <q-card-section>
                    <div class="text-h6">Update Remuneration</div>
                </q-card-section>

                <q-card-section>
                    <q-banner class="bg-grey-2 text-body2 q-pa-sm rounded-borders">
                        Note: When updating the increment date, each selected employee’s
                        <strong>remuneration</strong> will be set to their
                        <strong>next increment amount</strong>, and the new increment date will be applied
                        based on this updated remuneration.
                    </q-banner>
                </q-card-section>

                <q-card-section>
                    <q-input
                        type="date"
                        v-model="bulkForm.next_increment_date"
                        outlined
                        label="New Increment Date"
                        dense
                        :error="!!bulkForm.errors.next_increment_date"
                        :error-message="bulkForm.errors.next_increment_date"
                    />
                </q-card-section>



                <q-card-actions align="right">
                    <q-btn flat label="Cancel" v-close-popup @click="cancelBulkEdit" />
                    <q-btn
                        color="primary"
                        label="Update"
                        :loading="bulkForm.processing"
                        @click="submitBulkUpdate"
                    />
                </q-card-actions>
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

const props = defineProps(["office", "canGenerateRemuneration",'canCreateRemuneration','canEditRemuneration','canBulkUpdateRemuneration']);

const filters = ref({
    offices: [], // multiple offices
    incrementYear: "",   // 🔹 new
});

// Generate a range of years (e.g., from 2020 to 2030)
const startYear = 2020;
const endYear = 2035;

const yearOptions = Array.from(
    { length: endYear - startYear + 1 },
    (_, i) => startYear + i
);
const search = ref("");

const selectedEmployees = ref([])
const showBulkDialog = ref(false)

const q = useQuasar();
const rows = ref([]);
const loading = ref(false);



const bulkForm = useForm({
    employee_ids: [],
    next_increment_date: ''
})


const editMode = ref(false);
const showDialog = ref(false);

const showTable = ref(false);
const empTable = ref(null);   // declare a ref for your table
function fetchEmployees() {
    if (!filters.value.offices || filters.value.offices.length === 0) {
        q.notify({
            type: 'negative',
            message: 'Please select an office'
        })
        return
    }

    if (!filters.value.incrementYear) {
        q.notify({
            type: 'negative',
            message: 'Please enter the increment year'
        })
        return
    }

    showTable.value = true;
    rows.value = [];

    setTimeout(() => {
        if (empTable.value) empTable.value.requestServerInteraction();
    });
}

function handleSearch () {

    // reset to first page whenever search changes
    pagination.value.page = 1;

    if (empTable.value) {
        empTable.value.requestServerInteraction();
    }
}
function clearTable() {
    rows.value = [];
    showTable.value = false;
}
function checkOffices(val) {
    if (!val || val.length === 0) {
        clearTable();
    }
}

// Form
const form = useForm({
    remuneration: "",
    next_increment_date: "",
    pay_matrix:"",
});

const pagination = ref({
    sortBy: "next_increment_date",
    descending: false,
    page: 1,
    rowsPerPage: 5,
    rowsNumber: 0,
});
// Table columns
const columns = [
    { name: "name", label: "Name", field: "name", align: "left", sortable: true },
    { name: "designation", label: "Designation", field: "designation", align: "left", sortable: true },
    { name: "office", label: "Office", field: "office.name", align: "left" },
    { name: "remuneration", label: "Remuneration", field: "remuneration_detail.remuneration", align: "right" },
    { name: "round_total", label: "Total Monthly Remuneration", headerStyle: 'white-space: normal; word-wrap: break-word;', field: "remuneration_detail.round_total", align: "right" },
    { name: "next_increment_date", label: "Date of Next Increment",headerStyle: 'white-space: normal; word-wrap: break-word;', field: "remuneration_detail.next_increment_date", align: "left" },
    { name: "next_increment_amount", label: "Remuneration After Increment",headerStyle: 'white-space: normal; word-wrap: break-word;', field: "remuneration_detail.total", align: "left" },
    { name: 'actions', label: 'Actions', align: 'center' },
];

const formRowId = ref(null);
const currentRow = ref(null);


const openDialog = (row) => {
    currentRow.value = row;

    if (row.remuneration_detail) {
        form.remuneration = row.remuneration_detail.total;
        form.next_increment_date = row.remuneration_detail.next_increment_date;
        form.pay_matrix = row.remuneration_detail.pay_matrix;
    } else {
        form.reset();
    }

    formRowId.value = row.id;
    showDialog.value = true;
    editMode.value = false; // 🔹 start as readonly
};
function openBulkUpdateDialog () {
    bulkForm.reset()
    bulkForm.employee_ids = selectedEmployees.value.map(e => e.id)
    showBulkDialog.value = true
}

const enableEdit = () => {
    editMode.value = true; // enable editing
};

const cancelEdit = () => {
    editMode.value = false;
    showDialog.value = false;
    form.reset();
};

const cancelBulkEdit = () => {
    showBulkDialog.value = false;
    bulkForm.reset();
};

function onRequest(props) {

    const { page, rowsPerPage, sortBy, descending } = props.pagination;

    loading.value = true;
    axios
        .get(route("remuneration.json-detail"), {
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

const submitForm = () => {
    if (!formRowId.value) return;

    form.put(route("remuneration.update", formRowId.value), {
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
            // optionally refresh table
            if (empTable.value) empTable.value.requestServerInteraction();
            q.notify({ type: "positive", message: "Remuneration updated successfully." });
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
};


function submitBulkUpdate () {
    if (!bulkForm.next_increment_date) {
        q.notify({ type: 'negative', message: 'Please select a new increment date' })
        return
    }

    bulkForm.post(route('remuneration.bulk-update'), {
        preserveScroll: true,
        onSuccess: () => {
            q.notify({ type: 'positive', message: 'Increment updated successfully' })
            showBulkDialog.value = false
            selectedEmployees.value = []
            bulkForm.reset();
            // onRequest({ pagination: pagination.value }) // reload table
            if (empTable.value) empTable.value.requestServerInteraction();
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
    })
}

const goBack = () => {
    window.history.back()
}


</script>

<style scoped></style>
