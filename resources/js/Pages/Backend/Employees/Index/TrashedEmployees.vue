<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Trashed Employee List</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br>
        <q-card flat >

            <q-table
                flat
                ref="tableRef"
                title="List of Trashed Employees"
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

                <!-- Actions Cell -->
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn
                            dense
                            flat
                            round
                            color="green"
                            icon="restore"
                            @click="restoreEmployee(props.row)"
                            aria-label="Restore Employee"
                        />
                        <q-btn
                            dense
                            flat
                            round
                            color="red"
                            icon="delete_forever"
                            @click="permanentlyDeleteEmployee(props.row)"
                            aria-label="Delete Permanently"
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
    { name: 'post_assigned', label: 'Post/Work Assigned', align: 'left', field: 'post_assigned', sortable: true },
    { name: 'office', label: 'Office', align: 'left', field: 'office', sortable: true },
    { name: 'reason', label: 'Reason', align: 'left', field: 'reason', sortable: false },
    { name: 'year', label: 'Year', align: 'left', field: 'year', sortable: false },
    { name: 'actions', label: 'Actions', align: 'center' },
];


const filters = ref({
    office: null,
    reason : null,
})


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
    axios.get(route('employees.json-index-trashed'),{
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

const restoreEmployee = (employee) => {
    q.dialog({
        title: 'Confirm Restore',
        message: `Are you sure you want to restore <b>${employee.name}</b>?`,
        html: true,
        cancel: true,
        persistent: true,
        ok: {
            label: 'Yes, Restore',
            color: 'positive'
        },
    }).onOk(async () => {
        try {
            await axios.put(route('employee.restore',employee))

            q.notify({
                type: 'positive',
                message: `Employee ${employee.name} restored successfully`
            })

            // Refresh table after restore
            onRequest({
                pagination: pagination.value,
                filter: filters.value,
                search: search.value
            })
        } catch (error) {
            q.notify({
                type: 'negative',
                message: error.response?.data?.message || 'Failed to restore employee'
            })
        }
    })
}


const permanentlyDeleteEmployee = (employee) => {
    q.dialog({
        title: 'Confirm Permanent Delete',
        message: `Are you sure you want to permanently delete <b>${employee.name}</b>? This action cannot be undone.`,
        html: true,
        cancel: true,
        persistent: true,
        ok: {
            label: 'Yes, Delete Permanently',
            color: 'negative'
        },
    }).onOk(async () => {
        try {
            await axios.delete(route('employees.forceDelete', employee.id))

            q.notify({
                type: 'positive',
                message: `Employee ${employee.name} permanently deleted`
            })

            // Refresh table after deletion
            onRequest({
                pagination: pagination.value,
                filter: filters.value,
                search: search.value
            })
        } catch (error) {
            q.notify({
                type: 'negative',
                message: error.response?.data?.message || 'Failed to delete employee permanently'
            })
        }
    })
}
const goBack = () => {
    window.history.back()
}


</script>


<style scoped>

</style>


