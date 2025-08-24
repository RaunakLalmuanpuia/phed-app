<script setup>
import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { useQuasar } from "quasar";

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

// Dialog state
const showDialog = ref(false);
function parseReason(request) {
    try {
        return JSON.parse(request.reason);
    } catch {
        return {};
    }
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
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

            <div v-if="data.deletion_requests.length === 0" class="q-pa-md text-center  text-gray-500">
                No Deletion requests found.
            </div>

            <div class="row q-col-gutter-md q-gutter-sm flex-nowrap overflow-auto">
                <q-card
                    v-for="request in data.deletion_requests"
                    :key="request.id"
                    class="col-12 col-lg-12 q-mb-md shadow-2 rounded-lg flex-shrink-0"
                    style="min-width: 700px"
                    bordered
                >
                    <div class="row no-wrap items-center">

                        <!-- Left side: Request ID + Date -->
                        <div class="col-auto q-pa-md bg-grey-2 flex flex-col justify-center" style="width: 150px;">
                            <div class="text-h6 text-primary">#{{ request.id }}</div>
                            <div class="text-caption text-grey-7">
                                {{ formatDate(request.request_date) }}
                            </div>
                            <q-badge
                                :color="statusColor(request.approval_status)"
                                class="q-mt-sm"
                                align="middle"
                            >
                                {{ request.approval_status }}
                            </q-badge>
                        </div>

                        <!-- Middle: Reason Details -->
                        <div class="col q-pa-md">
                            <div class="text-subtitle1 font-medium">
                                Reason: <span class="text-dark">{{ parseReason(request).reason }}</span>
                            </div>
                            <div class="text-body2 text-grey-8 q-mt-xs">
                                Year: <span class="text-dark">{{ parseReason(request).year }}</span>
                            </div>
                            <div class="text-body2 text-grey-8">
                                Remark: <span class="text-dark">{{ parseReason(request).remark }}</span>
                            </div>
                            <div class="text-body2 text-grey-8">
                                Seniority List: <span class="text-dark">{{ parseReason(request).seniority_list }}</span>
                            </div>

                            <div v-if="parseReason(request).supporting_document" class="q-mt-sm">
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



                    </div>
                </q-card>
            </div>
        </q-card-section>
    </q-card>
</template>
