<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";
import {useQuasar} from "quasar";

const { formatDate } = useUtils();
const props = defineProps(["data", "documentTypes"]);

const $q = useQuasar();
const showDialog = ref(false);

// inertia form
const form = useForm({
    requested_changes: {
        name: "",
        date_of_birth: "",
        parent_name: "",
        educational_qln: "",
        technical_qln: "",
    },
    documents: {}, // 🔹 hold uploaded files
});

watch(showDialog, (val) => {
    if (!val) {
        form.reset(); // reset values
        form.clearErrors(); // optional: clear validation errors too
    }
});

// field → required docs mapping
const fieldToDocuments = {
    date_of_birth: ["Birth Certificate", "Aadhar", "EPIC", "Educational Certificate"],
    name: ["Birth Certificate", "Aadhar", "EPIC", "Educational Certificate"],
    parent_name: ["Birth Certificate"],
    educational_qln: ["Educational Certificate"],
    technical_qln: ["Technical Certificate"],
};

const fieldLabels = {
    name: "Name",
    date_of_birth: "Date of Birth",
    parent_name: "Parent's Name",
    educational_qln: "Educational Qualification",
    technical_qln: "Technical Qualification",
};

const submitRequest = () => {
    const fd = new FormData();

    // add requested changes
    Object.entries(form.requested_changes).forEach(([k, v]) => {
        if (v) fd.append(`requested_changes[${k}]`, v);
    });

    // add files
    Object.entries(form.documents).forEach(([docName, files]) => {
        if (files) {
            [].concat(files).forEach((f) => {
                fd.append(`documents[${docName}][]`, f);
            });
        }
    });

    form.post(route("edit.request", props.data), {
        data: fd,
        forceFormData: true,
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
        onError: (errors) => {
            // Loop through errors and show notify
            Object.values(errors).forEach((errArr) => {
                if (Array.isArray(errArr)) {
                    errArr.forEach((errMsg) => {
                        $q.notify({
                            type: "negative",
                            message: errMsg,
                        });
                    });
                } else {
                    $q.notify({
                        type: "negative",
                        message: errArr,
                    });
                }
            });
        },
    });
};
</script>

<template>
    <q-card class="q-mt-md">
        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Request Edit</div>
            <q-btn label="Request Edit" color="primary" @click="showDialog = true" />
        </q-card-section>

        <q-separator />

        <!-- Previous Requests -->
        <q-card-section>
            <div
                v-if="data.edit_requests.length === 0"
                class="text-gray-500 text-center py-6"
            >
                No edit requests found.
            </div>

            <div v-else class="flex flex-col gap-4">
                <q-card
                    v-for="req in data.edit_requests"
                    :key="req.id"
                    class="shadow-md rounded-xl border border-gray-200 hover:shadow-lg transition"
                >
                    <q-card-section>
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <div>
                                <b>Status:</b>
                                <span
                                    :class="{
                                    'text-yellow-600': req.approval_status === 'pending',
                                    'text-green-600': req.approval_status === 'approved',
                                    'text-red-600': req.approval_status === 'rejected'
                                  }"
                                                >
                                  {{ req.approval_status }}
                                </span>
                            </div>
                            <div>
                                <b>Requested On:</b> {{ formatDate(req.request_date) }}
                            </div>
                        </div>

                        <div>
                            <b class="block mb-2">Requested Changes:</b>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 p-3 rounded-md"
                            >
                                <div
                                    v-for="(value, key) in JSON.parse(req.requested_changes)"
                                    :key="key"
                                    class="flex justify-between items-center"
                                >
                                    <q-item>
                                        <q-item-section side>
                                            {{ fieldLabels[key] ?? key }}:
                                        </q-item-section>
                                        <q-item-section>
                                            {{
                                                key === "date_of_birth"
                                                    ? formatDate(value)
                                                    : value
                                            }}
                                        </q-item-section>
                                    </q-item>
                                </div>

                            </div>
                        </div>
                        <div v-if="req.attachments && req.attachments.length > 0" class="mt-4">
                            <b class="block mb-2">Attached Documents:</b>
                            <div class="space-y-2">
                                <div
                                    v-for="file in req.attachments"
                                    :key="file.id"
                                    class="flex items-center justify-between p-2 border rounded-md"
                                >
                                    <div class="flex items-center space-x-2">
                                        <q-icon name="attach_file" size="sm" />
                                        <!-- Show Document Type Name + File Name -->
                                        <span>
                                          <b>{{ file.type?.name || 'Unknown Type' }}</b>

                                        </span>
                                    </div>

                                    <div class="flex space-x-2">
                                        <!-- Download -->
                                        <q-btn
                                            flat
                                            dense
                                            round
                                            color="primary"
                                            icon="download"
                                            :href="`/storage/${file.path}`"
                                            target="_blank"
                                        >
                                            <q-tooltip>Download</q-tooltip>
                                        </q-btn>

                                        <!-- View -->
                                        <q-btn
                                            flat
                                            dense
                                            round
                                            color="primary"
                                            icon="visibility"
                                            :href="`/storage/${file.path}`"
                                            target="_blank"
                                        >
                                            <q-tooltip>View</q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </q-card-section>
                </q-card>
            </div>
        </q-card-section>
    </q-card>

    <!-- Request Edit Dialog -->
    <q-dialog v-model="showDialog" persistent>
        <q-card style="min-width: 700px">
            <q-card-section>
                <div class="text-lg font-bold">Request Edit</div>
            </q-card-section>

            <q-card-section>
                <!-- Loop through editable fields -->
                <div v-for="(label, field) in fieldLabels" :key="field" class="mb-6">
                    <!-- Grid: Left = Existing, Right = New Input -->
                    <div class="grid grid-cols-2 gap-4 items-start relative">
                        <!-- Left: Existing Value -->
                        <div>
                            <div class="text-sm font-bold mb-1">Existing {{ label }}:</div>
                            <div class="q-mt-md">
                                {{
                                    field === "date_of_birth"
                                        ? formatDate(props.data[field])
                                        : props.data[field] || "—"
                                }}
                            </div>
                        </div>

                        <!-- Vertical Separator -->
                        <div class="absolute top-0 bottom-0 left-1/2 w-px bg-gray-300"></div>

                        <!-- Right: New Input -->
                        <div>
                            <div class="text-sm font-bold mb-1">New {{ label }}:</div>

                            <q-input
                                v-if="field !== 'date_of_birth'"
                                v-model="form.requested_changes[field]"
                                outlined
                                dense
                            />

                            <q-input
                                v-else
                                v-model="form.requested_changes[field]"
                                type="date"
                                outlined
                                dense
                            />

                            <!-- Required docs for this field -->
                            <div
                                v-if="form.requested_changes[field]"
                                class="border rounded p-4 bg-gray-50 q-mt-md"
                            >
                                <div class="text-sm font-bold mb-2">
                                    Required Documents for {{ label }}:
                                </div>

                                <div v-for="docName in fieldToDocuments[field]" :key="docName" class="mb-3">
                                    <q-file
                                        filled
                                        v-model="form.documents[docName]"
                                        :label="docName"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        clearable
                                        class="q-mt-md"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separator between fields -->
                    <q-separator spaced class="q-my-md" />
                </div>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat label="Cancel" v-close-popup />
                <q-btn
                    label="Submit"
                    color="primary"
                    :loading="form.processing"
                    @click="submitRequest"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>
