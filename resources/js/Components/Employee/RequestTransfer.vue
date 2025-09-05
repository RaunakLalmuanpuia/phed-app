<script setup>
import {ref, computed, watch} from "vue";
import { useQuasar } from "quasar";
import { useForm } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
const props = defineProps(['data', 'office']);
const $q = useQuasar();

const showDialog = ref(false);

const form = useForm({
    requested_office_id: '',
    supporting_document: null,
});
watch(showDialog, (val) => {
    if (!val) {
        form.reset(); // reset values
        form.clearErrors(); // optional: clear validation errors too
    }
});

const latestRequestPending = computed(() =>
    props.data.transfer_requests?.slice(-1)[0]?.approval_status === 'pending'
);

// Submit request
const submit = () => {

    if (!form.requested_office_id) {
        $q.notify({
            type: 'negative',
            message: 'Office is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    if (!form.supporting_document) {
        $q.notify({
            type: 'negative',
            message: 'Transfer Order is required',
            position: 'bottom',
        });
        return; // stop submission
    }

    form.post(route('transfer.request', props.data), {
        onStart: () => {
            $q.loading.show();
        },
        onSuccess: () => {
            $q.loading.hide();
            showDialog.value = false; // close dialog
            form.reset();
            $q.notify({
                type: 'positive',
                message: 'Transfer Requested',
                position: 'bottom',
            });
        },
        onFinish: () => {
            $q.loading.hide();
            showDialog.value = false; // close dialog
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
};
</script>

<template>
    <q-card class="q-mt-md shadow-lg rounded-xl border border-gray-200">
        <!-- Title + Action Button -->
        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Request Transfer</div>
            <q-btn
                label="Request Transfer"
                color="primary"
                :disable="latestRequestPending"
                @click="showDialog = true"
            />
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

                        <!-- Office Info -->
                        <div>
                            <b class="block mb-3 text-gray-700">Transfer Details:</b>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4  p-  rounded-lg">
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
                </q-card>
            </div>
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
                    :error="!!form.errors.requested_office_id"
                    :error-message="form.errors.requested_office_id"
                    :rules="[
                         val=>!!val || 'Office is required'
                     ]"
                />
                <q-file
                    v-model="form.supporting_document"
                    label="Transfer Order"
                    outlined
                    class="q-mt-md"
                    :error="!!form.errors.supporting_document"
                    :error-message="form.errors.supporting_document"
                    :rules="[
                         val=>!!val || 'Transfer Order is required'
                     ]"
                    accept=".pdf,.jpg,.jpeg,.png"
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
