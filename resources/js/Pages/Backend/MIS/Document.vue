<template>
    <q-page class="container" padding>
        <!-- Header -->
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Employee Documents</div>
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

                            </div>
                        </q-card-section>

                        <q-separator />

                        <!-- Toolbar -->
                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-btn
                                    v-if="canUpdateDocument"
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
            title="Employees Document"
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

            <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                    <q-btn
                        v-if="canUpdateDocument"
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
                    <div class="text-h6">Update Document</div>
                </q-card-section>

                <q-card-section class="q-gutter-md">

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Name</div>
                        <div class="col-8 text-body1">{{ currentRow?.name }}</div>
                    </div>

                    <div class="row q-mb-sm items-center">
                        <div class="col-4 text-subtitle2 text-grey-8">Employment Type</div>
                        <div class="col-8 text-body1">{{ currentRow?.employment_type }}</div>
                    </div>

                    <q-separator/>
                    <div
                        class="col-12 col-sm-6"
                        v-for="(type, index) in documentTypes"
                        :key="type.id"
                    >
                        <div class="text-subtitle2 q-mb-xs">{{ type.name }}</div>
                        <q-file
                            filled
                            :model-value="form.documents[type.id]?.file || null"
                            @update:model-value="val => updateDocument(type.id, val)"
                            label="Upload File"
                            accept=".pdf,.jpg,.jpeg,.png"
                            clearable
                            class="full-width"

                        >
                            <template v-if="form.documents[type.id]?.url" v-slot:append>
                                <q-btn
                                    round
                                    dense
                                    flat
                                    icon="visibility"
                                    @click="viewDocument(form.documents[type.id].url)"
                                    class="q-ml-sm"
                                />
                            </template>
                        </q-file>


                    </div>

                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" v-close-popup @click="!showDialog" />
                    <q-btn
                        color="primary"
                        label="Update"
                        @click="submitForm"
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
import {router, useForm} from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";

defineOptions({ layout: BackendLayout });

const props = defineProps(["office", 'documentTypes',"canUpdateDocument"]);

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
            message: 'Please select an office'
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
    documents:[],
});

const pagination = ref({
    sortBy: "name",
    descending: false,
    page: 1,
    rowsPerPage: 5,
    rowsNumber: 0,
});
// Table columns
const columns = [
    { name: "name", label: "Name", field: "name", align: "left", sortable: true },
    { name: "employment_type", label: "Employment Type", field: "employment_type", align: "left", sortable: true },
    { name: "designation", label: "Designation", field: "designation", align: "left", sortable: true },
    { name: "post_assigned", label: "Post Assigned", field: "post_assigned", align: "left", sortable: true },
    { name: "office", label: "Office", field: "office.name", align: "left" },
    { name: 'actions', label: 'Actions', align: 'center' },
];

const formRowId = ref(null);
const currentRow = ref(null);


const openDialog = (row) => {
    currentRow.value = row;
    formRowId.value = row.id;

    // Reset before filling
    form.documents = [];

    // Map existing documents
    if (row.documents && row.documents.length > 0) {
        row.documents.forEach((doc) => {
            form.documents[doc.document_type_id] = {
                url: `/storage/${doc.path}`,   // ✅ make accessible via storage:link
                name: doc.name,
                type: doc.type,
                file: null // user may replace it
            }
        });
    }

    showDialog.value = true;
};

const updateDocument = (typeId, file) => {
    form.documents[typeId] = {
        ...form.documents[typeId],
        file // replace or add new
    }
}
function onRequest(props) {

    const { page, rowsPerPage, sortBy, descending } = props.pagination;

    loading.value = true;
    axios
        .get(route("mis.json-employee-document"), {
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

    const formData = new FormData();

    // Append only new files
    Object.entries(form.documents).forEach(([typeId, doc]) => {
        if (doc?.file) {
            formData.append(`documents[${typeId}]`, doc.file);
        }
    });

    axios.post(route('document.update', formRowId.value), formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
        .then(({ data }) => {
            const { employee, message } = data;

            q.notify({
                type: 'positive',
                message: message || 'Employee saved successfully.'
            });

            // Refresh table
            if (empTable.value) empTable.value.requestServerInteraction();

            // Close dialog
            showDialog.value = false;

            // Reset form
            form.documents = [];
            formRowId.value = null;
        })
        .catch((err) => {
            if (err.response) {
                // Laravel gave a response
                if (err.response.status === 422) {
                    const errors = err.response.data.errors;
                    const firstError = errors ? Object.values(errors)[0][0] : "Validation failed.";
                    q.notify({ type: "negative", message: firstError });
                } else {
                    q.notify({
                        type: "negative",
                        message: err.response.data?.message || "Something went wrong!"
                    });
                }
            } else if (err.request) {
                // No response received at all (e.g. file too large, server rejected early)
                q.notify({
                    type: "negative",
                    message: "Upload failed: File too large or server did not respond."
                });
            } else {
                q.notify({
                    type: "negative",
                    message: "Unexpected error occurred."
                });
            }
        });
};


const viewDocument = (url) => {
    window.open(url, '_blank')
}

const goBack = () => {
    window.history.back()
}

</script>

<style scoped></style>
