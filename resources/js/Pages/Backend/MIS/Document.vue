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
            <q-card style="min-width: 900px">

                <q-card-section>
                    <div class="text-lg font-bold">Update Document</div>
                </q-card-section>

                <q-card-section class="q-gutter-md">

                        <!-- Grid: Left = Existing, Right = New Input -->
                        <div class="grid grid-cols-2 gap-4 items-start relative">
                            <!-- Left: Existing Value -->
                            <div>
                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Office</div>
                                    <div class="col-8 text-body1"> {{currentRow?.office.name}}</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'WC'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Employment Type</div>
                                    <div class="col-8 text-body1">Work Charge Employee</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'PE'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Employment Type</div>
                                    <div class="col-8 text-body1">Provisional Employee</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Employment Type</div>
                                    <div class="col-8 text-body1">Muster Roll Employee</div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Is Scheme</div>
                                    <div class="col-8 text-body1"> {{ currentRow?.scheme_id ? 'Yes' : 'No' }}</div>
                                </div>

                                <div
                                    class="row q-mb-sm items-center"
                                    v-if="currentRow?.scheme_id"
                                >
                                    <div class="col-4 text-subtitle2 text-grey-8">Scheme</div>
                                    <div class="col-8 text-body1">
                                        {{ currentRow?.scheme?.name }}
                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Name</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.name"  outlined dense :error="!!form.errors?.name"
                                                 :error-message="form.errors?.name" />

                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Email</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.email" type="email" outlined dense
                                                 :error="!!form.errors?.email" :error-message="form.errors?.email" />
                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Contact</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.mobile" mask="##########" outlined dense
                                                 :error="!!form.errors?.mobile" :error-message="form.errors?.mobile"
                                                 />
                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Parent Name</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.parent_name" outlined dense
                                                 :error="!!form.errors?.parent_name" :error-message="form.errors?.parent_name"
                                                 />
                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Date of Birth</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.date_of_birth" label="MM/DD/YYYY" type="date" outlined dense
                                                                           :error="!!form.errors?.date_of_birth" :error-message="form.errors?.date_of_birth"
                                                                            /></div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Present Address</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.address" outlined dense
                                                 :error="!!form.errors?.address" :error-message="form.errors?.address"
                                                  />
                                    </div>
                                </div>
                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Education Qln.</div>
                                    <div class="col-8 text-body1">
                                        <q-select v-model="form.educational_qln" :options="educationalQualifications" outlined dense
                                                  emit-value map-options :error="!!form.errors?.educational_qln" :error-message="form.errors?.educational_qln"
                                                   />
                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Technical Qln.</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.technical_qln"  outlined dense
                                                 :error="!!form.errors?.technical_qln" :error-message="form.errors?.technical_qln"
                                                  />
                                    </div>
                                </div>


                                <div v-if="currentRow?.employment_type === 'PE' || currentRow?.employment_type === 'WC'"  class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Designation</div>
                                    <div class="col-8 text-body1">
                                        <q-select v-model="form.designation" :options="designations" outlined dense
                                                  emit-value map-options :error="!!form.errors?.designation" :error-message="form.errors?.designation"
                                                  :rules="[val => !!val || 'Designation is required']" />
<!--                                        <q-input v-model="form.designation" outlined dense-->
<!--                                                 :error="!!form.errors?.designation" :error-message="form.errors?.designation"-->
<!--                                                  />-->

                                    </div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Post/Work Assigned</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.post_assigned" type="text" outlined dense
                                                 :error="!!form.errors?.post_assigned" :error-message="form.errors?.post_assigned"
                                                />
                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Workplace</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.name_of_workplace" outlined dense
                                                 :error="!!form.errors?.name_of_workplace" :error-message="form.errors?.name_of_workplace"
                                        />

                                    </div>
                                </div>

                                <div class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Date of Initial Engagement</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.date_of_engagement" label="MM/DD/YYYY" type="date" outlined dense
                                                 :error="!!form.errors?.date_of_engagement" :error-message="form.errors?.date_of_engagement"
                                        />
                                    </div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'WC'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Date of Retirement</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.date_of_retirement" label="MM/DD/YYYY" type="date" outlined dense
                                                 :error="!!form.errors?.date_of_retirement" :error-message="form.errors?.date_of_retirement"
                                        />
                                    </div>
                                </div>


                                <div v-if="currentRow?.employment_type === 'PE'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Engagement Card No.</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.engagement_card_no" type="text" outlined dense
                                                 :error="!!form.errors?.engagement_card_no" :error-message="form.errors?.engagement_card_no"
                                        />

                                    </div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Skill Category at Initial Engagement</div>
                                    <div class="col-8 text-body1">
                                        <q-select v-model="form.skill_category" :options="skills"
                                                  emit-value map-options outlined dense
                                                  :error="!!form.errors?.skill_category" :error-message="form.errors?.skill_category"
                                        />

                                    </div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Skill Category at Present</div>
                                    <div class="col-8 text-body1">
                                        <q-select v-model="form.skill_at_present" :options="skills"
                                                  emit-value map-options outlined dense
                                                  :error="!!form.errors?.skill_at_present" :error-message="form.errors?.skill_at_present"
                                                  />

                                    </div>
                                </div>

                                <div v-if="currentRow?.employment_type === 'MR'" class="row q-mb-sm items-center">
                                    <div class="col-4 text-subtitle2 text-grey-8">Post Selected</div>
                                    <div class="col-8 text-body1">
                                        <q-input v-model="form.post_per_qualification" label="Post Selected" outlined dense
                                                 :error="!!form.errors?.post_per_qualification" :error-message="form.errors?.post_per_qualification"
                                        />

                                    </div>
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
                                            <q-btn
                                                round
                                                dense
                                                flat
                                                icon="delete"
                                                color="negative"
                                                @click="deleteDocument(type.id)"
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

