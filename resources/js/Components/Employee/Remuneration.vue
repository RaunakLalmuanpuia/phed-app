<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";
import {useQuasar} from "quasar";
const $q = useQuasar();
const {pay_matrix,formatDate} = useUtils();
const props = defineProps({
    data: Object, // contains employee and remuneration_detail
    canCreateRemuneration: Boolean,
});

const showDialog = ref(false);
const isEdit = ref(false);
const editMode = ref(false);
// Form
const form = useForm({
    remuneration: "",
    pay_matrix:"",
    next_increment_date: ""
});



// When dialog opens in edit mode, populate form
const openDialog = (edit = false) => {
    isEdit.value = edit;
    if (edit && props.data.remuneration_detail) {
        form.remuneration = props.data.remuneration_detail.remuneration;
        form.pay_matrix = props.data.remuneration_detail.pay_matrix;
        form.next_increment_date = props.data.remuneration_detail.next_increment_date;
    } else {
        form.reset();
    }
    showDialog.value = true;
    editMode.value = false;
};

const enableEdit = () => {
    editMode.value = true; // enable editing
};


// Submit
const submitForm = () => {

    if (!form.remuneration) {
        $q.notify({
            type: 'negative',
            message: 'Remuneration amount is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (!form.next_increment_date) {
        $q.notify({
            type: 'negative',
            message: 'Next Increment Date is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (isEdit.value) {
        form.put(route("remuneration.update", props.data.id), {
            onSuccess: () => {
                showDialog.value = false;
            },
            onError: (errors) => {
                Object.values(errors).forEach((error) => {
                    $q.notify({
                        type: 'negative',
                        message: error,
                        position: 'bottom',
                    });
                });
            },
        });
    } else {
        form.post(route("remuneration.store", props.data.id), {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
            onError: (errors) => {
                Object.values(errors).forEach((error) => {
                    $q.notify({
                        type: 'negative',
                        message: error,
                        position: 'bottom',
                    });
                });
            },
        });
    }
};
</script>

<template>
    <q-card class="q-mt-md">
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Remuneration Detail</div>
            </div>

            <div class="flex q-gutter-sm">
                <q-btn
                    v-if="canCreateRemuneration"
                    @click="openDialog(!!data.remuneration_detail)"
                    color="btn-primary"
                    :icon="data.remuneration_detail ? 'edit' : 'add'"

                />
            </div>
        </div>

        <q-card-section>
            <div v-if="!data.remuneration_detail" class="text-grey-7 text-center q-pa-md italic">
                No remuneration data available.
            </div>

            <q-list v-else class="q-mt-sm">
                <q-item>
                    <q-item-section side class="subtitle">Remuneration:</q-item-section>
                    <q-item-section class="text-label">
                        ₹ {{ data.remuneration_detail.remuneration.toLocaleString() }}
                    </q-item-section>
                </q-item>

                <q-item>
                    <q-item-section side class="subtitle">Pay Matrix:</q-item-section>
                    <q-item-section class="text-label">
                        {{ data.remuneration_detail.pay_matrix ?? 'N/A' }}
                    </q-item-section>
                </q-item>

                <q-item>
                    <q-item-section side class="subtitle">Medical Allowance (%):</q-item-section>
                    <q-item-section class="text-label">
                        {{ data.remuneration_detail.medical_percentage }}
                    </q-item-section>
                </q-item>

                <q-item>
                    <q-item-section side class="subtitle">Medical Allowance:</q-item-section>
                    <q-item-section class="text-label">
                        ₹ {{ data.remuneration_detail.medical_amount.toLocaleString() }}
                    </q-item-section>
                </q-item>

                <q-item>
                    <q-item-section side class="subtitle">Total:</q-item-section>
                    <q-item-section class="text-label">
                        ₹ {{ data.remuneration_detail.total.toLocaleString() }}
                    </q-item-section>
                </q-item>

                <q-item>
                    <q-item-section side class="subtitle">Monthly Remuneration:</q-item-section>
                    <q-item-section class="text-label">
                        ₹ {{ data.remuneration_detail.round_total.toLocaleString() }}
                    </q-item-section>
                </q-item>

                <q-separator spaced />

                <q-item>
                    <q-item-section side class="subtitle">Date of next Increment:</q-item-section>
                    <q-item-section class="text-label">
                        {{ formatDate(data.remuneration_detail.next_increment_date) }}
                    </q-item-section>
                </q-item>
            </q-list>
        </q-card-section>
    </q-card>

    <!-- Dialog -->
    <q-dialog v-model="showDialog">
        <q-card style="min-width: 400px">
            <q-card-section>
                <div class="text-h6">{{ isEdit ? 'Edit' : 'Add' }} Remuneration</div>
            </q-card-section>

            <q-card-section class="q-gutter-md">
                <q-input
                    label="Remuneration"
                    type="number"
                    v-model="form.remuneration"
                    outlined
                    dense
                    :readonly="!editMode"
                >
                    <template v-slot:append>
                        <q-icon
                            name="edit"
                            class="cursor-pointer"
                            @click="enableEdit"
                        />
                    </template>
                </q-input>

                <q-select
                    label="Pay Matrix"
                    :options=pay_matrix
                    v-model="form.pay_matrix"
                    outlined
                    dense
                    :readonly="!editMode"
                />
                <q-input
                    label="Next Increment Date"
                    type="date"
                    v-model="form.next_increment_date"
                    outlined
                    dense
                    :readonly="!editMode"
                />
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat label="Cancel" v-close-popup />
                <q-btn color="primary" :label="isEdit ? 'Update' : 'Save'"  :disable="!editMode" @click="submitForm" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
