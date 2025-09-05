<script setup>
import { router } from "@inertiajs/vue3";
import useUtils from "@/Compositions/useUtils";

const {formatDate, formatDateTime} = useUtils();
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
    technical_qln: "Technical Qualification",
    name_of_workplace: "Workplace Name",
    post_per_qualification: "Post per Qualification",
    date_of_engagement: "Date of Engagement",
    skill_category: "Skill Category",
    skill_at_present: "Skill at Present",
};

const safeParse = (data) => {
    try {
        return data ? JSON.parse(data) : {};
    } catch {
        return {};
    }
};
</script>

<template>
    <q-card class="q-mt-md shadow-lg rounded-xl border border-gray-200">
        <!-- Title -->
        <q-card-section>
            <div class="stitle text-lg font-bold">Edit Requests</div>
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
                                      <span class="font-bold">
                                        {{ fieldLabels[key] ?? key }}:
                                      </span>
                                        <span class="text-gray-800">
                                              From:
                                            <span class="font-bold">

                                              {{
                                                    key === "date_of_birth"
                                                        ? formatDate(safeParse(req.previous_data)[key])
                                                        : safeParse(req.previous_data)[key] || "—"
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

                        <!-- Attachments -->
                        <div
                            v-if="req.attachments && req.attachments.length > 0"
                            class="mt-6"
                        >
                            <b class="block mb-3 text-gray-700">Attached Documents</b>
                            <div class="space-y-2">
                                <div
                                    v-for="file in req.attachments"
                                    :key="file.id"
                                    class="flex items-center justify-between p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition"
                                >
                                    <div class="flex items-center space-x-2 text-gray-700">
                                        <q-icon name="attach_file" size="16px" class="text-gray-500" />
                                        <span>
                                            <b>{{ file.type?.name || "Unknown Type" }}</b>
                                        </span>
                                    </div>

                                    <div class="flex space-x-1">
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

                                        <q-btn
                                            flat
                                            dense
                                            round
                                            color="primary"
                                            icon="download"
                                            :href="`/storage/${file.path}`"
                                            download
                                        >
                                            <q-tooltip>Download</q-tooltip>
                                        </q-btn>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-card-section>

                    <!-- Actions (only pending) -->
                    <q-card-actions
                        align="right"
                        v-if="req.approval_status === 'pending'"
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
    </q-card>

</template>



<style scoped></style>
