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
                    <q-card-actions align="right" v-if="req.approval_status === 'pending'">
                        <q-btn
                            label="Approve"
                            color="positive"
                            @click="approveRequest(req.id)"
                        />
                        <q-btn
                            label="Reject"
                            color="negative"
                            @click="rejectRequest(req.id)"
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </q-card-section>
    </q-card>
</template>



<style scoped></style>
