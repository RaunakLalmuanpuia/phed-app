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

                                <q-select
                                    label="Select Employment Type"
                                    class="col-12 col-sm-4"
                                    v-model="filters.type"
                                    :options="type"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @clear="clearTable"
                                    @update:model-value="handleSearch"
                                />

                                <q-select
                                    v-if="filters.type === 'MR'"
                                    label="Is Scheme?"
                                    class="col-12 col-sm-4"
                                    v-model="filters.scheme"
                                    :options="scheme"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
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

            <!-- User Cell -->
            <template v-slot:body-cell-employee="props">
                <q-td :props="props">
                    <div class="flex items-center gap-3">
                        <q-avatar>
                            <q-img
                                v-if="props.row.avatar"
                                :src="`/storage/${props.row.avatar}`"
                            />
                            <q-icon v-else name="person" size="md" color="primary"/>
                        </q-avatar>
                        <div>
                            <div class="text-body1">{{ props.row.name }}</div>
                            <div class="text-caption text-grey">{{ props.row.mobile }}</div>
                            <div class="text-caption text-grey">{{ formatDate(props.row.date_of_birth) }}</div>
                        </div>
                    </div>
                    {{}}
                </q-td>
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
            <q-card style="min-width: 750px">

                <q-card-section>
                    <div class="text-lg font-bold">Update Docment</div>
                </q-card-section>

                <q-card-section class="q-gutter-md">

                        <!-- Grid: Left = Existing, Right = New Input -->
                        <div class="grid grid-cols-2 gap-4 items-start relative">
                            <!-- Left: Existing Value -->
                            <div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Name</div>
                                    <div class="col-8 text-body1">{{ currentRow?.name }}</div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Parent Name</div>
                                    <div class="col-8 text-body1">{{ currentRow?.parent_name }}</div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Date of Birth</div>
                                    <div class="col-8 text-body1">{{ formatDate(currentRow?.date_of_birth) }}</div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Present Address</div>
                                    <div class="col-8 text-body1">{{ currentRow?.address }}</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'PE'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Employment Type</div>
                                    <div class="col-8 text-body1">Provisional Employee</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Employment Type</div>
                                    <div class="col-8 text-body1">Muster Roll Employee</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'PE'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Designation</div>
                                    <div class="col-8 text-body1">{{ currentRow?.designation }}</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Post/Work Assigned</div>
                                    <div class="col-8 text-body1">{{ currentRow?.post_assigned }}</div>
                                </div>
                            </div>

                            <!-- Vertical Separator -->
                            <div class="absolute top-0 bottom-0 left-1/2 w-px bg-gray-300"></div>

                            <!-- Right: New Input -->
                            <div>


                                <div class="q-pa-md">
                                    <div class="row items-center q-gutter-md">
                                        <!-- Profile Image Preview -->
                                        <div class="rounded-borders overflow-hidden" style="background-color: #8a82f7;">
                                            <q-img
                                                :src="previewUrl"
                                                alt="Profile Photo"
                                                style="width: 120px; height: 120px"
                                                img-class="object-cover"
                                            />
                                        </div>

                                        <!-- Buttons & Info -->
                                        <div class="column q-gutter-sm">
                                            <div class="row q-gutter-md">
                                                <q-btn
                                                    label="Upload new Photo"
                                                    color="primary"
                                                    @click="triggerFileInput"
                                                    class="text-weight-medium"
                                                    rounded
                                                    unelevated
                                                />
                                                <q-btn
                                                    label="Reset"
                                                    color="grey-4"
                                                    text-color="dark"
                                                    @click="resetPhoto"
                                                    class="text-weight-medium"
                                                    rounded
                                                    unelevated
                                                />
                                            </div>
                                            <p class="text-subtitle2 text-grey-7">
                                                Allowed JPG, GIF or PNG. Max size of 800K
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Hidden file input -->
                                    <input
                                        ref="fileInput"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleFileChange"
                                    />
                                </div>


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
                            </div>
                        </div>
                    <q-separator/>


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

const {formatDate} = useUtils();

const filters = ref({
    offices: [], // multiple offices
    type: null,
    scheme:null,

});
const type = [
    {label: 'MR', value: 'MR'},
    {label: 'PE', value: 'PE'},
]

const scheme = [
    { label: 'No', value: 'no' },
    { label: 'Yes', value: 'yes' },
]

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

const fileInput = ref(null)
const defaultUrl = 'https://storage.googleapis.com/a1aa/image/089155c9-d1c1-4945-5728-c9bdb3576171.jpg'



function triggerFileInput() {
    fileInput.value?.click()
}

function handleFileChange(event) {
    const file = event.target.files[0]
    if (file && file.size <= 800 * 1024) {
        const reader = new FileReader()
        reader.onload = () => {
            previewUrl.value = reader.result
        }
        form.avatar = file
        reader.readAsDataURL(file)
    } else {
        alert('File too large or invalid type. Max size: 800KB.')
    }
}

function resetPhoto() {
    previewUrl.value = props.data?.avatar
        ? `/storage/${props.data.avatar}`
        : defaultUrl
    form.avatar = null
    fileInput.value.value = null
}

// ✅ Use reactive URL that changes on edit or file upload
const previewUrl = ref('')

// Form
const form = useForm({
    documents:[],
    avatar:null,
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
    {name: 'employee', label: 'Employee', align: 'left', field: 'employee', sortable: true},
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

    if (row?.avatar) {
        previewUrl.value = `/storage/${row.avatar}` // Laravel public storage path
    } else {
        previewUrl.value = defaultUrl
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
    if (form.avatar && typeof form.avatar !== 'string') {
        formData.append('avatar', form.avatar)
    }

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
