<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Muster Roll Employees Summary : {{ officeName }}</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>

        <br/>

        <!-- Offices Table -->
        <q-card flat>
            <q-card-section>
                <div class="text-h6 q-mb-sm">Muster Roll Employees Summary</div>
                <template v-if="props.offices.length > 0">
                    <q-table
                        :rows="props.offices"
                        :columns="columns"
                        row-key="id"
                        flat
                        :filter="filter"
                        :pagination="{ rowsPerPage: 0 }"
                        hide-bottom
                    >
                        <!-- Row Total Column -->
                        <template v-slot:body-cell-total="props">
                            <q-td class="text-center">
                                {{ getRowTotal(props.row) }}
                            </q-td>
                        </template>

                        <template v-slot:no-data>
                            <div class="text-center q-pa-md text-grey">
                                No data found
                            </div>
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

        <!-- Schemes Table -->
        <q-card flat class="q-mt-md">
            <q-card-section>
                <div class="text-h6 q-mb-sm">Scheme-wise Muster Roll Employees Summary</div>
                <template v-if="props.schemes.length > 0">
                    <q-table
                        :rows="props.schemes"
                        :columns="schemeColumns"
                        row-key="id"
                        flat
                        :pagination="{ rowsPerPage: 0 }"
                        hide-bottom
                    >
                        <template v-slot:body-cell-total="props">
                            <q-td class="text-center">
                                {{ getRowTotal(props.row) }}
                            </q-td>
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
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { useQuasar } from "quasar";
import { ref } from "vue";

defineOptions({ layout: BackendLayout });

const props = defineProps({
    offices: Array,
    skills: Array,
    schemes: Array,
    officeName: String
});

const q = useQuasar();
const filter = ref(""); // Search box model

// Desired skill order
const skillOrder = ['Skilled-I', 'Skilled-II','Semi-Skilled','Unskilled'];

// Base columns for offices
const columns = [
    { name: 'name', label: 'Office Name', field: 'name', align: 'left', sortable: true }
];

// Base columns for schemes
const schemeColumns = [
    { name: 'name', label: 'Scheme Name', field: 'name', align: 'left', sortable: true }
];

// Sort skills according to desired order
const orderedSkills = [...props.skills].sort((a, b) => skillOrder.indexOf(a) - skillOrder.indexOf(b));

// Add dynamic skill columns
orderedSkills.forEach(skill => {
    columns.push({
        name: skill,
        label: skill || 'No Skill Mentioned',
        field: skill,
        align: 'center',
        sortable: true
    });
    schemeColumns.push({
        name: skill,
        label: skill || 'No Skill Mentioned',
        field: skill,
        align: 'center',
        sortable: true
    });
});

// Add total column
columns.push({ name: 'total', label: 'Total', field: 'total', align: 'center', sortable: false });
schemeColumns.push({ name: 'total', label: 'Total', field: 'total', align: 'center', sortable: false });

// Function to calculate row total dynamically
const getRowTotal = (row) => {
    return orderedSkills.reduce((sum, skill) => sum + (Number(row[skill]) || 0), 0);
};

const handleBack = () => window.history.back();
</script>
