

<template>
    <q-card class="q-mt-md">
        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Uploaded Documents</div>
            <q-btn label="Request Document Update" color="primary"  :disable="latestRequestPending" v-if="canRequestDocumentEdit" @click="showDialog = true" />
        </q-card-section>


        <q-card-section>
            <div v-if="filteredDocuments.length === 0" class="text-grey-7 text-center q-pa-md italic">
                No documents uploaded
            </div>

            <div v-if="filteredDocuments.length > 0">

                <div class="row q-col-gutter-md q-mt-sm">
                    <q-input
                        v-model="search"
                        dense
                        outlined
                        label="Search by document type..."
                        class="col-12 col-sm-6"
                    />
                </div>

                <q-markup-table flat dense class="q-ma-sm">
                    <thead>
                    <tr class="text-left">

                        <th>Type</th>
                        <th>Mime Type</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="doc in filteredDocuments"
                        :key="doc.id"
                        class="text-left"
                    >
                        <td class="q-pa-sm">{{ doc.type?.name || '—' }}</td>
                        <td class="q-pa-sm">{{ doc.mime }}</td>
                        <td class="q-pa-sm">{{ formatDate(doc.upload_date) }}</td>
                        <td class="q-pa-sm">
                            <q-btn
                                icon="visibility"
                                flat
                                dense
                                color="primary"
                                round
                                :href="`/storage/${doc.path}`"
                                target="_blank"
                                title="View Document"
                            />
                            <q-btn
                                icon="download"
                                flat
                                dense
                                round
                                color="primary"
                                :href="`/storage/${doc.path}`"
                                download
                                title="Download Document"
                            />
                            <q-btn
                                v-if="canDeleteDocument"
                                icon="delete"
                                flat
                                dense
                                round
                                color="red"
                                @click="deleteDocument(doc)"
                                download
                                title="Download Document"
                            />
                            <q-btn
                                v-if="canRequestDocumentDelete"
                                icon="delete_outline"
                                :disable="latestDocumentDeleteRequestPending"
                                flat
                                dense
                                round
                                color="negative"
                                title="Request Delete"
                                @click="requestDeleteDocument(doc)"
                            />
                        </td>
                    </tr>
                    <tr v-if="filteredDocuments.length === 0">
                        <td colspan="5" class="text-center text-grey">No documents found</td>
                    </tr>
                    </tbody>
                </q-markup-table>
            </div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
            <div class="stitle text-lg font-bold q-mb-md">Document Update Request</div>
            <!-- Empty State -->
            <div
                v-if="data.document_request.length === 0"
                class="text-grey-7 text-center q-pa-md italic"
            >
                No Document update request found.
            </div>

            <!-- Requests List -->
            <div v-else class="flex flex-col gap-6">
                <q-card
                    v-for="req in data.document_request"
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

                        <!-- Document Details -->
                        <div>
                            <b class="block mb-3 text-gray-700">Documents</b>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4  p-4 "
                            >


                                <div v-for="file in req.files" :key="file.id" class="mb-2">
                                    <div class="text-gray-500">
                                        {{ file.document_type.name }}:
                                        <q-btn
                                            dense
                                            flat
                                            color="primary"
                                            icon="visibility"
                                            :href="`/storage/${file.path}`"
                                            target="_blank"
                                        />
                                        <q-btn
                                            dense
                                            flat
                                            color="primary"
                                            icon="download"
                                            :href="`/storage/${file.path}`"
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
                        v-if="req.approval_status === 'pending' && canApproveDocumentEdit"
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


        <q-card-section>
            <div class="stitle text-lg font-bold q-mb-md">Document Delete Request</div>
            <!-- Empty State -->
            <div
                v-if="data.document_delete_request.length === 0"
                class="text-grey-7 text-center q-pa-md italic"
            >
                No delete requests found.
            </div>

            <!-- Requests List -->
            <div v-else class="flex flex-col gap-6">
                <q-card
                    v-for="req in data.document_delete_request"
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

                        <!-- Document Details in one line -->
                        <div class="text-gray-700">
                            <b>Document:</b> {{ req.document_type_name || 'N/A' }}
                        </div>
                    </q-card-section>

                    <!-- Actions (only when pending) -->
                    <q-card-actions
                        align="right"
                        v-if="req.approval_status === 'pending' && canApproveDocumentDelete"
                        class="px-4 pb-4"
                    >
                        <q-btn
                            label="Approve"
                            color="positive"
                            unelevated
                            class="rounded-lg"
                            @click="approveDeleteRequest(req.id)"
                        />
                        <q-btn
                            label="Reject"
                            color="negative"
                            flat
                            class="rounded-lg"
                            @click="rejectDeleteRequest(req.id)"
                        />
                    </q-card-actions>
                </q-card>
            </div>

        </q-card-section>

    </q-card>

    <q-dialog v-model="showDialog" persistent>
        <q-card
            class="q-pa-md"
            style="width: 90vw; max-width: 600px"
        >
            <!-- Header -->
            <q-card-section>
                <div class="text-h6 text-weight-bold text-primary">
                    Request Document Update
                </div>
            </q-card-section>

            <!-- File Inputs -->
            <q-card-section>
                <div
                    v-for="dt in filteredDocumentTypes"
                    :key="dt.id"
                    class="column"
                >
                    <div class="text-subtitle2 text-weight-medium q-mb-xs">{{ dt.name }}</div>
                    <q-file
                        v-model="form.files[dt.id]"
                        filled
                        dense
                        label="Choose file"
                        accept=".pdf,.jpg,.jpeg,.png"
                    />
                </div>
            </q-card-section>

            <!-- Actions -->
            <q-card-actions align="right" class="q-pt-none">
                <q-btn flat label="Cancel" color="grey" v-close-popup />
                <q-btn
                    label="Submit Request"
                    color="primary"
                    :loading="form.processing"
                    :disable="isSubmitDisabled"
                    @click="submit"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script setup>
