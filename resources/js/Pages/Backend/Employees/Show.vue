<template>
    <q-page class="container" padding>
        <div class="row q-col-gutter-md">
            <!-- Left Panel -->
            <div class="col-12 col-md-5">
                <q-card class="q-pa-md">
                    <div class="column items-center q-gutter-sm">
                        <q-avatar size="96px">
                            <q-img :src="`/storage/${data.avatar}`" />
                        </q-avatar>

                        <div class="stitle">{{ data.name }}</div>
                        <q-badge color="grey-4" text-color="dark" class="stitle" :label="data?.employment_type" />
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
                                    <q-item-section side class="subtitle">Education Qln.:</q-item-section>
                                    <q-item-section  class="text-label">{{data?.educational_qln}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="subtitle">Technical Qln.:</q-item-section>
                                    <q-item-section  class="text-label">{{data?.technical_qln}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="subtitle">Employment Type:</q-item-section>
                                    <q-item-section  class="text-label">{{data?.employment_type}}</q-item-section>
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
                                    <q-item-section side class="subtitle">Post Per Qualification:</q-item-section>
                                    <q-item-section  class="text-label">{{data?.post_per_qualification}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="subtitle">Date of Engagement:</q-item-section>
                                    <q-item-section  class="text-label">{{data?.date_of_engagement ?? 'N/A'}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="subtitle">Skill Category:</q-item-section>
                                    <q-item-section  class="text-label">{{data?.skill_category}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="subtitle">Skill at Present:</q-item-section>
                                    <q-item-section class="text-label">{{data?.skill_at_present}}</q-item-section>
                                </q-item>
                            </q-list>

                            <div class=" q-mt-lg flex items-center justify-center">
                                <q-btn
                                    label="Edit"
                                    @click="$inertia.get(route('employee.edit',data))"
                                    class="q-mr-md"
                                    color="primary"
                                />

                                <q-btn
                                    label="Delete"
                                    color="negative"
                                    unelevated
                                />
                            </div>

                        </div>
                    </div>
                </q-card>
            </div>

            <!-- Right Panel -->
            <div class="col-12 col-md-7">
                <!-- Tabs -->
                <q-tabs
                    v-model="tab"
                    class="text-primary bg-white shadow-1 q-pa-sm"
                    dense
                    align="center"
                >
                    <q-tab name="document" label="Documents" icon="drive_folder_upload" />
                    <q-tab name="edit" label="Edit Request" icon="edit"/>
                    <q-tab name="transfer_request" label="Transfer Request" icon="input" />
                    <q-tab name="deletion" label="Deletion Request" icon="delete_sweep" />
                </q-tabs>

                <Document v-if="tab === 'document'" :data="data" />

                <EditRequest v-if="tab === 'edit'" :data="data" />
                <TransferRequest v-if="tab === 'transfer_request'" :data="data" />
                <DeletionRequest v-if="tab === 'deletion'" :data="data" />




                <Transfer :data="data" :office="office"/>

            </div>

        </div>
    </q-page>
</template>

<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";

import Document from "@/Components/Employee/Document.vue";
import TransferRequest from "@/Components/Employee/TransferRequest.vue";
import EditRequest from "@/Components/Employee/EditRequest.vue";
import DeletionRequest from "@/Components/Employee/DeletionRequest.vue";

import Transfer from "@/Components/Employee/Transfer.vue";

import { ref, computed } from 'vue'

defineOptions({layout:BackendLayout})

const props=defineProps(['data','office']);

const tab = ref('document')


</script>
