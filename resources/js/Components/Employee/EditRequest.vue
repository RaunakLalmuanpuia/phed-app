<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps(['data']);

// Approve request
const approveRequest = (id) => {
    router.post(route("edit.approve", id));
};

// Reject request
const rejectRequest = (id) => {
    router.post(route("edit.reject", id));
};

// Helper to safely parse JSON
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
        <q-card-section>
            <div class="stitle text-lg font-bold">Edit Requests</div>
        </q-card-section>

        <q-separator />

        <q-card-section>
            <div v-if="data.edit_requests.length === 0" class="text-gray-500">
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

                    <q-card-actions align="right">
                        <q-btn
                            label="Approve"
                            color="positive"
                            size="sm"
                            @click="approveRequest(req.id)"
                            :disable="req.approval_status !== 'pending'"
                        />
                        <q-btn
                            label="Reject"
                            color="negative"
                            size="sm"
                            @click="rejectRequest(req.id)"
                            :disable="req.approval_status !== 'pending'"
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </q-card-section>
    </q-card>
</template>

<style scoped></style>