import {computed, ref, watch} from "vue";
import { useForm } from "@inertiajs/vue3";
import {useQuasar} from "quasar";
const props=defineProps(['data', 'office','documentTypes','canDeleteDocument','canApproveDocumentEdit','canRequestDocumentEdit', 'canRequestDocumentDelete','canApproveDocumentDelete']);
const $q = useQuasar();
const search = ref('')
const showDialog = ref(false);

const filteredDocuments = computed(() => {
    if (!props.data?.documents) return [];

    return props.data.documents.filter((doc) => {
        const searchText = search.value.toLowerCase();
        return (
            doc.name?.toLowerCase().includes(searchText) ||
            doc.type?.name?.toLowerCase().includes(searchText)
        );
    });
});

const filteredDocumentTypes = computed(() => {
    return props.documentTypes.filter(
        dt => !dt.name.toLowerCase().includes("mr sheet")
    );
});



// Computed property to check if at least one file is uploaded
const isSubmitDisabled = computed(() => {
    return !Object.values(form.files).some(file => file); // disable if no file
});


function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}


const latestRequestPending = computed(() =>
    props.data.document_request?.slice(-1)[0]?.approval_status === 'pending'
);

const latestDocumentDeleteRequestPending = computed(() =>
    props.data.document_delete_request?.slice(-1)[0]?.approval_status === 'pending'
);

const form = useForm({
    files: {} // { [document_type_id]: File }
});

const deleteForm = useForm({
    document_id:''
})

const submit = () => {
    form.post(route("document.request", props.data), {
        onSuccess: () => {
            form.reset("files");
            showDialog.value = false; // close dialog
            $q.notify({
                type: 'positive',
                message: 'Document Request Submitted',
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
const approveRequest = (id) => {
    form.post(route('document.approve', id), {
        onStart: () => {
            $q.loading.show();
        },
        onFinish: () => {
            $q.loading.hide();
            showDialog.value = false; // close dialog
            form.reset();
            $q.notify({
                type: 'positive',
                message: 'Document Approved',
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
    form.post(route('document.reject', id), {
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


const deleteDocument = (doc) => {
    $q.dialog({
        title: 'Confirm',
        message: `Are you sure you want to delete "${doc.type?.name}"?`,
        cancel: true,
        persistent: true
    }).onOk(() => {
        form.delete(route('document.destroy', doc), {
            preserveScroll: true,
            onSuccess: () => {
                $q.notify({ type: 'positive', message: 'Document deleted successfully.' })
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
        })
    })
}
const requestDeleteDocument = (doc) => {
    if (doc.type?.name === 'MR Sheet/A-Roll') {
        $q.notify({
            type: 'negative',
            message: 'Delete request is not allowed for MR Sheet/A-Roll.'
        });
        return;
    }

    $q.dialog({
        title: 'Confirm Delete Request',
        message: `Do you want to request deletion of "${doc.type?.name}"?`,
        cancel: true,
        persistent: true
    }).onOk(() => {
        deleteForm.document_id = doc.id; // set the form field first

        deleteForm.post(route('document.requestDelete', props.data), {
            onStart: () => $q.loading.show(),
            onFinish: () => $q.loading.hide(),
            onSuccess: () => {
                $q.notify({
                    type: 'positive',
                    message: 'Delete request submitted successfully.'
                });
                deleteForm.reset();
            },
            onError: (errors) => {
                Object.values(errors).forEach((error) => {
                    $q.notify({ type: 'negative', message: error });
                });
            }
        });
    });
};

const approveDeleteRequest = (id) => {
    deleteForm.post(route('document.approveDelete', id), {
        onStart: () => $q.loading.show(),
        onFinish: () => $q.loading.hide(),
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: 'Delete request approved and document deleted.'
            });
        },
        onError: (errors) => {
            Object.values(errors).forEach((error) => {
                $q.notify({ type: 'negative', message: error });
            });
        }
    });
};

const rejectDeleteRequest = (id) => {
    deleteForm.post(route('document.rejectDelete', id), {
        onStart: () => $q.loading.show(),
        onFinish: () => $q.loading.hide(),
        onSuccess: () => {
            $q.notify({ type: 'info', message: 'Delete request rejected.' });
        },
    });
};

</script>

<style scoped>

</style>
