<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Remuneration List</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el
                        @click="$inertia.get(route('dashboard'))"
                        icon="dashboard"
                        label="Dashboard"
                    />
                    <q-breadcrumbs-el label="Remuneration" :to="route('mis.import')" />
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
                                    class="col-12 col-sm-6"
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
            title="Employees"
            :rows="rows"
            :columns="columns"
            row-key="id"
            :loading="loading"
            v-model:pagination="pagination"
            :rows-per-page-options="[5, 10, 20, 50]"
            @request="onRequest"
        >
            <template v-slot:top-right>
                <q-input
                    borderless
                    dense
                    debounce="800"
                    bg-color="grey-2"
                    outlined
                    v-model="search"
                    placeholder="Search"
                    clearable
                    @update:model-value="handleSearch"
                >
                    <template v-slot:append>
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
                    {{ props.row.remuneration_detail?.remuneration }}
                </q-td>
            </template>

            <template v-slot:body-cell-total="props">
                <q-td :props="props">
                    {{ props.row.remuneration_detail?.round_total }}
                </q-td>
            </template>

            <template v-slot:body-cell-next_increment_date="props">
                <q-td :props="props">
                    {{ props.row.remuneration_detail?.next_increment_date }}
                </q-td>
            </template>

            <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                    <q-btn
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
                        <div class="col-8 text-body1">{{ currentRow?.remuneration_detail?.next_increment_date || 0 }}</div>
                    </div>

                    <!-- Editable fields -->
                    <q-input
                        label="Remuneration"
                        type="number"
                        v-model="form.remuneration"
                        outlined
                        dense
                    />
                    <q-input
                        label="Next Increment Date"
                        type="date"
                        v-model="form.next_increment_date"
                        outlined
                        dense
                    />
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" v-close-popup />
                    <q-btn color="primary" label="Update" @click="submitForm" />
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

defineOptions({ layout: BackendLayout });

const props = defineProps(["office", "canGenerateRemuneration"]);

const filters = ref({
    offices: [], // multiple offices
});
const search = ref("");

const q = useQuasar();
const rows = ref([]);
const loading = ref(false);
const showDialog = ref(false);

const showTable = ref(false);
const empTable = ref(null);   // declare a ref for your table
function fetchEmployees() {

    if (!filters.value.offices || filters.value.offices.length === 0) {
        q.notify({
            type: 'negative',
            message: 'Please select at least one office'
        })
        return
    }
    showTable.value = true;
    rows.value = [];
    // trigger table request
    setTimeout(() => {
        // small delay to ensure table renders before firing request
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
    next_increment_date: ""
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
    { name: "total", label: "Total", field: "remuneration_detail.round_total", align: "right" },
    { name: "next_increment_date", label: "Next Increment", field: "remuneration_detail.next_increment_date", align: "left" },
    { name: 'actions', label: 'Actions', align: 'center' },
];

const formRowId = ref(null);
const currentRow = ref(null);


const openDialog = (row) => {
    currentRow.value = row;

    // Populate editable form fields
    if (row.remuneration_detail) {
        form.remuneration = row.remuneration_detail.remuneration;
        form.next_increment_date = row.remuneration_detail.next_increment_date;
    } else {
        form.reset();
    }

    formRowId.value = row.id;
    showDialog.value = true;
};

function onRequest(props) {

    const { page, rowsPerPage, sortBy, descending } = props.pagination;

    loading.value = true;
    axios
        .get(route("mis.json-remuneration"), {
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
    });
};


</script>

<style scoped></style>
