<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">All Employee List : {{ office.name }}</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard"
                                      label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>

            <div class="q-gutter-sm">
                <q-btn label="WC Summary" color="primary" @click="$inertia.get(route('summary.wc'))"/>
                <q-btn label="PE Summary" color="primary" @click="$inertia.get(route('summary.pe'))"/>
                <q-btn label="MR Summary" color="primary" @click="$inertia.get(route('summary.mr'))"/>
            </div>

        </div>
        <br/>

        <q-card flat>
            <q-card-section>
                <div class="q-pa-md">
                    <!-- Dashboard Cards -->
                    <div class="q-gutter-md row q-col-gutter-md">
                        <q-card
                            flat
                            bordered
                            class="col-12 col-sm bg-white q-px-md q-py-lg shadow-1"
                            v-for="card in cards"
                            :key="card.title"
                        >
                            <div class="row justify-between items-center">
                                <div>
                                    <div class="text-grey-6 text-subtitle1">{{ card.title }}</div>
                                    <div class="text-h5 text-weight-bold">
                                        {{ card.value }}
                                    </div>
                                    <div class="text-caption text-grey-6 q-mt-xs">
                                        Employees
                                    </div>
                                </div>
                                <q-avatar
                                    size="48px"
                                    :style="{ backgroundColor: card.bgColor }"
                                    text-color="white"
                                >
                                    <q-icon :name="card.icon" :color="card.iconColor" size="20px"/>
                                </q-avatar>
                            </div>
                        </q-card>
                    </div>

                    <!-- Filter + Toolbar -->
                    <q-card flat bordered class="q-mt-md bg-white shadow-1">
                        <q-card-section>
                            <div class="text-subtitle1 text-weight-medium text-grey-8 q-mb-md">
                                Search Filter
                            </div>

                            <div class="row q-col-gutter-md">

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
                                    @update:model-value="handleSearch"
                                />

                                <q-select
                                    v-if="filters.type === 'PE'"
                                    label="Select Designation"
                                    class="col-12 col-sm-4"
                                    v-model="filters.designation"
                                    :options="designationsPe"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
                                />
                                <q-select
                                    v-if="filters.type === 'WC'"
                                    label="Select Designation"
                                    class="col-12 col-sm-4"
                                    v-model="filters.designation"
                                    :options="designationsWc"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
                                />
                                <q-select
                                    v-if="filters.type === 'MR'"
                                    label="Select Skill"
                                    class="col-12 col-sm-4"
                                    v-model="filters.skill"
                                    :options="skills"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
                                />

                                <q-select
                                    v-if="filters.type === 'MR'"
                                    label="Select Education Qln."
                                    class="col-12 col-sm-4"
                                    v-model="filters.education_qln_mr"
                                    :options="educationQlnMr"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
                                />

                                <q-select
                                    v-if="filters.type === 'PE'"
                                    label="Select Education Qln."
                                    class="col-12 col-sm-4"
                                    v-model="filters.education_qln_pe"
                                    :options="educationQlnPe"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
                                />

                            </div>
                        </q-card-section>

                        <q-separator/>

                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">

                                <q-btn label="Export" icon="desktop_windows" color="primary" @click="exportData"/>

                                <q-input
                                    dense
                                    outlined
                                    debounce="300"
                                    v-model="filters.search"
                                    placeholder="Search"
                                    class="col-12 col-sm-auto"
                                    clearable
                                    @update:model-value="handleSearch"
                                >
                                    <template #append>
                                        <q-icon name="search"/>
                                    </template>
                                </q-input>

                                <!--                <q-btn-->
                                <!--                    label="Add New Employee"-->
                                <!--                    icon="add"-->
                                <!--                    color="primary"-->
                                <!--                    @click="$inertia.get(route('employee.create'))"-->
                                <!--                />-->
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </q-card-section>
            <q-table
                flat
                ref="tableRef"
                :title="office.name"
                :rows="rows"
                :columns="columns"
                row-key="id"
                v-model:pagination="pagination"
                :loading="loading"
                :filter="filters"
                binary-state-sort
                :rows-per-page-options="[1,7,15,30,50]"
                @request="onRequest"
            >
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
                    </q-td>
                </template>

                <!-- Role Cell -->
                <template v-slot:body-cell-office="props">
                    <q-td :props="props">
                        <q-chip color="primary" text-color="white" dense>{{ props.row.office?.name }}</q-chip>
                    </q-td>
                </template>

                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn
                            dense
                            flat
                            round
                            color="primary"
                            icon="visibility"
                            @click="$inertia.get(route('employee.show',props.row.id))"
                            aria-label="Show user"
                        />
                    </q-td>
                </template>
            </q-table>
        </q-card>
    </q-page>
