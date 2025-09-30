<script setup>
import {computed, ref} from "vue";
import {useForm, router} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
const props = defineProps(['data']);

const $q = useQuasar();

// Inertia form
const form = useForm({
    reason: "",
    seniority_list: "",
    year: new Date().getFullYear(),
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

// Submit deletion request
function submitRequest() {

    if (!form.reason) {
        $q.notify({
            type: 'negative',
            message: 'Reason is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (!form.supporting_document) {
        $q.notify({
            type: 'negative',
            message: 'Supporting Document is required',
            position: 'bottom',
        });
        return; // stop submission
    }

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

    <q-card class="q-mt-md shadow-lg rounded-xl border border-gray-200">
        <!-- Title -->
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

        <q-separator/>

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

                </q-card>
            </div>
        </q-card-section>
    </q-card>

    <!-- Dialog -->
    <q-dialog v-model="showDialog">
        <q-card style="min-width: 400px">
            <q-card-section>
                <div class="text-h6">Submit Deletion Request</div>
            </q-card-section>

            <q-card-section>
                <q-select
                    v-model="form.reason"
                    label="Reason"
                    outlined
                    :options="[
                        'Expired',
                        'Resigned',
                        'Dismissed',
                        'Regularised',
                        'Others (Specify the reasons to Remarks)',
                        'Overage (Retired)'
                      ]"
                    :error="!!form.errors.reason"
                    :error-message="form.errors.reason"
                    :rules="[
                        val => !!val || 'Reason is required'
                     ]"
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
                    accept=".pdf,.jpg,.jpeg,.png"
                    :error="!!form.errors.supporting_document"
                    :error-message="form.errors.supporting_document"
                />
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat label="Cancel" color="grey" @click="showDialog = false"/>
                <q-btn
                    label="Submit"
                    color="negative"
                    @click="submitRequest"
                    :loading="form.processing"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>
