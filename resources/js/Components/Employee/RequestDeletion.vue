<script setup>
import {computed, ref} from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { useQuasar } from "quasar";
import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
const props = defineProps(['data']);

const $q = useQuasar();

// Inertia form
const form = useForm({
    reason: "",
    seniority_list: "",
    year: "",
    remark: "",
    supporting_document: null,
});

const latestRequestPending = computed(() =>
    props.data.deletion_requests?.slice(-1)[0]?.approval_status === 'pending'
);

// Dialog state
const showDialog = ref(false);
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
// Submit deletion request
function submitRequest() {
    form.post(route("deletion.request", props.data), {
        preserveScroll: true,
        onSuccess: () => {
            $q.notify({
                type: "positive",
                message: "Deletion request submitted successfully",
            });
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
</script>

<template>
    <q-card class="q-mt-md">


        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Request Deletion</div>

            <!-- Open Dialog Button -->
            <div>
                <q-btn
                    label="Request Deletion"
                    color="negative"
                    v-if="data.employment_type !== 'Deleted'"
                    :disable="latestRequestPending"
                    @click="showDialog = true"
                />
            </div>
        </q-card-section>


        <q-separator />

        <!-- Dialog -->
        <q-dialog v-model="showDialog">
            <q-card style="min-width: 400px">
                <q-card-section>
                    <div class="text-h6">Submit Deletion Request</div>
                </q-card-section>

                <q-card-section>
                    <q-input
                        v-model="form.reason"
                        label="Reason"
                        type="textarea"
                        outlined
                        :error="!!form.errors.reason"
                        :error-message="form.errors.reason"
                    />
                    <q-input
                        v-model="form.seniority_list"
                        label="Seniority List"
                        outlined
                        class="q-mt-sm"
                    />
                    <q-input
                        v-model="form.year"
                        label="Year"
                        type="number"
                        outlined
                        class="q-mt-sm"
                    />
                    <q-input
                        v-model="form.remark"
                        label="Remark"
                        type="textarea"
                        outlined
                        class="q-mt-sm"
                    />

                    <q-file
                        v-model="form.supporting_document"
                        label="Supporting Document"
                        outlined
                        class="q-mt-sm"
                        :error="!!form.errors.supporting_document"
                        :error-message="form.errors.supporting_document"
                    />
                </q-card-section>

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" color="grey" @click="showDialog = false" />
                    <q-btn
                        label="Submit"
                        color="negative"
                        @click="submitRequest"
                        :loading="form.processing"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
        <!-- History -->
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

                </q-card>
            </div>
        </q-card-section>
    </q-card>
</template>
