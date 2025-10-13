<script setup>
import { ref, watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";
import {useQuasar} from "quasar";

const { formatDate, educationalQualifications } = useUtils();
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

const latestRequestPending = computed(() =>
    props.data.edit_requests?.slice(-1)[0]?.approval_status === 'pending'
);


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

const clearForm = () => {
    form.reset();
    form.clearErrors();
    form.documents = {}; // 🔹 explicitly clear uploaded files too
};

const hasChanges = computed(() => {
    return Object.values(form.requested_changes).some(v => v && v.trim?.() !== "");
});

const submitRequest = () => {
    if (!hasChanges.value) {
        $q.notify({
            type: "warning",
            message: "Please enter at least one change before submitting.",
        });
        return;
    }

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

// Helper to safely parse JSON
const parseChanges = (changes) => {
    try {
        return JSON.parse(changes);
    } catch (e) {
        return {};
    }
};
</script>

<template>
    <q-card class="q-mt-md shadow-lg rounded-xl border border-gray-200">
        <!-- Title -->
        <q-card-section class="flex items-center justify-between">
            <div class="stitle text-lg font-bold">Request Edit</div>
            <q-btn label="Request Edit" color="primary"  :disable="latestRequestPending" @click="showDialog = true" />
        </q-card-section>


        <q-separator />

        <q-card-section>
            <!-- Empty state -->
            <div
                v-if="data.edit_requests.length === 0"
                class="text-grey-7 text-center q-pa-md italic"
            >
                No edit requests found.
            </div>

            <!-- Requests list -->
            <div v-else class="flex flex-col gap-6">
                <q-card
                    v-for="req in data.edit_requests"
                    :key="req.id"
                    class="rounded-xl border border-gray-100 hover:shadow-md transition duration-200"
                >
                    <q-card-section>
                        <!-- Header Info -->
                        <div class="flex flex-wrap justify-between items-center mb-4">
                            <div class="">
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

                        <!-- Requested Changes -->
                        <div>
                            <b class="block mb-3 text-gray-700">Requested Changes</b>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4  p-4 rounded-lg"
                            >
                                <div
                                    v-for="(value, key) in parseChanges(req.requested_changes)"
                                    :key="key"
                                >
                                    <div class="flex flex-col">
                                      <span class="text-gray-500">
                                        {{ fieldLabels[key] ?? key }}:
                                      </span>
                                        <span class="text-gray-800">
                                              From:
                                            <span class="font-bold">

                                              {{
                                                    key === "date_of_birth"
                                                        ? formatDate(parseChanges(req.previous_data)[key])
                                                        : parseChanges(req.previous_data)[key] || "—"
                                                }}
                                            </span>
                                           To:
                                            <span class="font-bold">

                                              {{
                                                    key === "date_of_birth" ||
                                                    key === "date_of_engagement"
                                                        ? formatDate(value)
                                                        : value
                                                }}
                                            </span>
                                         </span>
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
        <q-card
            class="q-pa-md"
            style="width: 95vw; max-width: 800px; max-height: 90vh; overflow-y: auto;"
        >
            <!-- Header -->
            <q-card-section>
                <div class="text-h6 text-weight-bold text-primary">Request Edit</div>
            </q-card-section>

            <!-- Fields -->
            <q-card-section class="q-gutter-md">
                <div
                    v-for="(label, field) in fieldLabels"
                    :key="field"
                    class="q-mb-lg"
                >
                    <!-- Responsive Grid -->
                    <div
                        class="grid gap-4 items-start relative"
                        :class="{
            'grid-cols-2': $q.screen.gt.sm,   // 2 columns on tablet & desktop
            'grid-cols-1': !$q.screen.gt.sm, // 1 column on mobile
          }"
                    >
                        <!-- Existing Value -->
                        <div>
                            <div class="text-sm text-weight-bold q-mb-xs">
                                Existing {{ label }}:
                            </div>
                            <div class="q-mt-sm text-body2">
                                {{
                                    field === "date_of_birth"
                                        ? formatDate(props.data[field])
                                        : props.data[field] || "—"
                                }}
                            </div>
                        </div>

                        <!-- Vertical Divider (only for large screens) -->
                        <div
                            v-if="$q.screen.gt.sm"
                            class="absolute top-0 bottom-0 left-1/2 w-px bg-grey-4"
                        ></div>

                        <!-- New Input -->
                        <div>
                            <div class="text-sm text-weight-bold q-mb-xs">
                                New {{ label }}:
                            </div>

                            <q-input
                                v-if="field === 'date_of_birth'"
                                v-model="form.requested_changes[field]"
                                type="date"
                                outlined
                                dense
                            />

                            <q-select
                                v-else-if="field === 'educational_qln'"
                                v-model="form.requested_changes[field]"
                                :options="educationalQualifications"
                                label="Select Qualification"
                                outlined
                                dense
                                emit-value
                                map-options
                            />

                            <q-input
                                v-else
                                v-model="form.requested_changes[field]"
                                outlined
                                dense
                            />

                            <!-- Required Documents -->
                            <div
                                v-if="form.requested_changes[field]"
                                class="q-mt-md q-pa-sm bg-grey-1 rounded-borders"
                            >
                                <div class="text-sm text-weight-bold q-mb-sm">
                                    Required Documents for {{ label }}:
                                </div>

                                <div
                                    v-for="docName in fieldToDocuments[field]"
                                    :key="docName"
                                    class="q-mb-sm"
                                >
                                    <q-file
                                        filled
                                        v-model="form.documents[docName]"
                                        :label="docName"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        clearable
                                        dense
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separator -->
                    <q-separator spaced class="q-my-md" />
                </div>
            </q-card-section>

            <!-- Actions -->
            <q-card-actions align="right" class="q-pt-none">
                <q-btn flat label="Clear" color="negative" @click="clearForm" />
                <q-btn flat label="Cancel" color="grey" v-close-popup />
                <q-btn
                    label="Submit"
                    color="primary"
                    :loading="form.processing"
                    :disable="!hasChanges"
                    @click="submitRequest"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>


    <!--    <q-dialog v-model="showDialog" >-->
<!--        <q-card style="min-width: 700px">-->
<!--            <q-card-section>-->
<!--                <div class="text-lg font-bold">Request Edit</div>-->
<!--            </q-card-section>-->

<!--            <q-card-section>-->
<!--                &lt;!&ndash; Loop through editable fields &ndash;&gt;-->
<!--                <div v-for="(label, field) in fieldLabels" :key="field" class="mb-6">-->
<!--                    &lt;!&ndash; Grid: Left = Existing, Right = New Input &ndash;&gt;-->
<!--                    <div class="grid grid-cols-2 gap-4 items-start relative">-->
<!--                        &lt;!&ndash; Left: Existing Value &ndash;&gt;-->
<!--                        <div>-->
<!--                            <div class="text-sm font-bold mb-1">Existing {{ label }}:</div>-->
<!--                            <div class="q-mt-md">-->
<!--                                {{-->
<!--                                    field === "date_of_birth"-->
<!--                                        ? formatDate(props.data[field])-->
<!--                                        : props.data[field] || "—"-->
<!--                                }}-->
<!--                            </div>-->
<!--                        </div>-->

<!--                        &lt;!&ndash; Vertical Separator &ndash;&gt;-->
<!--                        <div class="absolute top-0 bottom-0 left-1/2 w-px bg-gray-300"></div>-->

<!--                        &lt;!&ndash; Right: New Input &ndash;&gt;-->
<!--                        <div>-->
<!--                            <div class="text-sm font-bold mb-1">New {{ label }}:</div>-->

<!--                            <q-input-->
<!--                                v-if="field === 'date_of_birth'"-->
<!--                                v-model="form.requested_changes[field]"-->
<!--                                type="date"-->
<!--                                outlined-->
<!--                                dense-->
<!--                            />-->

<!--                            &lt;!&ndash; Educational Qualification dropdown &ndash;&gt;-->
<!--                            <q-select-->
<!--                                v-else-if="field === 'educational_qln'"-->
<!--                                v-model="form.requested_changes[field]"-->
<!--                                :options="educationalQualifications"-->
<!--                                label="Select Qualification"-->
<!--                                outlined-->
<!--                                dense-->
<!--                                emit-value-->
<!--                                map-options-->
<!--                            />-->

<!--                            &lt;!&ndash; Default input &ndash;&gt;-->
<!--                            <q-input-->
<!--                                v-else-->
<!--                                v-model="form.requested_changes[field]"-->
<!--                                outlined-->
<!--                                dense-->
<!--                            />-->

<!--                            &lt;!&ndash; Required docs for this field &ndash;&gt;-->
<!--                            <div-->
<!--                                v-if="form.requested_changes[field]"-->
<!--                                class="border rounded p-4 bg-gray-50 q-mt-md"-->
<!--                            >-->
<!--                                <div class="text-sm font-bold mb-2">-->
<!--                                    Required Documents for {{ label }}:-->
<!--                                </div>-->

<!--                                <div v-for="docName in fieldToDocuments[field]" :key="docName" class="mb-3">-->
<!--                                    <q-file-->
<!--                                        filled-->
<!--                                        v-model="form.documents[docName]"-->
<!--                                        :label="docName"-->
<!--                                        accept=".pdf,.jpg,.jpeg,.png"-->
<!--                                        clearable-->
<!--                                        class="q-mt-md"-->
<!--                                    />-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    &lt;!&ndash; Separator between fields &ndash;&gt;-->
<!--                    <q-separator spaced class="q-my-md" />-->
<!--                </div>-->
<!--            </q-card-section>-->

<!--            <q-card-actions align="right">-->
<!--                <q-btn-->
<!--                    flat-->
<!--                    label="Clear"-->
<!--                    color="red"-->
<!--                    @click="clearForm"-->
<!--                />-->
<!--                <q-btn flat label="Cancel" v-close-popup />-->
<!--                <q-btn-->
<!--                    label="Submit"-->
<!--                    color="primary"-->
<!--                    :loading="form.processing"-->
<!--                    :disable="!hasChanges"-->
<!--                    @click="submitRequest"-->
<!--                />-->
<!--            </q-card-actions>-->
<!--        </q-card>-->
<!--    </q-dialog>-->

</template>
