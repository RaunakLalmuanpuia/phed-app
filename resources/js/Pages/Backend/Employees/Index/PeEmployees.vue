<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Provisional Employee List: {{office.name}}</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el label="All Employees" :to="route('mis.import')"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>

        <q-card flat bordered>
            <q-card-section class="row items-center justify-between q-gutter-md">
                <div class="row q-gutter-sm col-12 col-sm justify-end">
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
                            <q-icon name="search" />
                        </template>
                    </q-input>

<!--                                                    <q-btn label="Export" icon="desktop_windows" color="grey-4" disable />-->
                                    <q-btn
                                        label="Add New PE Employee"
                                        icon="add"
                                        color="primary"
                                        @click="$inertia.get(route('employee.create'))"
                                    />
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

                <!-- Role Cell -->
                <template v-slot:body-cell-office="props">
                    <q-td :props="props">
                        <q-chip color="primary" text-color="white" dense>{{ props.row.office?.name }}</q-chip>
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
<!--                        <q-btn-->
<!--                            dense-->
<!--                            flat-->
<!--                            round-->
<!--                            color="primary"-->
<!--                            icon="edit"-->
<!--                            @click="$inertia.get(route('employee.edit',props.row.id))"-->
<!--                            aria-label="Edit user"-->
<!--                        />-->
<!--                        <q-btn-->
<!--                            dense-->
<!--                            flat-->
<!--                            round-->
<!--                            color="red"-->
<!--                            icon="delete"-->
<!--                            @click="deleteUser(props.row.id)"-->
<!--                            aria-label="Delete user"-->
<!--                        />-->
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
    { name: 'designation', label: 'Designation', align: 'left', field: 'designation', sortable: false },
    { name: 'name_of_workplace', label: 'Workplace', align: 'left', field: 'name_of_workplace', sortable: false },
    { name: 'office', label: 'Office', align: 'left', field: 'office', sortable: true },
    { name: 'actions', label: 'Actions', align: 'center' },
];

const cards = [
    {
        title: 'Total',
        value: '237',
        trend: 42,
        icon: 'person_search',
        iconColor: 'light-blue-5',
        bgColor: '#d6f3ff',
    },
    {
        title: 'Muster Roll',
        value: '21,459',
        trend: 29,
        icon: 'person',
        iconColor: 'indigo-6',
        bgColor: '#d7d9ff',
    },
    {
        title: 'Provisional',
        value: '2,137',
        trend: 23,
        icon: 'person_add',
        iconColor: 'red-6',
        bgColor: '#ffe1e1',
    },
    {
        title: 'Skilled',
        value: '9,632',
        trend: -19,
        icon: 'how_to_reg',
        iconColor: 'orange-5',
        bgColor: '#ffe9d6',
    },

]

const filters = ref({
    office: null,
    skill: null,
    search:null,
})



const skills = [
    { label: 'Skilled', value: 'skilled' },
    { label: 'Unskilled', value: 'unskilled' },
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
        search: filters.value.search
    })
}
function onRequest (prop) {
    const { page, rowsPerPage, sortBy, descending } = prop.pagination
    const filter = prop.filter
    const search = prop.search

    loading.value = true
    axios.get(route('employees.json-index-pe', props.office),{
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


</script>


<style scoped>

</style>


