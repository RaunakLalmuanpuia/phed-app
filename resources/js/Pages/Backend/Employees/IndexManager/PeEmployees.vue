<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Provisional Employee List: <q-chip square v-for="office in offices" :key="office.value" :label="office.label"/></div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>
            <div class="q-gutter-sm">
                <q-btn label="PE Summary" color="primary" @click="$inertia.get(route('summary.pe'))" />
            </div>
        </div>
        <br/>

        <q-card flat bordered>

            <q-card-section>
                <div class="text-subtitle1 text-weight-medium text-grey-8 q-mb-md">
                    Search Filter
                </div>

                <div class="row q-col-gutter-md">

                    <q-select
                        label="Select Designation"
                        class="col-12 col-sm-4"
                        v-model="filters.designation"
                        :options="designations"
                        emit-value
                        map-options
                        outlined
                        dense
                        clearable
                        @update:model-value="handleSearch"
                    />

                    <q-select
                        label="Select Education Qln."
                        class="col-12 col-sm-4"
                        v-model="filters.education_qln"
                        :options="educationQln"
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



                    <q-btn label="Export" icon="desktop_windows" color="primary"   @click="exportData" />
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
                </div>
            </q-card-section>

            <q-table
                flat
                ref="tableRef"
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
                                <div class="text-caption text-grey">{{ formatDate(props.row.date_of_birth) }}</div>
                            </div>
                        </div>
                    </q-td>
                </template>

                <!-- Remuneration Cell -->
                <template v-slot:body-cell-remuneration="props">
                    <q-td :props="props">
                        {{
                            props.row.remuneration_detail?.round_total != null
                                ? `₹ ${props.row.remuneration_detail.round_total.toLocaleString()}`
                                : '—'
                        }}
                    </q-td>
                </template>

                <template v-slot:body-cell-office="props">
                    <q-td :props="props">
                        <q-chip color="primary" text-color="white" dense>{{ props.row.office?.name }}</q-chip>
                    </q-td>
                </template>


                <!-- Date of Next Increment Cell -->
                <template v-slot:body-cell-date_of_next_increment="props">
                    <q-td :props="props">
                        {{
                            props.row.remuneration_detail?.next_increment_date
                                ? formatDate(props.row.remuneration_detail.next_increment_date)
                                : '—'
                        }}
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
import {onMounted, ref} from 'vue';

import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";
import useUtils from "@/Compositions/useUtils";

const {formatDate} = useUtils();
defineOptions({layout:BackendLayout})

const props = defineProps(['offices', 'designations', 'educationQln',]);



const columns = [
    { name: 'employee', label: 'Employee', align: 'left', field: 'employee', sortable: true },
    { name: 'designation', label: 'Designation', align: 'left', field: 'designation', sortable: false },
    { name: 'name_of_workplace', label: 'Workplace', align: 'left', field: 'name_of_workplace', sortable: false },
    { name: 'office', label: 'Office', align: 'left', field: 'office', sortable: false },
    { name: 'remuneration', label: 'Remuneration', align: 'left', field: 'remuneration', sortable: true },
    { name: 'date_of_next_increment', label: 'Next Increment', align: 'left', field: 'date_of_next_increment', sortable: true },
    { name: 'actions', label: 'Actions', align: 'center' },
];


const filters = ref({
    office: [],
    search: null,
    designation: null,
    education_qln: null,
});

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
    axios.get(route('employees.json-manager-pe'),{
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
const exportData = () => {
    q.loading.show();

    // Pick first office
    const firstOffice = props.offices?.length ? props.offices[0] : null;

    axios.get(
        route('export.pe', firstOffice?.value),   // <-- pass ID only
        { responseType: 'blob' }
    )
        .then((res) => {
            const fileUrl = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = fileUrl;
            link.setAttribute(
                'download',
                (firstOffice?.label || 'employees').replace(/\s+/g, '_') + '_pe_employees.xlsx'
            );
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


