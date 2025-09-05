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
    <q-card class="q-mt-md shadow-lg rounded-xl border border-gray-200">
        <!-- Title -->
        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Transfer Request</div>
        </q-card-section>

        <q-separator />

        <q-card-section>
            <!-- Empty State -->
            <div
                v-if="data.transfer_requests.length === 0"
                class="text-grey-7 text-center q-pa-md italic"
            >
                No transfer requests found.
            </div>

            <!-- Requests List -->
            <div v-else class="flex flex-col gap-6">
                <q-card
                    v-for="req in data.transfer_requests"
                    :key="req.id"
                    class="rounded-xl border border-gray-100 hover:shadow-md transition duration-200"
                >
                    <q-card-section>
                        <!-- Header Info -->
                        <div class="flex flex-wrap justify-between items-center mb-4">
                            <div>
                                <b class="text-gray-600">Status:</b>
                                <span
                                    class="ml-1 font-medium capitalize"
                                    :class="{
                                  'text-yellow-600': req.approval_status === 'pending',
                                  'text-green-600': req.approval_status === 'approved',
                                  'text-red-600': req.approval_status === 'rejected'
                                }"
                                                >
                                {{ req.approval_status }}
                              </span>
                            </div>
                            <div class="text-gray-600">
                                <b>Requested On:</b> {{ formatDate(req.request_date) }}
                            </div>
                        </div>

                        <!-- Transfer Details -->
                        <div>
                            <b class="block mb-3 text-gray-700">Transfer Details</b>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4  p-4 "
                            >
                                <div>
                                    <div class="text-gray-500">
                                        Current Office:
                                        <span class="text-gray-800 font-bold">
                                        {{ req.current_office?.name || '—' }}
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <div class="text-gray-500">
                                        Requested Office:
                                        <span class="text-gray-800 font-bold">
                                       {{ req.requested_office?.name || '—' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-500">
                                        Transfer Order:
                                        <q-btn
                                            dense
                                            flat
                                            color="primary"
                                            icon="visibility"
                                            :href="`/storage/${req.supporting_document}`"
                                            target="_blank"
                                        />
                                        <q-btn
                                            dense
                                            flat
                                            color="primary"
                                            icon="download"
                                            :href="`/storage/${req.supporting_document}`"
                                            target="_blank"
                                            download
                                        />

                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-card-section>

                    <!-- Actions (only when pending) -->
                    <q-card-actions
                        align="right"
                        v-if="req.approval_status === 'pending'"
                        class="px-4 pb-4"
                    >
                        <q-btn
                            label="Approve"
                            color="positive"
                            unelevated
                            class="rounded-lg"
                            @click="approveRequest(req.id)"
                        />
                        <q-btn
                            label="Reject"
                            color="negative"
                            flat
                            class="rounded-lg"
                            @click="rejectRequest(req.id)"
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </q-card-section>
    </q-card>


</template>

<style scoped>
.stitle {
    font-weight: 600;
}
</style>
