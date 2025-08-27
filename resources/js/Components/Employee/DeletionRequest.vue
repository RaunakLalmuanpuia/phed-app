<script setup>
import {computed} from "vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
const $q = useQuasar();

const form = useForm({
});


const props = defineProps(['data']);

function parseReason(request) {
    try {
        return JSON.parse(request.reason);
    } catch {
        return {};
    }
}



function statusColor(status) {
    switch (status) {
        case "approved":
            return "green";
        case "rejected":
            return "red";
        default:
            return "orange"; // pending
    }
}

const approveRequest = (id) => {
    form.post(route('deletion.approve', id), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            form.reset();
            $q.notify({
                type: 'success',
                message: 'Deletion Approved',
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
    form.post(route('deletion.reject', id), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            form.reset();
            $q.notify({
                type: 'negative',
                message: 'Deletion Rejected',
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
        <q-card-section>
            <div class="stitle text-lg font-bold">Deletion Requests</div>
        </q-card-section>

        <q-separator />

        <q-card-section>
            <!-- Empty state -->
            <div v-if="data.deletion_requests.length === 0" class="text-gray-500 text-center py-6">
                No deletion requests.
            </div>

            <!-- Requests flex layout -->
            <div v-else class="flex flex-col gap-4">
                <q-card
                    v-for="request in data.deletion_requests"
                    :key="request.id"
                    class="shadow-md rounded-xl border border-gray-200 hover:shadow-lg transition"
                >
                    <q-card-section>
                        <!-- Top meta info -->
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <div>
                                <b class="text-label">Status:</b>
                                <span
                                    :class="{
                    'text-yellow-600': request.approval_status === 'pending',
                    'text-green-600': request.approval_status === 'approved',
                    'text-red-600': request.approval_status === 'rejected'
                  }"
                                >
                  {{ request.approval_status }}
                </span>
                            </div>
                            <div>
                                <b class="text-label">Requested On:</b>
                                {{ formatDate(request.request_date) }}
                            </div>

                        </div>

                        <!-- Reason details stacked horizontally -->
                        <div>
                            <b class="block mb-2 text-label">Reason Details:</b>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 p-3 rounded-md">
                                <q-item>
                                    <q-item-section side class="subtitle">Reason:</q-item-section>
                                    <q-item-section class="text-label">{{ parseReason(request).reason }}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="subtitle">Year:</q-item-section>
                                    <q-item-section class="text-label">{{ parseReason(request).year }}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="subtitle">Remark:</q-item-section>
                                    <q-item-section class="text-label">{{ parseReason(request).remark }}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="subtitle">Seniority List:</q-item-section>
                                    <q-item-section class="text-label">{{ parseReason(request).seniority_list }}</q-item-section>
                                </q-item>
                            </div>

                            <!-- Supporting Document -->
                            <div v-if="parseReason(request).supporting_document" class="mt-3">
                                <q-btn
                                    dense
                                    flat
                                    color="primary"
                                    icon="attach_file"
                                    :href="`/${parseReason(request).supporting_document}`"
                                    target="_blank"
                                    label="View Document"
                                />
                            </div>
                        </div>
                    </q-card-section>

                    <!-- Show buttons only when status is pending -->
                    <q-card-actions align="right" v-if="request.approval_status === 'pending'">
                        <q-btn
                            label="Approve"
                            color="positive"
                            @click="approveRequest(request.id)"
                        />
                        <q-btn
                            label="Reject"
                            color="negative"
                            @click="rejectRequest(request.id)"
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </q-card-section>
    </q-card>
</template>




<style scoped>

</style>
