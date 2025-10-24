<script setup>
import {computed} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
const $q = useQuasar();
const page = usePage();
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



const approveRequest = (id) => {
    form.post(route('deletion.approve', id), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            form.reset();

            const msg = page.props.flash.success

            if (msg === 'Employee moved to Trash.') {
                $q.notify({
                    type: 'negative',
                    message: 'Employee Moved to trash.'
                })
            } else {
                $q.notify({
                    type: 'positive',
                    message: 'Employee Deleted successfully.'
                })
            }
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
    <q-card class="q-mt-md shadow-lg rounded-xl border border-gray-200">
        <!-- Title -->
        <q-card-section>
            <div class="stitle text-lg font-bold">Deletion Requests</div>
        </q-card-section>

        <q-separator />

        <q-card-section>
            <!-- Empty State -->
            <div
                v-if="data.deletion_requests.length === 0"
                class="text-grey-7 text-center q-pa-md italic"
            >
                No deletion requests found.
            </div>

            <!-- Requests List -->
            <div v-else class="flex flex-col gap-6">
                <q-card
                    v-for="request in data.deletion_requests"
                    :key="request.id"
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
                                      'text-yellow-600': request.approval_status === 'pending',
                                      'text-green-600': request.approval_status === 'approved',
                                      'text-red-600': request.approval_status === 'rejected'
                                    }"
                                                    >
                                    {{ request.approval_status }}
                                  </span>
                            </div>
                            <div class="text-gray-600">
                                <b>Requested On:</b> {{ formatDate(request.request_date) }}
                            </div>
                        </div>

                        <!-- Reason Details -->
                        <div>
                            <b class="block mb-3 text-gray-700">Reason Details</b>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-lg"
                            >
                                <div>
                                    <div class="text-gray-500">
                                        Reason:
                                        <span class="text-gray-800 font-bold">
                                         {{ parseReason(request).reason }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-gray-500">
                                        Year:
                                        <span class="text-gray-800 font-bold">
                                         {{ parseReason(request).year }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-gray-500">
                                        Remark:
                                        <span class="text-gray-800 font-bold">
                                        {{ parseReason(request).remark }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-gray-500">
                                        Seniority List:
                                        <span class="text-gray-800 font-bold">
                                        {{ parseReason(request).seniority_list }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="parseReason(request).supporting_document">
                                    <div class="text-gray-500">
                                        Supporting Document:
                                        <q-btn
                                            dense
                                            flat
                                            color="primary"
                                            icon="visibility"
                                            :href="`/storage/${parseReason(request).supporting_document}`"
                                            target="_blank"
                                        />
                                        <q-btn
                                            dense
                                            flat
                                            color="primary"
                                            icon="download"
                                            :href="`/storage/${parseReason(request).supporting_document}`"
                                            target="_blank"
                                            download
                                        />

                                    </div>
                                </div>

                            </div>

                        </div>
                    </q-card-section>

                    <!-- Actions (only pending) -->
                    <q-card-actions
                        align="right"
                        v-if="request.approval_status === 'pending'"
                        class="px-4 pb-4"
                    >
                        <q-btn
                            label="Approve"
                            color="positive"
                            unelevated
                            class="rounded-lg"
                            @click="approveRequest(request.id)"
                        />
                        <q-btn
                            label="Reject"
                            color="negative"
                            flat
                            class="rounded-lg"
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