const {educationalQualifications,designations, skills,formatDate} = useUtils();
const q = useQuasar();

// Form
const form = useForm({
    name:"",
    email:"",
    mobile:"",
    parent_name:"",
    date_of_birth:"",
    address:"",
    designation:"",
    post_assigned:"",
    employment_type:"",
    educational_qln:"",
    technical_qln:"",
    name_of_workplace:"",
    post_per_qualification:"",
    engagement_card_no: "",
    date_of_engagement:"",
    date_of_retirement:"",
    skill_category:"",
    skill_at_present:"",
    documents:[],
    deletedDocuments:[],
    avatar:null,
    delete_avatar: false,
});


const filters = ref({
    offices: [], // multiple offices
    type: null,
    scheme:null,

});
const type = [
    {label: 'MR', value: 'MR'},
    {label: 'PE', value: 'PE'},
    {label: 'WC', value: 'WC'},
]

const scheme = [
    { label: 'No', value: 'no' },
    { label: 'Yes', value: 'yes' },
]

const search = ref("");

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
    previewUrl.value = defaultUrl
    form.avatar = null
    fileInput.value.value = null
    form.delete_avatar = true
}

// ✅ Use reactive URL that changes on edit or file upload
const previewUrl = ref('')

const deleteDocument = (typeId) => {
    if (form.documents[typeId]) {
        // Add typeId to deletedDocuments if not already added
        if (!form.deletedDocuments) form.deletedDocuments = [];
        if (!form.deletedDocuments.includes(typeId)) {
            form.deletedDocuments.push(typeId);
        }

        // Clear the document preview locally
        form.documents[typeId] = { file: null, url: null };
    }
};
const pagination = ref({
    sortBy: "name",
    descending: false,
    page: 1,
    rowsPerPage: 50,
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

    form.name = row.name;
    form.email = row.email;
    form.mobile = row.mobile;
    form.parent_name = row.parent_name;
    form.date_of_birth = row.date_of_birth;
    form.address = row.address;
    form.designation = row.designation;
    form.post_assigned = row.post_assigned;

    form.technical_qln = row.technical_qln;
    form.educational_qln = row.educational_qln;
    form.name_of_workplace = row.name_of_workplace;
    form.post_per_qualification = row.post_per_qualification;
    form.engagement_card_no = row.engagement_card_no;
    form.date_of_engagement = row.date_of_engagement;
    form.date_of_retirement = row.date_of_retirement;
    form.skill_category = row.skill_category;
    form.skill_at_present = row.skill_at_present
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

    formData.append('name', form.name || '')
    formData.append('email', form.email || '')
    formData.append('mobile', form.mobile || '')
    formData.append('parent_name', form.parent_name || '')
    formData.append('date_of_birth', form.date_of_birth || '')
    formData.append('address', form.address || '')
    formData.append('designation', form.designation || '')
    formData.append('post_assigned', form.post_assigned || '')
    formData.append('educational_qln', form.educational_qln || '')
    formData.append('technical_qln', form.technical_qln || '')
    formData.append('name_of_workplace', form.name_of_workplace || '')
    formData.append('post_per_qualification', form.post_per_qualification || '')
    formData.append('engagement_card_no', form.engagement_card_no || '')
    formData.append('date_of_engagement', form.date_of_engagement || '')
    formData.append('date_of_retirement', form.date_of_retirement || '')
    formData.append('skill_category', form.skill_category || '')
    formData.append('skill_at_present', form.skill_at_present || '')

    // Append deleted documents
    if (form.deletedDocuments && form.deletedDocuments.length > 0) {
        form.deletedDocuments.forEach(id => {
            formData.append('deleted_documents[]', id);
        });
    }



    // Append only new files
    Object.entries(form.documents).forEach(([typeId, doc]) => {
        if (doc?.file) {
            formData.append(`documents[${typeId}]`, doc.file);
        }
    });

    if (form.delete_avatar) {
        formData.append('delete_avatar', 1)
    }


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
            form.reset();
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
