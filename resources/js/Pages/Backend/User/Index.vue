<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">User Accounts</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el label="User Accounts" :to="route('user.index')"/>
                </q-breadcrumbs>
            </div>

            <div class="flex q-gutter-sm">
                <q-btn @click="$inertia.get(route('user.create'))" color="btn-primary" label="New User" :disable="!canCreate" />
            </div>
        </div>
        <br/>
        <q-table
            flat
            ref="tableRef"
            title="List of User"
            :rows="rows"
            :columns="columns"
            row-key="id"
            v-model:pagination="pagination"
            :loading="loading"
            :filter="filter"
            binary-state-sort
            :rows-per-page-options="[1,7,15,30,50]"
            @request="onRequest"
        >
            <template v-slot:top-right>
                <q-input borderless dense debounce="800"
                         @update:model-value="handleSearch"
                         outlined
                         v-model="filter" placeholder="Search">
                    <template v-slot:append>
                        <q-icon name="search" />
                    </template>
                </q-input>
            </template>
            <template v-slot:body-cell-role="props">
                <q-td>
                    <q-chip v-for="item in props.row?.roles" :label="item.name" square/>
                </q-td>
            </template>
            <template v-slot:body-cell-office="props">
                <q-td>
                    <q-chip v-for="item in props.row?.offices" :label="item.name" square/>
                </q-td>
            </template>

            <template v-slot:body-cell-action="props">
                <q-td>
                    <q-btn round icon="more_vert">
                        <q-menu>
                            <q-item clickable @click="$inertia.get(route('user.show',props.row.id))">
                                <q-item-section>View Detail</q-item-section>
                            </q-item>
                            <q-item clickable @click="$inertia.get(route('user.edit',props.row.id))" :disable="!canEdit">
                                <q-item-section>Edit</q-item-section>
                            </q-item>
                            <q-item clickable @click="handleDelete(props.row)" :disable="!canDelete">
                                <q-item-section>Delete</q-item-section>
                            </q-item>
                        </q-menu>
                    </q-btn>


                </q-td>
            </template>

        </q-table>
    </q-page>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useQuasar} from "quasar";
import {router} from "@inertiajs/vue3";
defineOptions({layout:BackendLayout})
const props=defineProps(['canCreate','canEdit','canDelete'])
const columns = [
    { name: 'name', align: 'left', label: 'Name', field: 'name', sortable: false },
    { name: 'designation', align: 'left', label: 'Designation', field: 'designation', sortable: false },
    { name: 'mobile', align:'left', label: 'Mobile', field: 'mobile', sortable: true },
    { name: 'email',align:'left', label: 'Email', field: 'email', sortable: true },
    { name: 'office',align:'left', label: 'Office', field: 'office', sortable: true },
    { name: 'role', align:'left',label: 'Role', field: 'role', sortable: true },
    { name: 'action',align:'left', label: 'Action', field: 'action', sortable: true },
]

const q = useQuasar();
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    sortBy: 'desc',
    descending: false,
    page: 1,
    rowsPerPage: 15,
    rowsNumber: 0
})

const handleDelete=item=>{
    q.dialog({
        title:'Confirmation',
        message:'Do you want to delete?',
        ok:'Yes',
        cancel:'No'
    })
    .onOk(()=>{
        router.delete(route('user.destroy',item.id),{
            onStart:params => q.loading.show(),
            onFinish:params => q.loading.hide(),
            preserveState: false
        })
    })
}
const handleSearch=val=>{
    onRequest({pagination:pagination.value,filter:val})
}
function onRequest (props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter

    loading.value = true
    axios.get(route('user.json-index'),{
        params:{
            filter,
            page,
            rowsPerPage,
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
        filter:filter.value
    })
})


</script>
