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
    requested_office_id: '',
});

const latestRequestPending = computed(() =>
    props.data.transfer_requests?.slice(-1)[0]?.approval_status === 'pending'
);

// Submit request
const submit = () => {
    form.post(route('transfer.request', props.data), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            showDialog.value = false; // close dialog
            form.reset();
            $q.notify({
                type: 'positive',
                message: 'Transfer Requested',
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
            <div class="stitle text-lg font-bold">Request Transfer</div>

            <!-- Open Dialog Button -->
            <div>
                <q-btn
                    label="Request Transfer"
                    color="primary"
                    :disable="latestRequestPending"
                    @click="showDialog = true"
                />
            </div>
        </q-card-section>


        <q-separator />

        <q-card-section>
            <div class="stitle text-md font-semibold q-mb-sm">Previous Requests</div>

            <q-table
                :rows="data.transfer_requests"
                :columns="[
                  { name: 'id', label: 'ID', field: 'id', align: 'left' },
                  { name: 'current_office', label: 'Current Office', field: row => row.current_office?.name },
                  { name: 'requested_office', label: 'Requested Office', field: row => row.requested_office?.name },
                  { name: 'status', label: 'Status', field: 'approval_status' },
                  { name: 'date', label: 'Request Date', field: row => formatDate(row.request_date) }
                ]"
                row-key="id"
                flat
                bordered
            />
        </q-card-section>
    </q-card>

    <!-- Dialog -->
    <q-dialog v-model="showDialog" persistent>
        <q-card style="min-width: 400px">
            <q-card-section>
                <div class="text-h6">Select Requested Office</div>
            </q-card-section>

            <q-card-section>
                <q-select
                    v-model="form.requested_office_id"
                    :options="office"
                    option-value="id"
                    option-label="name"
                    label="Office"
                    dense
                    outlined
                />
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat label="Cancel" color="negative" v-close-popup />
                <q-btn flat label="Submit" color="primary" @click="submit" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<style scoped>
.stitle {
    font-weight: 600;
}
</style>
