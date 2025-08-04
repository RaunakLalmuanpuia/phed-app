<template>
    <q-page class="container" padding>
        <q-card flat bordered>
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
                                        <span
                                            :class="[
                                          'text-caption',
                                          'text-weight-regular',
                                          card.trend > 0 ? 'text-green' : 'text-red'
                                        ]"
                                        >
                                        (
                                        <span class="text-weight-bold">
                                          {{ card.trend > 0 ? '+ ' : '- ' }}{{ Math.abs(card.trend) }}%
                                        </span>
                                        )
                                      </span>
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
                                    <q-icon :name="card.icon" :color="card.iconColor" size="20px" />
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
                            </div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section class="row items-center justify-between q-gutter-md">
                            <div class="row q-gutter-sm col-12 col-sm justify-end">
                                <q-input
                                    dense
                                    outlined
                                    debounce="300"
                                    v-model="search"
                                    placeholder="Search"
                                    class="col-12 col-sm-auto"
                                    @update:model-value="handleSearch"
                                >
                                    <template #append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>

                                <q-btn label="Export" icon="desktop_windows" color="grey-4" disable />
                                <q-btn
                                    label="Add New User"
                                    icon="add"
                                    color="primary"
                                    disable
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </q-card-section>

            <q-table
                :rows="rows"
                :columns="columns"
                row-key="id"
                flat
                class="q-pa-md"
                ref="tableRef"
                title="List of Employees"
                v-model:pagination="pagination"
                :loading="loading"
                binary-state-sort
                :rows-per-page-options="[5,10,15,30,50]"
                @request="onRequest"
            >
                <!-- User Cell -->
                <template v-slot:body-cell-employee="props">
                    <q-td :props="props">
                        <div class="flex items-center gap-3">
                            <q-avatar>
                                <img src="https://storage.googleapis.com/a1aa/image/ce4ce84e-7065-465f-056e-505939d6ea1d.jpg" />
<!--                                <img :src="props.row.avatar" />-->
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
                        <q-btn
                            dense
                            flat
                            round
                            color="primary"
                            icon="edit"
                            @click="editUser(props.row.id)"
                            aria-label="Edit user"
                        />
                        <q-btn
                            dense
                            flat
                            round
                            color="red"
                            icon="delete"
                            @click="deleteUser(props.row.id)"
                            aria-label="Delete user"
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
    { name: 'employment_type', label: 'Employment Type', align: 'left', field: 'employment_type', sortable: false },
    { name: 'designation', label: 'Designation', align: 'left', field: 'designation', sortable: false },
    { name: 'office', label: 'Office', align: 'left', field: 'office', sortable: true },
    { name: 'name_of_workplace', label: 'Workplace', align: 'left', field: 'name_of_workplace', sortable: false },
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
    type: null,
    office: null,
    skill: null,
})

const type = [
    { label: 'MR', value: 'MR' },
    { label: 'PE', value: 'PE' },
]

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
        search: search.value
    })
}
function onRequest (props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    const search = props.search

    loading.value = true
    axios.get(route('employees.json-index-all'),{
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
    // get initial data from server (1st page)
    // tableRef.value.requestServerInteraction()
    onRequest({pagination:pagination.value,
        filter:filters.value,
        search:search.value,
    })
})


</script>


<style scoped>

</style>


