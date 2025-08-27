<script setup>
import { ref,computed } from "vue";
import { useQuasar } from "quasar";
import { useForm } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
const props = defineProps(['data', 'office']);
const $q = useQuasar();

const showDialog = ref(false);

const form = useForm({
});


// Approve request
const approveRequest = (id) => {
    form.post(route('transfer.approve', id), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            showDialog.value = false; // close dialog
            form.reset();
            $q.notify({
                type: 'success',
                message: 'Transfer Approved',
                position: 'bottom',
            });
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
};

// Approve request
const rejectRequest = (id) => {
    form.post(route('transfer.reject', id), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            showDialog.value = false; // close dialog
            form.reset();
            $q.notify({
                type: 'negative',
                message: 'Transfer Rejected',
                position: 'bottom',
            });
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
};
</script>

<template>
    <q-card class="q-mt-md">
        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Transfer Request</div>

            <!-- Open Dialog Button -->
        </q-card-section>


        <q-separator />

        <q-card-section>
            <div class="stitle text-md font-semibold q-mb-sm">Tranfer Requests</div>

            <q-table
                :rows="data.transfer_requests"
                :columns="[
                  { name: 'current_office', label: 'Current Office', field: row => row.current_office?.name },
                  { name: 'requested_office', label: 'Requested Office', field: row => row.requested_office?.name },
                  { name: 'status', label: 'Status', field: 'approval_status' },
                  { name: 'date', label: 'Request Date', field: row => formatDate(row.request_date) },
                  { name: 'actions', label: 'Actions', field: 'id', align: 'center' }
                ]"
                row-key="id"
                flat
                bordered
            >
                <!-- Action Column -->
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn-dropdown
                            v-if="props.row.approval_status === 'pending'"
                            flat
                            dense
                            color="primary"
                            label="Actions"
                            size="sm"
                        >
                            <q-list>
                                <q-item clickable v-ripple @click="approveRequest(props.row.id)">
                                    <q-item-section avatar>
                                        <q-icon name="check" color="positive" />
                                    </q-item-section>
                                    <q-item-section>Approve</q-item-section>
                                </q-item>

                                <q-item clickable v-ripple @click="rejectRequest(props.row.id)">
                                    <q-item-section avatar>
                                        <q-icon name="close" color="negative" />
                                    </q-item-section>
                                    <q-item-section>Reject</q-item-section>
                                </q-item>
                            </q-list>
                        </q-btn-dropdown>
                    </q-td>
                </template>
            </q-table>
        </q-card-section>
    </q-card>

</template>

<style scoped>
.stitle {
    font-weight: 600;
}
</style>
