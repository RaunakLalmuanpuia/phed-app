<script setup>
import {computed} from "vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";


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
            <div class="stitle">Deletion Request</div>
        </q-card-section>

        <q-separator />

        <q-card-section>
            <div class="q-pa-md">
                <div v-if="data.deletion_requests.length === 0" class="text-center text-grey-7">
                    No deletion requests.
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

                            <!-- Right side: Action Buttons -->
                            <div class="col-auto q-pa-md flex flex-col items-end justify-center" style="min-width: 120px;">
                                <q-btn
                                    v-if="request.approval_status === 'pending'"
                                    color="positive"
                                    label="Approve"
                                    class="q-mb-sm"
                                    style="width: 100px"
                                    @click="approveRequest(request.id)"
                                />
                                <q-btn
                                    v-if="request.approval_status === 'pending'"
                                    color="negative"
                                    label="Reject"
                                    style="width: 100px"
                                    @click="rejectRequest(request.id)"
                                />
                            </div>

                        </div>
                    </q-card>
                </div>
            </div>
        </q-card-section>
    </q-card>
</template>



<style scoped>

</style>
