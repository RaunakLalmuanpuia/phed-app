<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Provisional Employees Summary : {{ officeName }}</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>

        <br/>

        <q-card flat>
            <q-card-section>
                <div class="text-h6 q-mb-sm">Provisional Employees Summary</div>
                <template v-if="props.rows.length > 0">
                    <q-table
                    :rows="rows"
                    :columns="columns"
                    row-key="designation"
                    flat
                    :pagination="{ rowsPerPage: 0 }"
                    hide-bottom
                >
                    <!-- Explicit body slots for each column -->
                    <template v-slot:body-cell-designation="props">
                        <q-td :props="props">{{ props.row.designation }}</q-td>
                    </template>

                    <template v-slot:body-cell-total="props">
                        <q-td :props="props" class="text-center">{{ props.row.total }}</q-td>
                    </template>

                    <!-- Bottom row -->
                    <template #bottom-row>
                        <q-tr class="bg-grey-3 text-bold">
                            <q-td>Total</q-td>
                            <q-td class="text-center">{{ totalSum }}</q-td>
                        </q-tr>
                    </template>
                </q-table>
                </template>
                <template v-else>
                    <div class="text-center q-pa-md text-grey">
                        No data found
                    </div>
                </template>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import { computed } from "vue";
import BackendLayout from "@/Layouts/BackendLayout.vue";

defineOptions({ layout: BackendLayout });

const props = defineProps({
    rows: Array,       // Backend JSON array
    officeName: String,
});

// Columns
const columns = [
    { name: 'designation', label: 'Designation', field: 'designation', align: 'left' },
    { name: 'total', label: 'Total', field: 'total', align: 'center' },
];

// Compute total sum of all totals
const totalSum = computed(() => {
    return props.rows.reduce((sum, r) => sum + (r.total || 0), 0);
});

const handleBack = () => window.history.back();
</script>

<style scoped>
</style>
