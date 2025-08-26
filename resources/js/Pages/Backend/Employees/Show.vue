<template>
    <q-page class="container" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Employee Detail</div>
                <q-breadcrumbs  class="text-dark">
                    <q-breadcrumbs-el @click="$inertia.get(route('dashboard'))" icon="dashboard" label="Dashboard"/>
                    <q-breadcrumbs-el label="All Employees" :to="route('mis.import')"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br>

        <q-card flat >
            <q-card-section>
                <div class="row q-col-gutter-md">
                    <!-- Left Panel -->
                    <div class="col-12 col-md-4">
                        <q-card class="q-pa-md">
                            <div class="column items-center q-gutter-sm">


                                <q-avatar size="96px">
                                    <q-img
                                        v-if="data?.avatar"
                                        :src="`/storage/${data?.avatar}`"
                                    />
                                    <q-icon v-else name="person"  color="primary" />
                                </q-avatar>

                                <div class="stitle">{{ data.name }}</div>
                                <q-badge
                                    :color="data?.employment_type?.trim() === 'Deleted' ? 'red-6' : 'grey-6'"
                                    text-color="white"
                                    class="stitle"
                                    :label="data?.employment_type?.trim() === 'MR'
                                        ? 'Muster Roll'
                                        : data?.employment_type?.trim() === 'PE'
                                          ? 'Provisional'
                                          : 'Deleted'"
                                    style="padding: 8px;"
                                    rounded
                                />
                                <div class="q-mt-lg full-width">

                                    <q-list  class="rounded-borders">
                                        <q-item>
                                            <q-item-section  class="text-label">Details</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Employee Code:</q-item-section>
                                            <q-item-section  class="text-label">{{ data?.employee_code }}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Name:</q-item-section>
                                            <q-item-section  class="text-label">{{ data?.name }}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Email:</q-item-section>
                                            <q-item-section  class="text-label">{{data.email}}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Contact:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.mobile}}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Parent Name:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.parent_name}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Date of Birth:</q-item-section>
                                            <q-item-section  class="text-label">{{data.date_of_birth}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Address:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.address ?? 'N/A'}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Education Qln.:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.educational_qln}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Technical Qln.:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.technical_qln}}</q-item-section>
                                        </q-item>


                                        <q-item>
                                            <q-item-section side class="subtitle" >Designation:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.designation}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Office:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.office?.name}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Office Type:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.office?.type}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Workplace:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.name_of_workplace}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Date of Engagement:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.date_of_engagement ?? 'N/A'}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR'">
                                            <q-item-section side class="subtitle">Post Per Qualification:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.post_per_qualification}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR'">
                                            <q-item-section side class="subtitle">Skill Category:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.skill_category}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR'">
                                            <q-item-section side class="subtitle">Skill at Present:</q-item-section>
                                            <q-item-section class="text-label">{{data?.skill_at_present}}</q-item-section>
                                        </q-item>
                                    </q-list>

                                    <div class=" q-mt-lg flex items-center justify-center">
                                        <q-btn
                                            v-if="canEdit"
                                            label="Edit"
                                            @click="$inertia.get(route('employee.edit',data))"
                                            class="q-mr-md"
                                            color="primary"
                                        />

                                        <q-btn
                                            v-if="canDelete && data.employment_type !== 'Deleted'"
                                            label="Delete"
                                            color="negative"
                                            unelevated
                                            @click="showDeleteDialog = true"
                                        />
                                    </div>

                                </div>
                            </div>
                        </q-card>
                    </div>

                    <DeletionDialog
                        v-model="showDeleteDialog"
                        :employee="data"
                    />

                    <!-- Right Panel -->
                    <div class="col-12 col-md-8">

                        <!-- Tabs -->
                        <q-tabs
                            v-model="tab"
                            class="text-primary bg-white shadow-1 q-pa-sm"
                            dense
                            align="center"
                        >
                            <q-tab name="document" label="Documents" icon="drive_folder_upload" />

                            <q-tab
                                v-if="isAdmin && canApproveEdit"
                                name="edit"
                                label="Edit Request"
                                icon="edit"
                                :alert="showEditAlert"
                                alert-icon="circle"
                                alert-color="red"
                                :class="showEditAlert ? 'text-red' : ''"
                            />

                            <q-tab
                                v-if="isAdmin && canApproveTransfer"
                                name="transfer_request"
                                label="Transfer Request"
                                icon="input"
                                :alert="showTransferAlert"
                                alert-icon="circle"
                                alert-color="red"
                                :class="showTransferAlert ? 'text-red' : ''"
                            />

                            <q-tab
                                v-if="isAdmin && canApproveDelete"
                                name="deletion"
                                label="Deletion Request"
                                icon="delete_sweep"
                                :alert="showDeletionAlert"
                                alert-icon="circle"
                                alert-color="red"
                                :class="showDeletionAlert ? 'text-red' : ''"
                            />

                            <q-tab
                                v-if="isManager && canRequestEdit"
                                name="request_edit"
                                label="Request Edit"
                                icon="edit"
                                :alert="showEditAlert"
                                alert-icon="circle"
                                alert-color="red"
                                :class="showEditAlert ? 'text-red' : ''"
                            />

                            <q-tab
                                v-if="isManager && canRequestTransfer"
                                name="request_transfer"
                                label="Request Transfer"
                                icon="input"
                                :alert="showTransferAlert"
                                alert-icon="circle"
                                alert-color="red"
                                :class="showTransferAlert ? 'text-red' : ''"
                            />

                            <q-tab
                                v-if="isManager && canRequestDelete"
                                name="request_deletion"
                                label="Request Deletion"
                                icon="delete_sweep"
                                :alert="showDeletionAlert"
                                alert-icon="circle"
                                alert-color="red"
                                :class="showDeletionAlert ? 'text-red' : ''"
                            />
                        </q-tabs>



                        <Document v-if="tab === 'document'" :data="data" />

                        <EditRequest v-if="tab === 'edit'" :data="data" />
                        <TransferRequest v-if="tab === 'transfer_request'" :data="data" :office="office" />
                        <DeletionRequest v-if="tab === 'deletion'" :data="data" />


                        <RequestEdit v-if="tab === 'request_edit'" :data="data"/>
                        <RequestTransfer v-if="tab ==='request_transfer'" :data="data" :office="office" />
                        <RequestDeletion v-if="tab === 'request_deletion'" :data="data" />




                        <div class="row q-col-gutter-md">
                            <div class="col-12 col-md-7">
                                <Remuneration v-if="data.remuneration_detail" :data="data" :canCreateRemuneration="canCreateRemuneration"/>
                                <Transfer :data="data" :office="office" :canCreateTransfer="canCreateTransfer" :canDeleteTransfer="canDeleteTransfer" />
                            </div>

                            <div class="col-12 col-md-5">
                                <EngagementCard v-if="data.employment_type === 'PE'"
                                    :data="data" :canCreateEngagementCard="canCreateEngagementCard" :canDownloadEngagementCard="canDownloadEngagementCard" />
                                <Deletion v-if="data.employment_type === 'Deleted'" :deletion="data.deletion_detail" :canEditDelete="canEditDelete"/>
                            </div>

                        </div>



                    </div>

                </div>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";

