<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps(['data']);

const showDialog = ref(false);

// inertia form
const form = useForm({
    requested_changes: {
        name: "",
        date_of_birth: "",
        parent_name: "",
        employment_type: "",
        designation:  "",
        educational_qln: "",
        name_of_workplace: "",
        post_per_qualification: "",
        date_of_engagement: "",
        skill_category: "",
        skill_at_present: "",
    },
});

const type = [
    { label: 'MR', value: 'MR' },
    { label: 'PE', value: 'PE' },
]

const skills = [
    { label: 'Unskilled', value: 'Unskilled' },
    { label: 'Semi-Skilled', value: 'Semi-Skilled' },
    { label: 'Skilled-I', value: 'Skilled-I' },
    { label: 'Skilled-II', value: 'Skilled-II' },

]

const submitRequest = () => {
    form.post(route("edit.request", props.data), {
        onSuccess: () => {
            showDialog.value = false;
            form.reset();
        },
    });
};

const parseChanges = (changes) => {
    try {
        return JSON.parse(changes);
    } catch (e) {
        return {};
    }
};
const fieldLabels = {
    name: "Name",
    date_of_birth: "Date of Birth",
    parent_name: "Parent's Name",
    employment_type: "Employment Type",
    designation: "Designation",
    educational_qln: "Educational Qualification",
    name_of_workplace: "Workplace Name",
    post_per_qualification: "Post per Qualification",
    date_of_engagement: "Date of Engagement",
    skill_category: "Skill Category",
    skill_at_present: "Skill at Present",
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
            <!-- Empty state -->
            <div v-if="data.edit_requests.length === 0" class="text-gray-500 text-center py-6">
                No edit requests found.
            </div>

            <!-- Requests flex layout -->
            <div v-else class="flex flex-col gap-4">
                <q-card
                    v-for="req in data.edit_requests"
                    :key="req.id"
                    class="shadow-md rounded-xl border border-gray-200 hover:shadow-lg transition"
                >
                    <q-card-section>
                        <!-- Top meta info -->
                        <div class="flex flex-wrap justify-between items-center  mb-3">
                            <div>
                                <b class="text-label">Status:</b>
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
                            <div><b class="text-label">Requested On:</b> {{ req.request_date }}</div>
                        </div>

                        <!-- Requested Changes stacked horizontally -->
                        <div>
                            <b class="block mb-2 text-label">Requested Changes:</b>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2  p-3 rounded-md ">
                                <div
                                    v-for="(value, key) in parseChanges(req.requested_changes)"
                                    :key="key"
                                    class="flex justify-between items-center"
                                >
                                    <q-item>
                                        <q-item-section side class="subtitle"> {{ fieldLabels[key] ?? key }}:</q-item-section>
                                        <q-item-section  class="text-label">{{ value }}</q-item-section>
                                    </q-item>
                                </div>
                            </div>
                        </div>
                    </q-card-section>

                    <!-- Show buttons only when status is pending -->

                </q-card>
            </div>
        </q-card-section>
    </q-card>

    <!-- Request Edit Dialog -->
    <q-dialog v-model="showDialog" persistent>
        <q-card style="min-width: 400px">
            <q-card-section>
                <div class="text-lg font-bold">Request Edit</div>
            </q-card-section>

            <q-card-section>

                <q-input
                    v-model="form.requested_changes.name"
                    label="New Name"
                    outlined
                    dense
                />

                <q-input
                    v-model="form.requested_changes.date_of_birth"
                    label="New Date of Birth"
                    type="date"
                    outlined
                    dense
                    class="q-mt-md"
                />
                <q-input
                    v-model="form.requested_changes.parent_name"
                    label="New Parent Name"
                    outlined
                    dense
                    class="q-mt-md"
                />

                <q-input
                    v-model="form.requested_changes.educational_qln"
                    label="Education Qualification"
                    outlined
                    dense
                    class="q-mt-md"
                />
                <q-input
                    v-model="form.requested_changes.designation"
                    label="New Designation"
                    outlined
                    dense
                    class="q-mt-md"
                />
                <q-input
                    v-model="form.requested_changes.date_of_engagement"
                    label="New Date of Engagement"
                    type="date"
                    outlined
                    dense
                    class="q-mt-md"
                />
                <q-select
                    v-model="form.requested_changes.employment_type"
                    label="New Employment Type"
                    outlined
                    dense
                    class="q-mt-md"
                    :options="type"
                    emit-value
                    map-options
                />

                <q-input
                    v-if="form.requested_changes.employment_type === 'MR' || data.employment_type === 'MR' "
                    v-model="form.requested_changes.post_per_qualification"
                    label="New Post per Qualification"
                    outlined
                    dense
                    class="q-mt-md"
                />


                <q-select
                    v-if="form.requested_changes.employment_type === 'MR' || data.employment_type === 'MR' "
                    v-model="form.requested_changes.skill_category"
                    label="New Skill Category"
                    outlined
                    dense
                    class="q-mt-md"
                    :options="skills"
                    emit-value
                    map-options
                />
                <q-select
                    v-if="form.requested_changes.employment_type === 'MR' || data.employment_type === 'MR' "
                    v-model="form.requested_changes.skill_at_present"
                    label="New Skill at Present"
                    outlined
                    dense
                    class="q-mt-md"
                    :options="skills"
                    emit-value
                    map-options
                />

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