</template>


<script setup>
import {onMounted, ref, watch} from 'vue';

import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";
import useUtils from "@/Compositions/useUtils";

const {formatDate} = useUtils();
defineOptions({layout: BackendLayout})

const props = defineProps(['office', 'totalEmployees','wcCount', 'peCount', 'mrCount', 'deletedCount','designationsWc', 'designationsPe', 'skills', 'educationQlnPe', 'educationQlnMr'])


const columns = [
    {name: 'employee', label: 'Employee', align: 'left', field: 'employee', sortable: true},
    {name: 'employment_type', label: 'Employment Type', align: 'left', field: 'employment_type', sortable: false},
    {name: 'designation', label: 'Designation', align: 'left', field: 'designation', sortable: false},
    {name: 'post_assigned', label: 'Post/Work Assigned', align: 'left', field: 'post_assigned', sortable: false},
    {name: 'name_of_workplace', label: 'Workplace', align: 'left', field: 'name_of_workplace', sortable: false},
    {name: 'office', label: 'Office', align: 'left', field: 'office', sortable: true},
    {name: 'actions', label: 'Actions', align: 'center'},
];

const cards = [
    {
        title: 'Total',
        value: props.totalEmployees,
        icon: 'person_search',
        iconColor: 'light-blue-5',
        bgColor: '#d6f3ff',
    },
    {
        title: 'Work Charge',
        value: props.wcCount,
        icon: 'person',
        iconColor: 'red-6',
        bgColor: '#ffe1e1',
    },
    {
        title: 'Provisional',
        value: props.peCount,
        icon: 'person_add',
        iconColor: 'orange-5',
        bgColor: '#ffe9d6',
    },
    {
        title: 'Muster Roll',
        value: props.mrCount,
        icon: 'how_to_reg',
        iconColor: 'indigo-6',
        bgColor: '#d7d9ff',
    },


]

const filters = ref({
    type: null,
    skill: null,
    search: null,
    designation: null,
    education_qln_pe: null,
    education_qln_mr: null,
})
const type = [
    {label: 'MR', value: 'MR'},
    {label: 'PE', value: 'PE'},
    {label: 'WC', value: 'WC'},
]


const search = ref('')

const q = useQuasar();
const rows = ref([])

const loading = ref(false)
const pagination = ref({
    sortBy: 'desc',
    descending: false,
    page: 1,
    rowsPerPage: 5,
    rowsNumber: 0
})


const handleSearch = () => {
    onRequest({
        pagination: pagination.value,
        filter: filters.value,
        search: filters.value.search // ✅ FIXED
    });
};

function onRequest(prop) {
    const {page, rowsPerPage, sortBy, descending} = prop.pagination
    const filter = prop.filter
    const search = prop.search

    loading.value = true
    axios.get(route('employees.json-index-all', props.office), {
        params: {
            filter,
            page,
            rowsPerPage,
            search
        }
    })
        .then(res => {
            console.log(res.data);
            const {list} = res.data;
            const {data, per_page, current_page, total, to, from} = list;
            rows.value = data;
            pagination.value.page = current_page;
            pagination.value.rowsNumber = total;
            pagination.value.rowsPerPage = per_page;

        })
        .catch(err => {
            q.notify({type: 'negative', message: err?.response?.data?.message})
        })
        .finally(() => loading.value = false)
}


const exportData = () => {
    q.loading.show();

    axios.get(route('export.all', props.office), {responseType: 'blob'})
        .then((res) => {
            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = fileUrl;
            link.setAttribute('download', props.office.name.replace(/\s+/g, '_') + '_all_employees.xlsx');
            link.click();
        })
        .catch((err) => {
            q.notify({
                type: 'negative',
                message: err.response?.data?.message || 'Failed to download file',
            });
        })
        .finally(() => {
            q.loading.hide();
        });
};
onMounted(() => {
    onRequest({
        pagination: pagination.value,
        filter: filters.value,
        search: filters.value.search
    });
});

const goBack = () => {
    window.history.back()
}
</script>


<style scoped>

</style>