import Document from "@/Components/Employee/Document.vue";
import TransferRequest from "@/Components/Employee/TransferRequest.vue";
import EditRequest from "@/Components/Employee/EditRequest.vue";
import DeletionRequest from "@/Components/Employee/DeletionRequest.vue";

import RequestDeletion from "@/Components/Employee/RequestDeletion.vue";
import RequestEdit from "@/Components/Employee/RequestEdit.vue";
import RequestTransfer from "@/Components/Employee/RequestTransfer.vue";

import Transfer from "@/Components/Employee/Transfer.vue";

import DeletionDialog from "@/Components/Employee/DeletionDialog.vue";

import Deletion from "@/Components/Employee/Deletion.vue";
import Remuneration from "@/Components/Employee/Remuneration.vue";
import EngagementCard from "@/Components/Employee/EngagementCard.vue";


import { ref, computed } from 'vue'
import {usePage} from "@inertiajs/vue3";

defineOptions({layout:BackendLayout})

const props=defineProps(['data','office','canCreate','canEdit','canDelete','canCreateRemuneration','canCreateTransfer',
    'canDeleteTransfer','canCreateEngagementCard', 'canDownloadEngagementCard','canRequestEdit','canRequestDelete','canRequestTransfer',
    'canApproveEdit','canApproveTransfer','canApproveDelete','canEditDelete']);

const tab = ref('document')

const showDeleteDialog = ref(false)

const isAdmin = computed(() => !!usePage().props.roles?.find(item => item === 'Admin'));
const isManager = computed(() => !!usePage().props.roles?.find(item => item === 'Manager'));

// Latest requests (last element is latest)
const latestEditRequest = computed(() => {
    const arr = props.data.edit_requests;
    return arr && arr.length ? arr[arr.length - 1] : null;
});

const latestDeletionRequest = computed(() => {
    const arr = props.data.deletion_requests;
    return arr && arr.length ? arr[arr.length - 1] : null;
});

const latestTransferRequest = computed(() => {
    const arr = props.data.transfer_requests;
    return arr && arr.length ? arr[arr.length - 1] : null;
});
// Helper to check if approval_date is within last 15 days
function isRecent(approvalDate) {
    if (!approvalDate) return true; // no date, treat as recent
    const today = new Date();
    const approved = new Date(approvalDate);
    const diffDays = (today - approved) / (1000 * 60 * 60 * 24);
    return diffDays <= 15;
}

// Alert logic
const showEditAlert = computed(() => {
    if (!latestEditRequest.value) return false;
    if (isAdmin.value) {
        return latestEditRequest.value.approval_status === 'pending';
    } else if (isManager.value) {
        return latestEditRequest.value.approval_status !== 'pending' &&
            isRecent(latestEditRequest.value.approval_date);
    }
    return false;
});

const showDeletionAlert = computed(() => {
    if (!latestDeletionRequest.value) return false;
    if (isAdmin.value) {
        return latestDeletionRequest.value.approval_status === 'pending';
    } else if (isManager.value) {
        return latestDeletionRequest.value.approval_status !== 'pending' &&
            isRecent(latestDeletionRequest.value.approval_date);
    }
    return false;
});

const showTransferAlert = computed(() => {
    if (!latestTransferRequest.value) return false;
    if (isAdmin.value) {
        return latestTransferRequest.value.approval_status === 'pending';
    } else if (isManager.value) {
        return latestTransferRequest.value.approval_status !== 'pending' &&
            isRecent(latestTransferRequest.value.approval_date);
    }
    return false;
});
</script>
