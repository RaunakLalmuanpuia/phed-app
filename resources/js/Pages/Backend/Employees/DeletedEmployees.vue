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
                                <q-input
                                    dense
                                    outlined
                                    debounce="300"
                                    v-model="filters.search"
                                    placeholder="Search"
                                    class="col-12 col-sm-4"
                                    @update:model-value="handleSearch"
                                >
                                    <template #append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>
                            </div>
                        </q-card-section>

                    </q-card>
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

defineOptions({layout:BackendLayout})

const props=defineProps(['office','canCreate','canEdit','canDelete'])


const columns = [
    { name: 'employee', label: 'Employee', align: 'left', field: 'employee', sortable: true },
    { name: 'office', label: 'Office', align: 'left', field: 'office', sortable: true },
    { name: 'reason', label: 'Reason', align: 'left', field: 'reason', sortable: false },
    { name: 'year', label: 'Year', align: 'left', field: 'year', sortable: false },
    { name: 'actions', label: 'Actions', align: 'center' },
];

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

const goBack = () => {
    window.history.back()
}
</script>


<style scoped>

</style>


