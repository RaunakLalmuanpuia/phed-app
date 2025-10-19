<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Deleted Employee List</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br>
        <q-card flat >
            <q-card-section>
                <div class="q-pa-md">


                    <!-- Filter + Toolbar -->
                    <q-card flat bordered class="q-mt-md bg-white shadow-1">
                        <q-card-section>
                            <div class="text-subtitle1 text-weight-medium text-grey-8 q-mb-md">
                                Search Filter
                            </div>

                            <div class="row q-col-gutter-md">

                                <q-select
                                    label="Select Office"
                                    class="col-12 col-sm-4"
                                    v-model="filters.office"
                                    :options="office"
                                    option-label="name"
                                    option-value="id"
                                    emit-value
                                    map-options
                                    outlined
                                    dense
                                    clearable
                                    @update:model-value="handleSearch"
                                />

                                <q-select
                                    dense
                                    outlined
                                    debounce="300"
                                    v-model="filters.reason"
                                    :options="[
                                    'Expired',
                                    'Resigned',
                                    'Dismissed',
                                    'Regularised',
                                    'Others',
                                    'Overage (Retired)'
                                  ]"
                                    label="Select Reason"
                                    class="col-12 col-sm-4"
                                    emit-value
                                    map-options
                                    clearable
                                    @update:model-value="handleSearch"
                                >
                                </q-select>
                            </div>
                        </q-card-section>

                    </q-card>
                </div>
            </q-card-section>

            <q-card-section class="row items-center justify-between q-gutter-md">
                <div class="row q-gutter-sm col-12 col-sm justify-end">

                    <q-btn label="Export" icon="desktop_windows" color="primary" @click="exportData"/>

                </div>
            </q-card-section>

            <q-table
                flat
                ref="tableRef"
                title="List of Deleted Employees"
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
                                <q-icon v-else name="person" size="md" color="primary" />
                            </q-avatar>
                            <div>
                                <div class="text-body1">{{ props.row.name }}</div>
                                <div class="text-caption text-grey">{{ props.row.mobile }}</div>
                                <div class="text-caption text-grey">{{ props.row.date_of_birth }}</div>
                            </div>
                        </div>
                    </q-td>
                </template>

                <template v-slot:body-cell-type="props">
                    <q-td :props="props">
                        {{ getEmployeeType(props.row) }}
                    </q-td>
                </template>

                <!-- Office Cell -->
                <template v-slot:body-cell-office="props">
                    <q-td :props="props">
                        <q-chip color="primary" text-color="white" dense>{{ props.row.office?.name }}</q-chip>
                    </q-td>
                </template>

                <template v-slot:body-cell-reason="props">
                    <q-td :props="props">
                        {{ props.row.deletion_detail?.reason }}
                    </q-td>
                </template>

                <template v-slot:body-cell-year="props">
                    <q-td :props="props">
                        {{ props.row.deletion_detail?.year }}
                    </q-td>
                </template>

                <template v-slot:body-cell-supporting_document="props">
                    <q-td :props="props">
                        <span v-if="props.row.deletion_detail?.supporting_document">
                          <q-btn
                              dense
                              flat
                              round
                              color="primary"
                              icon="attachment"
                              :href="`/storage/${props.row.deletion_detail.supporting_document}`"
                              target="_blank"
                          />
                        </span>
                        <span v-else>N/A</span>
                    </q-td>
                </template>

                <!-- Actions Cell -->
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

                        <q-btn
                            dense
                            flat
                            round
                            color="negative"
                            icon="delete"
                            @click="trashEmployee(props.row)"
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

defineOptions({layout:BackendLayout})

const props=defineProps(['office','canCreate','canEdit','canDelete'])


const columns = [
    { name: 'employee', label: 'Employee', align: 'left', field: 'employee', sortable: true },
    { name: 'type', label: 'Employment Type', align: 'left', field: 'type', sortable: true },
    { name: 'designation', label: 'Designation', align: 'left', field: 'designation', sortable: true },
    { name: 'office', label: 'Office', align: 'left', field: 'office', sortable: true },
    { name: 'reason', label: 'Reason', align: 'left', field: 'reason', sortable: false },
    { name: 'year', label: 'Year', align: 'left', field: 'year', sortable: false },
    { name: 'supporting_document', label: 'Document', align: 'center', field: 'supporting_document', sortable: false },
    { name: 'actions', label: 'Actions', align: 'center' },
];


const filters = ref({
    office: null,
    reason : null,
})

const getEmployeeType = (row) => {

    // Workcharge → has designation AND date_of_retirement
    if (row.designation && row.date_of_retirement) {
        return 'Work Charge';
    }

    // Provisional → has designation but no retirement date
    if (row.designation && !row.date_of_retirement) {
        return 'Provisional';
    }

    // Muster Roll → no designation
    return 'Muster Roll';
};

const reasonOptions = [
    { label: 'Transfer', value: 'transfer' },
    { label: 'Retirement', value: 'retirement' },
    { label: 'Resignation', value: 'resignation' },
    { label: 'Other', value: 'other' }
];



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
    filters.value.search = search.value
    onRequest({
        pagination: pagination.value,
        filter: filters.value,
        search: filters.value.search
    })
}
function onRequest (props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    const search = props.search

    loading.value = true
    axios.get(route('employees.json-index-deleted'),{
        params:{
            filter,
            page,
            rowsPerPage,
            search
        }
    })
        .then(res=>{
            console.log(res.data);
            const {list} = res.data;
            const {data,per_page,current_page,total,to,from} = list;
            rows.value = data;
            pagination.value.page = current_page;
            pagination.value.rowsNumber = total;
            pagination.value.rowsPerPage = per_page;

        })
        .catch(err=>{
            q.notify({type:'negative',message:err?.response?.data?.message})
        })
        .finally(()=>loading.value=false)
}

onMounted(() => {
    onRequest({pagination:pagination.value,
        filter:filters.value,
        search: filters.value.search
    })
})


const trashEmployee = (employee) => {
    q.dialog({
        title: 'Confirm',
        message: `Are you sure you want to move <b>${employee.name}</b> to trash?`,
        html: true,
        cancel: true,
        persistent: true,
        ok: {
            label: 'Yes, Move to Trash',
            color: 'negative'
        },
    }).onOk(async () => {
        try {
            await axios.delete(route('employee.destroy', employee))

            q.notify({
                type: 'positive',
                message: `Employee ${employee.name} moved to trash`
            })

            // 🔄 Refresh table after delete
            onRequest({
                pagination: pagination.value,
                filter: filters.value,
                search: filters.value.search
            })
        } catch (error) {
            q.notify({
                type: 'negative',
                message: 'Failed to move employee to trash. Please try again.'
            })
        }
    })
}

const goBack = () => {
    window.history.back()
}


const exportData = () => {
    q.loading.show(); // Show loading indicator (assuming you're using Quasar's loading plugin)

    // Make a GET request to the URL with responseType as 'blob'
    axios.get(route('export.deleted'), { responseType: 'blob' })
        .then((res) => {
            // Create an object URL from the response data and trigger a download
            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = fileUrl;
            link.setAttribute('download', Date.now() + '.xlsx'); // Set a dynamic file name
            link.click();
        })
        .catch((err) => {
            // Show an error notification if something goes wrong
            q.notify({
                type: 'negative',
                message: err.response?.data?.message || 'Failed to download file',
            });
        })
        .finally(() => {
            q.loading.hide(); // Hide loading indicator
        });
};


</script>


<style scoped>

</style>


