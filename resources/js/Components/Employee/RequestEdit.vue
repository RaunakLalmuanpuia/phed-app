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
            <div v-if="data.edit_requests.length === 0" class=" text-center text-gray-500">
                No edit requests found.
            </div>

            <div v-else class="grid gap-3">
                <q-card
                    v-for="req in data.edit_requests"
                    :key="req.id"
                    class="shadow-md rounded-lg"
                >
                    <q-card-section>
                        <div class="text-sm">
                            <div><b>Status:</b> {{ req.approval_status }}</div>
                            <div><b>Requested On:</b> {{ req.request_date }}</div>
                            <div>
                                <b>Requested Changes:</b>
                                <div class="bg-gray-50 p-2 rounded text-xs border mt-1">
                                    <div
                                        v-for="(value, key) in parseChanges(req.requested_changes)"
                                        :key="key"
                                        class="flex justify-between border-b last:border-0 py-1"
                                    >
                                        <span class="font-medium">
                                            {{ fieldLabels[key] ?? key }}
                                        </span>
                                        <span>{{ value }}</span>
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
